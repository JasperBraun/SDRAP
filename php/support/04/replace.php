<?php

// 'is_replacable' determines whether or not an HSP covers a match
// sufficiently to replace it;
function is_replacable ( array $hsp, array $match, $PRE_MATCH_MIN_COVERAGE_ADDITION ) {

  $overlap = min( 0, min($hsp['prod_end'], $match['prod_end']) - max($hsp['prod_start'], $match['prod_start']) + 1 );
  $match_prod_len = $match['prod_end'] - $match['prod_start'] + 1;

  // match is replaceable if it covers less that $min_cov_add base pairs
  // on the product sequence which are not covered by the hsp
  if ( $match_prod_len - $overlap < $PRE_MATCH_MIN_COVERAGE_ADDITION ) {

    return true;

  }

  // match covers at least $min_cov_add base pairs on the product
  // sequence which are not covered by the hsp
  return false;

}

// replaces matches by the given hsp where appropriate
function replace( array $merged_hsp, array &$pre_match_array, $PRE_MATCH_MIN_COVERAGE_ADDITION ) {

  $delete_match_keys = array();
  foreach ( $pre_match_array as $key => $match ) {

    if ( is_replacable( $merged_hsp, $match, $PRE_MATCH_MIN_COVERAGE_ADDITION ) ) {

      // remember to delete the match, since merged_hsp will replace it
      $delete_match_keys[] = $key;

    }

  }

  // delete each match which was replaced by merged_hsp
  foreach ( $delete_match_keys as $key ) {

    unset( $pre_match_array[ $key ] );

  }

  return true;

}

?>