<?php

// load helper functions
require_once( __DIR__ . "/process_sequence_pair.php" );
require_once( __DIR__ . "/../upload_tsv_to_table.php" );

// computes and uploads contents of `properties` table
function compute_properties( array $INPUT, array &$ERRORS, $LINK, array $DIRECTORIES,
    $MAX_NUM_TABLE_FILE_ROWS ) {

  // get pairs of precursor and product sequences where the product sequence is sufficiently
  // covered by the precursor
  $sequence_pairs_table = mysqli_query( $LINK,
      "SELECT M.`prec_nuc_id`, M.`prod_nuc_id` AS `prod_nuc_id`, MAX(ABS(M.`index`)) AS `max_index`,
          G.`prod_nuc_id` AS `has_gap`
      FROM `match` AS M
      LEFT JOIN (SELECT DISTINCT `prec_nuc_id`, `prod_nuc_id` FROM `gap`) AS G
      ON M.`prec_nuc_id` = G.`prec_nuc_id`
      AND M.`prod_nuc_id` = G.`prod_nuc_id`
      LEFT JOIN `coverage` AS C
      ON M.`prec_nuc_id` = C.`prec_nuc_id`
      AND M.`prod_nuc_id` = C.`prod_nuc_id`
      WHERE M.`is_preliminary` = 1
      AND C.`coverage` >= '{$INPUT['PROPERTY_MIN_COVERAGE']}'
      GROUP BY M.`prec_nuc_id`, M.`prod_nuc_id`;"
  );
  if ( $sequence_pairs_table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
        " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // keep track of table id's, numbers of files and row numbers of current
  // files while outputting contents `properties` table
  $output_aux = array(
      "prop_id" => 0, "num_prop_files" => 0, "num_prop_row" => 0, "prop_file" => NULL
  );

  // compute and output properties for each sequence pair one-by-one
  while ( $pair = mysqli_fetch_assoc( $sequence_pairs_table ) ) {

    $process_sequence_pair_result = process_sequence_pair( $pair, $output_aux, $INPUT, $ERRORS,
        $LINK, $DIRECTORIES, $MAX_NUM_TABLE_FILE_ROWS );
    if ( $process_sequence_pair_result === false ) {
      $ERRORS['other'][] = "Error while computing properties of sequence pair in " .
          basename(__FILE__,".php") . " near line " . __LINE__ . ".";
      return false;
    }

  }

  // upload properties into `properties` table
  $column_names = array(
      "prop_id", "prec_nuc_id", "prod_nuc_id",
      "preliminary_match_number", "total_match_number",
      "non_gapped", "non_overlapping", "non_repeating", "exceeded_clique_limit",
      "weakly_complete", "strongly_complete",
      "weakly_consecutive", "strongly_consecutive",
      "weakly_ordered", "strongly_ordered",
      "weakly_non_scrambled", "strongly_non_scrambled"
  );
  $bulk_upload_result = upload_tsv_to_table( "properties", $column_names,
    $DIRECTORIES['PROPERTIES_FILE_PATH'], $output_aux['num_prop_files'], true,
    $LINK, $ERRORS );
  if ( $bulk_upload_result === false ) {
    $ERRORS['other'][] = "Error while uploading preliminary matches in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  return true;

}

?>