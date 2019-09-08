<?php

require_once( __DIR__ . "/get_aliases.php" );
require_once( __DIR__ . "/compute_pre_annotations.php" );
require_once( __DIR__ . "/../upload_tsv_to_table.php" );
require_once( __DIR__ . "/compute_add_annotations.php" );

// annotates preliminary matches between all precursor and product sequences
function annotate( array $INPUT, array &$ERRORS, $LINK, array $DIRECTORIES, $MAX_NUM_TABLE_FILE_ROWS
    ) {

  $nuc_id_to_alias = array();
  $alias_result = get_aliases( $nuc_id_to_alias, $ERRORS, $LINK );
  if ( $alias_result === false ) {
    $ERRORS['other'][] = "Failure to get aliases " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  $prod_nuc_table = mysqli_query( $LINK,
    "SELECT DISTINCT T.`nuc_id`, T.`five_start`, T.`five_length`, T.`three_start`, T.`three_length`
     FROM `telomere` AS T
     LEFT JOIN `hsp` AS H
     ON T.`nuc_id` = H.`prod_nuc_id`
     WHERE H.`bitscore` >= '{$INPUT['PRE_MATCH_MIN_BITSCORE']}'
     AND H.`pident` >= '{$INPUT['PRE_MATCH_MIN_PIDENT']}';"
  );
  if ( $prod_nuc_table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }
  if ( mysqli_num_rows($prod_nuc_table) === 0 ) {
    $ERRORS['other'][] = "No product sequences found for annotation in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // annotate one product sequence at a time
  $max_id = array( "match" => 0, "gap" => 0, "ptr" => 0, "cov" => 0 );
  $num_files = array( "match" => 0, "gap" => 0, "ptr" => 0, "cov" => 0 );
  $num_match_row = 0;
  $prec_nuc_id_array = array();
  while ( $prod = mysqli_fetch_assoc( $prod_nuc_table ) ) {

    $inter_tel_interval = array(
      "start" => max( 1, $prod['five_start'] + $prod['five_length'] ),
      "end" => $prod['three_start'] - 1
    );
    $prod_nuc_id = $prod['nuc_id'];

    // compute preliminary matches, gaps, pointers, and coverages of product for all matching
    // precursors
    $compute_pre_annotations_result = compute_pre_annotations( $inter_tel_interval, $prod_nuc_id,
        $nuc_id_to_alias, $prec_nuc_id_array, $max_id, $num_files, $num_match_row, $INPUT, $LINK,
        $ERRORS, $DIRECTORIES, $MAX_NUM_TABLE_FILE_ROWS );
    if ( $compute_pre_annotations_result === false ) {
      $ERRORS['other'][] = "Error during preliminary match, gap, pointer, and coverage computation in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
      return false;
    }

    // compute additional matches, and fragments of product for all matching precursors
    $compute_add_annotations_result = compute_add_annotations( $inter_tel_interval, $prod_nuc_id,
        $nuc_id_to_alias, $max_id, $num_files, $num_match_row, $INPUT, $LINK, $ERRORS, $DIRECTORIES,
        $MAX_NUM_TABLE_FILE_ROWS );
    if ( $compute_add_annotations_result === false ) {
      $ERRORS['other'][] = "Error during additional match, and fragment computation in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
      return false;
    }

    foreach ( $prec_nuc_id_array as $prec_nuc_id ) {
      unlink( $DIRECTORIES['TMP_MATCH_FILE_PATH'] . $prec_nuc_id . ".tsv" );
    }
    $prec_nuc_id_array = array();

  }
  mysqli_free_result( $prod_nuc_table );
  unset( $nuc_id_to_alias );

  // upload matches into `match` table
  $column_names = array(
    "match_id", "prec_nuc_id", "prod_nuc_id",
    "prec_start", "prec_end", "prod_start", "prod_end",
    "orientation", "length", "hsp_id", "index",
    "pre_cov", "add_cov", "is_preliminary", "is_additional", "is_fragment",
    "prec_segment_alias", "prod_segment_alias"
  );
  $bulk_upload_result = upload_tsv_to_table( "match", $column_names,
    $DIRECTORIES['MATCH_FILE_PATH'], $num_files['match'], true,
    $LINK, $ERRORS );
  if ( $bulk_upload_result === false ) {
    $ERRORS['other'][] = "Error while uploading preliminary matches in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    false;
  }

  // upload contents of `gap` table
  $column_names = array(
    "gap_id", "prec_nuc_id", "prod_nuc_id",
    "index", "start", "end", "length",
    "is_terminal", "gap_alias"
  );
  $bulk_upload_result = upload_tsv_to_table( "gap", $column_names,
    $DIRECTORIES['GAP_FILE_PATH'], $num_files['gap'], true,
    $LINK, $ERRORS );
  if ( $bulk_upload_result === false ) {
    $ERRORS['other'][] = "Error while uploading gaps in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // upload contents of `pointer` table
  $column_names = array(
    "ptr_id", "prec_nuc_id", "prod_nuc_id",
    "left_match_id", "right_match_id", "prod_start", "prod_end",
    "left_prec_start", "left_prec_end", "left_match_orientation",
    "right_prec_start", "right_prec_end", "right_match_orientation",
    "length", "is_preliminary",
    "prod_alias", "prec_left_alias", "prec_right_alias"
  );
  $bulk_upload_result = upload_tsv_to_table( "pointer", $column_names,
    $DIRECTORIES['POINTER_FILE_PATH'], $num_files['ptr'], true,
    $LINK, $ERRORS );
  if ( $bulk_upload_result === false ) {
    $ERRORS['other'][] = "Error while uploading pointers in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // upload contents of `coverage` table
  $column_names = array( "cov_id", "prec_nuc_id", "prod_nuc_id", "coverage" );
  $bulk_upload_result = upload_tsv_to_table( "coverage", $column_names,
    $DIRECTORIES['COVERAGE_FILE_PATH'], $num_files['cov'], true,
    $LINK, $ERRORS );
  if ( $bulk_upload_result === false ) {
    $ERRORS['other'][] = "Error while uploading coverages in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  return true;

}

?>