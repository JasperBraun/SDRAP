<?php

// merges second array into first array avoiding repetitions
// behaves unexpectedly when mixed integer/string arrays due to integer type casting
// of string indices automatically done by php
function array_merge_unique( array &$array_a, array &$array_b ) {

  $exists = array();
  foreach ( $array_a as $x ) {
    $exists[$x] = true;
  }

  foreach ( $array_b as $x ) {
    if ( !isset( $exists[$x] ) ) {
      $array_a[] = $x;
    }
  }

  return true;

}

// merges two matches to a bigger match, but does not
// test whether they are suitable to be merged
function merged( array $match_a, array $match_b ) {

  $result = $match_a;

  // update match to include the regions covered by both matches and in between them
  array_merge_unique( $result['hsp_id'], $match_b['hsp_id'] );
  $result['prec_start'] = min( $match_a['prec_start'], $match_b['prec_start'] );
  $result['prec_end'] = max( $match_a['prec_end'], $match_b['prec_end'] );
  $result['prod_start'] = min( $match_a['prod_start'], $match_b['prod_start'] );
  $result['prod_end'] = max( $match_a['prod_end'], $match_b['prod_end'] );
  $result['length'] = $result['prod_end'] - $result['prod_start'] + 1;

  return $result;

}

// 'is mergeable' determines whether or not an match and an HSP which
// interacts with the match can be merged
function is_mergeable ( array $match_a, array $match_b, $MERGE_TOLERANCE, $MERGE_MAX_GAP ) {

  // return false if matches are too far apart on product or precursor
  // sequence and if the orientations of the matches disagree
  if ( max( $match_a['prod_start'], $match_b['prod_start'] ) -
       min( $match_a['prod_end'], $match_b['prod_end'] ) - 1 > $MERGE_MAX_GAP ||
       max( $match_a['prec_start'], $match_b['prec_start'] ) -
       min( $match_a['prec_end'], $match_b['prec_end'] ) - 1 > $MERGE_MAX_GAP ||
       $match_a['orientation'] !== $match_b['orientation'] ) {

    return false;

  }

  $orientation = $match_a['orientation'];

  $prod_left_diff = $match_a['prod_start'] - $match_b['prod_start'];
  $prod_right_diff = $match_a['prod_end'] - $match_b['prod_end'];

  if ( $orientation == '+' ) {

    $prec_left_diff = $match_a['prec_start'] - $match_b['prec_start'];
    $prec_right_diff = $match_a['prec_end'] - $match_b['prec_end'];

  } else {

    $prec_left_diff = $match_b['prec_end'] - $match_a['prec_end'];
    $prec_right_diff = $match_b['prec_start'] - $match_a['prec_start'];

  }

  $left_diff = $prod_left_diff - $prec_left_diff;
  $right_diff = $prod_right_diff - $prec_right_diff;

  if ( abs( $left_diff ) <= $MERGE_TOLERANCE && abs( $right_diff ) <= $MERGE_TOLERANCE ) {

    return true;

  }

  // diff at either end exceeded tolerance
  return false;

}

// merges the given hsp with all mergeable matches and replace those matches
function merge( array $hsp, array &$match_array, $MERGE_TOLERANCE, $MERGE_MAX_GAP ) {

  $merged_hsp = $hsp;
  $delete_match_keys = array();
  foreach ( $match_array as $key => $match ) {

    if ( is_mergeable( $match, $merged_hsp, $MERGE_TOLERANCE, $MERGE_MAX_GAP ) ) {

      // merge hsp with match
      $merged_hsp = merged( $merged_hsp, $match );

      // remember to delete the match, since merged_hsp will replace it
      $delete_match_keys[] = $key;

    }

  }

  // delete each match which was merged with merged_hsp
  foreach ( $delete_match_keys as $key ) {

    unset( $match_array[$key] );

  }

  return $merged_hsp;

}

?>