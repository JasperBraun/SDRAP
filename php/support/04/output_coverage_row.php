<?php

// load helper functions
require_once( __DIR__ . "/../write_table_row_into_tsv.php" );

// output entry for `coverage` table
function output_coverage_row( $covered, array $inter_tel_interval, $prec_nuc_id, $prod_nuc_id, array &$cov_aux, array &$max_id, array &$num_files, $COVERAGE_FILE_PATH, $MAX_NUM_TABLE_FILE_ROWS ) {
  
  // determine coverage
  $tel_length = $inter_tel_interval['end'] - $inter_tel_interval['start'] + 1;
  $coverage = round( 100 * ($covered / $tel_length), 2);

  // build `coverage` table row
  $max_id['cov'] += 1;
  $coverage_row = array(
    $max_id['cov'], // `cov_id`
    $prec_nuc_id, // `prec_nuc_id`
    $prod_nuc_id, // `prod_nuc_id`
    $coverage // `coverage`
  );

  write_table_row_into_tsv( $coverage_row, $cov_aux['num_row'], $cov_aux['file'], $COVERAGE_FILE_PATH, $num_files['cov'], $MAX_NUM_TABLE_FILE_ROWS );

  return true;

}

?>