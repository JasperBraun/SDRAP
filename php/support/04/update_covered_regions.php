<?php

// places merged_hsp in covered regions and updates the regions
// regions are always disjoint and sorted by start coordinate
function update_covered_regions( array &$covered_regions, array $merged_hsp ) {

  if ( sizeof( $covered_regions ) === 0 ) {

    $covered_regions[] = array(
      "start" => $merged_hsp['prod_start'],
      "end" => $merged_hsp['prod_end']
    );

  } else {

    $new_covered_regions = array();
    $new_region = array(
      "start" => $merged_hsp['prod_start'],
      "end" => $merged_hsp['prod_end']
    );
    $was_placed = false;
    foreach ( $covered_regions as $region ) {

      if ( $region['end'] < $merged_hsp['prod_start'] - 1 ) {

        // region occurs before hsp's region, add region and proceed with next
        $new_covered_regions[] = $region;

      } else if ( $merged_hsp['prod_end'] < $region['start'] - 1 ) {

        // region occurs after hsp's region; add hsp's region, if not
        // done before and then add region and then proceed with next
        if ( !$was_placed ) {
          $new_covered_regions[] = $new_region;
          $was_placed = true;
        }
        $new_covered_regions[] = $region;
        
      } else {

        // region and hsp's region are adjacent or overlapping, merge
        $new_region['start'] = min( $merged_hsp['prod_start'], $region['start'] );
        $new_region['end'] = max( $merged_hsp['prod_end'], $region['end'] );

      }

    }

    // this triggers if none of the regions occur after hsp's region
    if ( !$was_placed ) {
      $new_covered_regions[] = $new_region;
    }

    $covered_regions = $new_covered_regions;

  }

  return true;

}

?>