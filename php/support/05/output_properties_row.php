<?php

// load helper functions
require_once( __DIR__ . "/../write_table_row_into_tsv.php" );

// outputs row for `properties` table
function output_properties_row( array $properties, array $pair, $n, $non_overlapping,
    $non_repeating, $exceeded_clique_limit, array &$output_aux, $PROPERTIES_FILE_PATH,
    $MAX_NUM_TABLE_FILE_ROWS ) {

  // build `properties` table row
  $output_aux['prop_id'] += 1;
  $properties_row = array(
      $output_aux['prop_id'], // `prop_id`
      $pair['prec_nuc_id'], // `prec_nuc_id`
      $pair['prod_nuc_id'], // `prod_nuc_id`
      $pair['max_index'], // `preliminary_match_number`
      $n, // `total_match_number`
      $pair['has_gap'] === NULL ? 1 : 0, // `non_gapped`
      $non_overlapping === true ? 1 : 0, // `non_overlapping`
      $non_repeating === true ? 1 : 0, // `non_repeating`
      $exceeded_clique_limit === true ? 1 : 0, // `exceeded_max_arr_num`
      $properties['weakly_complete'] === true ? 1 : 0, // `weakly_complete`
      $properties['strongly_complete'] === true ? 1 : 0, // `strongly_complete`
      $properties['weakly_consecutive'] === true ? 1 : 0, // `weakly_consecutive`
      $properties['strongly_consecutive'] === true ? 1 : 0, // `strongly_consecutive`
      $properties['weakly_ordered'] === true ? 1 : 0, // `weakly_ordered`
      $properties['strongly_ordered'] === true ? 1 : 0, // `strongly_ordered`
      $properties['weakly_non_scrambled'] === true ? 1 : 0, // `weakly_non_scrambled`
      $properties['strongly_non_scrambled'] === true ? 1 : 0, // `strongly_non_scrambled`
  );

  write_table_row_into_tsv( $properties_row, $output_aux['num_prop_row'], $output_aux['prop_file'],
      $PROPERTIES_FILE_PATH, $output_aux['num_prop_files'], $MAX_NUM_TABLE_FILE_ROWS );

  return true;

}

?>