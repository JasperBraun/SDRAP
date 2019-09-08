<?php

// load helper functions
require_once( __DIR__ . "/set_sequence_file_paths.php" );
require_once( __DIR__ . "/read_input_sequence_file.php" );
require_once( __DIR__ . "/../upload_tsv_to_table.php" );

// reads in input sequence, computes telomeres if product sequences,
// populates `nucleotide` and `telomere` tables and outputs
// outputs sequence files for blast with `nuc_id` as description lines
function process_sequences( array $INPUT, array &$ERRORS, $LINK, array $DIRECTORIES, $MAX_NUM_TABLE_FILE_ROWS, $nucleus, $max_nuc_id, $max_alias_id ) {

  $result = array(
    "success" => true,
    "max_nuc_id" => $max_nuc_id,
    "max_alias_id" => $max_alias_id
  );

  // set file paths
  $file_paths = array();
  set_sequence_file_paths( $DIRECTORIES, $nucleus, $file_paths );

  // open input sequence file
  $input_sequence_file = fopen( $file_paths['input_sequence_file_name'], "r" );
  if ( $input_sequence_file === false ) {
    $ERRORS['other'][] = "Could not open file " . $file_paths['input_sequence_file_name'] . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    $result['success'] = false;
    return $result;
  }

  // create and open blast sequence file
  $blast_sequence_file = fopen( $file_paths['blast_sequence_file_name'], "w" );
  if ( $blast_sequence_file === false ) {
    $ERRORS['other'][] = "Could not open file " . $file_paths['blast_sequence_file_name'] . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    $result['success'] = false;
    return $result;
  }

  // read input sequence file
  $read_input_result = read_input_sequence_file( $INPUT, $ERRORS, $MAX_NUM_TABLE_FILE_ROWS, $nucleus, $max_nuc_id, $max_alias_id, $input_sequence_file, $blast_sequence_file, $file_paths );
  if ( $read_input_result['success'] === false ) {
    $ERRORS['other'][] = "Error while processing input file " . $file_paths['input_sequence_file_name'] . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    $result['success'] = false;
    return $result;
  }

  // close files
  fclose( $input_sequence_file );
  fclose( $blast_sequence_file );

  // upload contents of `nucleotide` table
  $column_names = array( "nuc_id", "feat_id", "length", "sequence" );
  $num_nuc_files = $read_input_result['num_nuc_files'];

  $bulk_upload_result = upload_tsv_to_table( "nucleotide", $column_names,
    $file_paths['nucleotide_file_path'], $num_nuc_files, true,
    $LINK, $ERRORS );

  if ( $bulk_upload_result === false ) {
    $ERRORS['other'][] = "Error oploading nucleotide sequences in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    $result['success'] = false;
    return $result;
  }

  // upload contents of `alias` table
  $column_names = array( "alias_id", "nuc_id", "alias", "is_primary" );
  $num_alias_files = $read_input_result['num_alias_files'];

  $bulk_upload_result = upload_tsv_to_table( "alias", $column_names,
    $file_paths['alias_file_path'], $num_alias_files, true,
    $LINK, $ERRORS );

  if ( $bulk_upload_result === false ) {
    $ERRORS['other'][] = "Error oploading nucleotide sequences in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    $result['success'] = false;
    return $result;
  }

  // upload contents of `telomere` table, if product sequences
  if ( $nucleus === "PRODUCT" ) {

    $column_names = array( "tel_id", "nuc_id", "five_start", "five_length", "three_start", "three_length" );
    $num_tel_files = $read_input_result['num_tel_files'];

    $bulk_upload_result = upload_tsv_to_table( "telomere", $column_names,
      $file_paths['telomere_file_path'], $num_tel_files, true,
      $LINK, $ERRORS );

    if ( $bulk_upload_result === false ) {
      $ERRORS['other'][] = "Error oploading telomeres in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
      $result['success'] = false;
      return $result;
    }

  }

  $result['max_nuc_id'] = $read_input_result['max_nuc_id'];
  $result['max_alias_id'] = $read_input_result['max_alias_id'];
  return $result;

}

?>