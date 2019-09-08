<?php

// load helper functions
require_once( __DIR__ . "/../write_table_row_into_tsv.php" );

// compute and output row for `pointer` table, if any
// assumes $last_match['prod_start'] <= $match['prod_start']
// achieved by previous sorting and $last_match['prod_end'] <= $match['prod_end']
function process_pointer( array $match, array $last_match, $prec_nuc_id, $prec_alias, $prod_nuc_id,
    $prod_alias, $OUTPUT_USE_ALIAS, array &$ptr_aux, array &$max_id, array &$num_files,
    array $match_aux, $POINTER_MIN_LENGTH, $POINTER_FILE_PATH, $MAX_NUM_TABLE_FILE_ROWS ) {

  $pointer_length = $last_match['prod_end'] - $match['prod_start'] + 1;
  if ( $pointer_length >= $POINTER_MIN_LENGTH ) {

    // determine coordinates in precursor
    if ( $last_match['orientation'] === '+' ) {
      $left_prec_start = $last_match['prec_end'] - $pointer_length + 1;
      $left_prec_end = $last_match['prec_end'];
    } else {
      $left_prec_start = $last_match['prec_start'];
      $left_prec_end = $last_match['prec_start'] + $pointer_length - 1;
    }
    if ( $match['orientation'] === '+' ) {
      $right_prec_start = $match['prec_start'];
      $right_prec_end = $match['prec_start'] + $pointer_length - 1;
    } else {
      $right_prec_start = $match['prec_end'] - $pointer_length + 1;
      $right_prec_end = $match['prec_end'];
    }

    // determine aliases
    $prec_id = $OUTPUT_USE_ALIAS === true ? $prec_nuc_id : $prec_alias;
    $prod_id = $OUTPUT_USE_ALIAS === true ? $prod_nuc_id : $prod_alias;
    // ptr_[prec_id]
    // _[left-flanking-match-index]_[left-flanking-match-prec-ptr-start]_[left-flanking-match-prec-ptr-end]
    // _[right-flanking-match-index]_[right-flanking-match-prec-ptr-start]_[right-flanking-match-prec-ptr-end]
    $ptr_prod_alias = implode( "_", array_map( "strval",
        array( "ptr", $prec_id, $match_aux['index'] - 1, $left_prec_start, $left_prec_end,
            $match_aux['index'], $right_prec_start, $right_prec_end )
    ) );

    // ptr_[prod_id]_[prod-ptr-start]_[prod-ptr-end]
    // _[attached-match-index]_[other-match-index]
    // _[other-match-prec-ptr-start]_[other-match-prec-ptr-end]
    $prec_left_alias = implode( "_", array_map( "strval",
        array( "ptr", $prod_id, $match['prod_start'], $last_match['prod_end'],
            $match_aux['index'] - 1, $match_aux['index'], $right_prec_start, $right_prec_end )
    ) );
    $prec_right_alias = implode( "_", array_map( "strval",
        array( "ptr", $prod_id, $match['prod_start'], $last_match['prod_end'],
            $match_aux['index'], $match_aux['index'] - 1, $left_prec_start, $left_prec_end )
    ) );

    // build `pointer` table row
    $max_id['ptr'] += 1;
    $pointer_row = array(
      $max_id['ptr'], // `ptr_id`
      $prec_nuc_id, // `prec_nuc_id`
      $prod_nuc_id, // `prod_nuc_id`
      $max_id['match'] - 1, // `left_match_id` // above assumptions used here
      $max_id['match'], // `right_match_id` // above assumptions used here
      $match['prod_start'], // `prod_start`
      $last_match['prod_end'], // `prod_end`
      $left_prec_start, // `left_prec_start`
      $left_prec_end, // `left_prec_end`
      $last_match['orientation'], // `left_match_orientation`
      $right_prec_start, // `right_prec_start`
      $right_prec_end, // `right_prec_end`
      $match['orientation'], // `right_match_orientation`
      $pointer_length, // `length`
      1, // `is_preliminary`
      $ptr_prod_alias, // `prod_alias`
      $prec_left_alias, // `prec_left_alias`
      $prec_right_alias //`prec_right_alias`
    );

    write_table_row_into_tsv( $pointer_row, $ptr_aux['num_row'], $ptr_aux['file'], $POINTER_FILE_PATH, $num_files['ptr'], $MAX_NUM_TABLE_FILE_ROWS );

  }

  return true;

}

?>