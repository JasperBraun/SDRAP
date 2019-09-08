<?php

// convers letters of a substring of an all capitalized string to lower case
function soft_mask( $string, $start, $length ) {

    return substr ( $string, 0, $start ) .
           strtolower ( substr ( $string, $start, $length ) ) .
           substr ( $string, $start + $length );
}

// converts nucleotide sequence into its reverse complement
function reverse_complement( $dna ) {

  return strrev ( strtr ( $dna, array (
    "A" => "T",
    "C" => "G",
    "G" => "C",
    "T" => "A",
    "N" => "N"
  ) ) );

}

// finds all cyclic permutations of the string
function find_cyclic_permutations( $string ) {

  $result = array();
  $len = strlen( $string );
  for ( $i = 0; $i < $len; $i++ ) {
    $result[] = substr( $string, $i ) . substr( $string, 0 , $i );
  }
  return $result;

}

function find_comparison_telomere( $pi, $telo_start, $TELO_MAX_LENGTH, $TELO_MAX_OFFSET ) {

  $permutation_len = strlen( $pi );
  $shift = $telo_start % $permutation_len;
  $fragment = substr( $pi, $permutation_len - $shift );
  $prefix = $fragment . str_pad( $pi, $telo_start + $permutation_len - strlen($fragment), $pi, STR_PAD_LEFT );
  $comparison_telomere = str_pad ( $prefix, $TELO_MAX_LENGTH + $TELO_MAX_OFFSET, $pi );

  return $comparison_telomere;

}

function expand_telomere( &$sequence, &$comparison_telomere, array $telo, $dir, $TELO_ERROR_LIMIT, $TELO_BUFFER_LIMIT, $TELO_MAX_LENGTH ) {

  // initialize variables
  $result = $telo;
  $tmp = $result;
  $old_dist = 0;
  $buffer = 0;

  while ( $tmp['length'] < $TELO_MAX_LENGTH && $buffer <= $TELO_BUFFER_LIMIT ) {

    if ( $dir === 'EXPAND_LEFT' && $tmp['start'] <= 0 ) { break; }

    // expand telomere temporarily by 1 bp
    $tmp['length'] += 1;
    if ( $dir === 'EXPAND_LEFT' ) {
      $tmp['start'] -= 1;
    }

    // check whether added bp increases the levenshtein distance
    $new_dist = levenshtein( substr( $sequence, $tmp['start'], $tmp['length'] ),
                             substr( $comparison_telomere, $tmp['start'], $tmp['length'] ) );
    $diff = $new_dist - $old_dist;

    // keep levenshtein distance relative to length below error_limit,
    // check whether added bp increased the levenshtein distance,
    // and increment/decrement buffer accoringly
    // make temporary telomere permanent if added bp results in 0 or less buffer
    if ( $new_dist / $tmp['length'] > $TELO_ERROR_LIMIT ) { // error_limit exceeded

      break;

    } elseif ( $diff > 0 ) { // distance increased, increment buffer

      $buffer += 1;

    } elseif ( $diff <= 0 ) { // distance didn't increase, decrement buffer

      $buffer -= 1;

      // if bp results in 0 or less buffer, make temporary telomere permanent
      if ( $buffer <= 0 ) {
        $result = $tmp;
        $buffer = 0;
      }

    }

    $old_dist = $new_dist;

  }

  return $result;

}

function compute_telomere( $sequence, $TELO_PATTERN, $TELO_ERROR_LIMIT, $TELO_BUFFER_LIMIT, $TELO_MAX_LENGTH, $TELO_MAX_OFFSET, $TELO_MIN_LENGTH  ) {

  ini_set( 'max_execution_time', 3600 );

  $telo = array( "start" => 0, "length" => 0 );
  $permutations = find_cyclic_permutations ( $TELO_PATTERN );

  foreach ( $permutations as $pi ) {

    $telo_start = stripos ( substr( $sequence, 0, $TELO_MAX_OFFSET + strlen($pi) ), $pi );

    if ( $telo_start !== false ) {

      $comparison_telomere = find_comparison_telomere( $pi, $telo_start, $TELO_MAX_LENGTH, $TELO_MAX_OFFSET );
      $telo = array( "start" => $telo_start, "length" => strlen($pi) );
      $telo = expand_telomere( $sequence, $comparison_telomere, $telo, 'EXPAND_LEFT', $TELO_ERROR_LIMIT, $TELO_BUFFER_LIMIT, $TELO_MAX_LENGTH );
      $telo = expand_telomere( $sequence, $comparison_telomere, $telo, 'EXPAND_RIGHT', $TELO_ERROR_LIMIT, $TELO_BUFFER_LIMIT, $TELO_MAX_LENGTH );

    }

    if ( $telo['length'] >= $TELO_MIN_LENGTH ) { break; }

  }

  if ( $telo['length'] < $TELO_MIN_LENGTH ) {
    $result = array( "start" => 0, "length" => 0 );
  } else {
    $result = $telo;
  }

  return $result;

}

function get_telomeres ( $INPUT, &$sequence_result ) {

  $telomeres = array();

  // obtain prefix and suffix from product sequence for telomere computation
  $prefix = substr(
    $sequence_result['sequence'],
    0,
    $INPUT['TELO_MAX_OFFSET'] + $INPUT['TELO_MAX_LENGTH']
  );

  $suffix = reverse_complement( substr(
    $sequence_result['sequence'],
    $sequence_result['length'] - $INPUT['TELO_MAX_OFFSET'] - $INPUT['TELO_MAX_LENGTH'],
    $INPUT['TELO_MAX_OFFSET'] + $INPUT['TELO_MAX_LENGTH']
  ) );

  $telomeres['left_telo'] = compute_telomere(
    $prefix,
    $INPUT['TELO_PATTERN'],
    $INPUT['TELO_ERROR_LIMIT'],
    $INPUT['TELO_BUFFER_LIMIT'],
    $INPUT['TELO_MAX_LENGTH'],
    $INPUT['TELO_MAX_OFFSET'],
    $INPUT['TELO_MIN_LENGTH']
  );

  $tmp_right_telo = compute_telomere(
    $suffix,
    $INPUT['TELO_PATTERN'],
    $INPUT['TELO_ERROR_LIMIT'],
    $INPUT['TELO_BUFFER_LIMIT'],
    $INPUT['TELO_MAX_LENGTH'],
    $INPUT['TELO_MAX_OFFSET'],
    $INPUT['TELO_MIN_LENGTH']
  );
  $telomeres['right_telo'] = array(
    "start" => $sequence_result['length'] - $tmp_right_telo['start'] - $tmp_right_telo['length'],
    "length" => $tmp_right_telo['length']
  );

  // mask telomeres, if any, in nucleotide sequence by converting to lower case letters 
  if ( $telomeres['left_telo']['length'] !== 0 ) {
    $sequence_result['sequence'] = soft_mask(
      $sequence_result['sequence'],
      $telomeres['left_telo']['start'],
      $telomeres['left_telo']['length']
    );
  }
  if ( $telomeres['right_telo']['length'] !== 0 ) {
    $sequence_result['sequence'] = soft_mask (
      $sequence_result['sequence'],
      $telomeres['right_telo']['start'],
      $telomeres['right_telo']['length']
    );
  }

  return $telomeres;

}

?>