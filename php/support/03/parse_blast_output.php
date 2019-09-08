<?php

// load helper functions
require_once( __DIR__ . "/read_blast_output_file.php" );
require_once( __DIR__ . "/../upload_tsv_to_table.php" );

// parses blast output and outputs and uploads contents of `hsp` table
function parse_blast_output( $HSP_MIN_LENGTH, $LINK, array &$ERRORS, array $DIRECTORIES, $MAX_NUM_TABLE_FILE_ROWS, $HSP_MIN_LENGTH ) {

  // open blast output file
  $blast_output_file = fopen( $DIRECTORIES['BLAST_OUTPUT_FILE'], "r" );
  if ( $blast_output_file === false ) {
    $ERRORS['other'][] = "Could not open file " . $DIRECTORIES['BLAST_OUTPUT_FILE'] . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // parse blast output and output contents of `hsp` table
  $read_blast_output_result = read_blast_output_file( $HSP_MIN_LENGTH, $ERRORS,
      $DIRECTORIES, $MAX_NUM_TABLE_FILE_ROWS, $blast_output_file );
  if ( $read_blast_output_result['success'] === false ) {
    $ERRORS['other'][] = "Error while reading blast output file " . $DIRECTORIES['BLAST_OUTPUT_FILE'] . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  fclose( $blast_output_file );
  unlink( $DIRECTORIES['BLAST_OUTPUT_FILE'] );
  
  // upload contents of `hsp` table
  $column_names = array( 
    "hsp_id", "org_id",
    "prec_nuc_id", "prec_start", "prec_end",
    "prod_nuc_id", "prod_start", "prod_end",
    "orientation", "length",
    "pident", "mismatch", "evalue", "bitscore"
  );
  $num_hsp_files = $read_blast_output_result['num_hsp_files'];

  $bulk_upload_result = upload_tsv_to_table( "hsp", $column_names,
    $DIRECTORIES['HSP_FILE_PATH'], $num_hsp_files, true,
    $LINK, $ERRORS );

  if ( $bulk_upload_result === false ) {
    $ERRORS['other'][] = "Error oploading high-scoring pairs in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  return true;

}

?>