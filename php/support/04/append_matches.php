<?php

// checks which preliminary matches the merged hsp overlaps with sufficiently
// adds a match or fragment to add_match_array for each preliminary match the
// merged hsp overlaps with sufficiently; pre_match_array is assumed to be sorted
// by starting coordinate on product sequence in ascending order
function append_matches( array $merged_hsp, array &$pre_match_array, array &$add_match_array, $ADD_MATCH_MIN_PROD_SEGMENT_OVERLAP, $FRAGMENT_MIN_PROD_SEGMENT_OVERLAP ) {

  $was_placed = false;

  foreach ( $pre_match_array as $match ) {

    if ( $merged_hsp['prod_end'] < $match['prod_start'] ) { break; } // sorting assumption used here

    $overlap = min( $merged_hsp['prod_end'], $match['prod_end'] ) -
               max( $merged_hsp['prod_start'], $match['prod_start'] ) + 1;

    if ( $overlap >= $FRAGMENT_MIN_PROD_SEGMENT_OVERLAP * $match['length'] ) {

      $is_frag = $overlap < $ADD_MATCH_MIN_PROD_SEGMENT_OVERLAP * $match['length'];

      // prepare additional match, or fragment, and add to add_match_array
      $new_match = $merged_hsp;
      $new_match['index'] = $match['index'];
      $new_match['pre_cov'] = round( 100 * ( $overlap / $match['length'] ), 2 );
      $new_match['add_cov'] = round( 100 * ( $overlap / $merged_hsp['length'] ), 2 );
      $new_match['is_fragment'] = $is_frag;
      $add_match_array[] = $new_match;
      $was_placed = true;

    }

  }

  // if hsp did not overlap sufficiently with any preliminary matches
  // the hsp may sufficiently overlap after merging with subsequent hsp's, or
  // fragment with index 0, will be appended for transparency
  if ( !$was_placed ) {

    // prepare additional match, or fragment, and add to add_match_array
    $new_match = $merged_hsp;
    $new_match['index'] = 0;
    $new_match['pre_cov'] = 0.00;
    $new_match['add_cov'] = 0.00;
    $new_match['is_fragment'] = true;
    $add_match_array[] = $new_match;

  }

  return true;

}

?>