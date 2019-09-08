<?php

// computes the number of base pairs in product covered by given
// hsp, but none of the matches in given array of matches
// $covered_regions must consist of disjoint regions covered by
// matches and sorted by start coordinate
function get_coverage( array $hsp, array &$pre_match_array, $PRE_MATCH_MIN_COVERAGE_ADDITION ) {

  $cov = $hsp['length'];
  foreach ( $pre_match_array as $match ) {

    $overlap = max( 0,
        min( $hsp['prod_end'], $match['prod_end'] ) -
        max( $hsp['prod_start'], $match['prod_start'] ) + 1 );
    $cov -= $overlap;
    
    if ( $cov < $PRE_MATCH_MIN_COVERAGE_ADDITION ) { break; }

  }

  return $cov;

}

?>