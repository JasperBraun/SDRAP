<?php
/* part of SDRAP step; sanitizes and validates form inputs */

// base directory for input, output and temporary files of the program
$BASE_DIRECTORY = str_replace( "/php/sdrap", "/", __DIR__ );

// parameters for blast; currently not userdefined to avoid command line injection
$BLAST_PARAMETERS = "-task megablast -ungapped -lcase_masking -word_size 18 -dust no -max_hsps 10000 -max_target_seqs 10000";
$BLAST_DATABASE_FILE_EXTENSIONS = array( ".nhr", ".nin", ".nog", ".nsd", ".nsi", ".nsq" );

// maximum number of rows in data files used for bulk uploads into tables
$MAX_NUM_TABLE_FILE_ROWS = 500000;

// initialize variables for error message handling
$ERRORS = array(
  "input" => array(),
  "other" => array()
);
$NO_ERRORS = $ERRORS;
$RESPONSE = array();

// initialize mysql database connection
$LINK = NULL;

// load helper functions
require_once( __DIR__ . "/../support/validate_inputs/validate.php" );
require_once( __DIR__ . "/../support/validate_inputs/test_connection.php" );
require_once( __DIR__ . "/../support/validate_inputs/get_directories.php" );

// assign variables from input
$INPUT = array(
  "HOSTNAME" => ( $_POST['hostname'] === 'localhost' ) ?
    'localhost' :
    filter_var( gethostbyname ( $_POST['hostname'] ), FILTER_VALIDATE_IP ),
  "USERNAME" => filter_input( INPUT_POST, 'username', FILTER_SANITIZE_STRING ),
  "PASSWORD" => filter_input( INPUT_POST, 'password', FILTER_SANITIZE_STRING ),
  "DATABASE" => filter_input( INPUT_POST, 'database', FILTER_SANITIZE_EMAIL ),
  "PRECURSOR_FILENAME" => filter_input( INPUT_POST, 'precursor_filename', FILTER_SANITIZE_STRING ),
  "PRECURSOR_DELIMITER" => filter_input(
    INPUT_POST, 'precursor_delimiter', FILTER_SANITIZE_STRING
  ),
  "PRODUCT_FILENAME" => filter_input( INPUT_POST, 'product_filename', FILTER_SANITIZE_STRING ),
  "PRODUCT_DELIMITER" => filter_input( INPUT_POST, 'product_delimiter', FILTER_SANITIZE_STRING ),
  "GENUS" => filter_input( INPUT_POST, 'genus', FILTER_SANITIZE_STRING ),
  "SPECIES" => filter_input( INPUT_POST, 'species', FILTER_SANITIZE_STRING ),
  "STRAIN" => filter_input( INPUT_POST, 'strain', FILTER_SANITIZE_STRING ),
  "TAXONOMY_ID" => intval( filter_input( INPUT_POST, 'taxonomy_id', FILTER_SANITIZE_NUMBER_INT ) ),
  "TELO_PATTERN" => filter_input( INPUT_POST, 'telo_pattern', FILTER_SANITIZE_STRING ),
  "TELO_ERROR_LIMIT" => floatval( filter_input( INPUT_POST, 'telo_error_limit', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) ),
  "TELO_BUFFER_LIMIT" => intval( filter_input( INPUT_POST, 'telo_buffer_limit', FILTER_SANITIZE_NUMBER_INT ) ),
  "TELO_MAX_LENGTH" => intval( filter_input( INPUT_POST, 'telo_max_length', FILTER_SANITIZE_NUMBER_INT ) ),
  "TELO_MAX_OFFSET" => intval( filter_input( INPUT_POST, 'telo_max_offset', FILTER_SANITIZE_NUMBER_INT ) ),
  "TELO_MIN_LENGTH" => intval( filter_input( INPUT_POST, 'telo_min_length', FILTER_SANITIZE_NUMBER_INT ) ),
  "HSP_MIN_LENGTH" => intval( filter_input( INPUT_POST, 'hsp_min_length', FILTER_SANITIZE_NUMBER_INT ) ),
  "PRE_MATCH_MIN_BITSCORE" => intval( filter_input( INPUT_POST, 'pre_match_min_bitscore', FILTER_SANITIZE_NUMBER_INT ) ),
  "PRE_MATCH_MIN_PIDENT" => floatval( filter_input( INPUT_POST, 'pre_match_min_pident', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) ),
  "PRE_MATCH_MIN_COVERAGE_ADDITION" => intval( filter_input( INPUT_POST, 'pre_match_min_coverage_addition', FILTER_SANITIZE_NUMBER_INT ) ),
  "MERGE_TOLERANCE" => intval( filter_input( INPUT_POST, 'merge_tolerance', FILTER_SANITIZE_NUMBER_INT ) ),
  "MERGE_MAX_GAP" => intval( filter_input( INPUT_POST, 'merge_max_gap', FILTER_SANITIZE_NUMBER_INT ) ),
  "GAP_MIN_LENGTH" => intval( filter_input( INPUT_POST, 'gap_min_length', FILTER_SANITIZE_NUMBER_INT ) ),
  "COMPUTE_POINTERS" => filter_input( INPUT_POST, 'compute_pointers', FILTER_VALIDATE_BOOLEAN ),  
  "POINTER_MIN_LENGTH" => intval( filter_input( INPUT_POST, 'pointer_min_length', FILTER_SANITIZE_NUMBER_INT ) ),
  "ADD_MATCH_MIN_BITSCORE" => intval( filter_input( INPUT_POST, 'add_match_min_bitscore', FILTER_SANITIZE_NUMBER_INT ) ),
  "ADD_MATCH_MIN_PIDENT" => floatval( filter_input( INPUT_POST, 'add_match_min_pident', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) ),
  "ADD_MATCH_MIN_PROD_SEGMENT_OVERLAP" => floatval( filter_input( INPUT_POST, 'add_match_min_prod_segment_overlap', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) ),
  "FRAGMENT_MIN_PROD_SEGMENT_OVERLAP" => floatval( filter_input( INPUT_POST, 'fragment_min_prod_segment_overlap', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) ),
  "PROPERTY_MIN_COVERAGE" => intval( filter_input( INPUT_POST, 'property_min_coverage', FILTER_SANITIZE_NUMBER_INT ) ),
  "PROPERTY_MAX_MATCH_OVERLAP" => intval( filter_input( INPUT_POST, 'property_max_match_overlap', FILTER_SANITIZE_NUMBER_INT ) ),
  "PROPERTY_CLIQUE_LIMIT" => intval( filter_input( INPUT_POST, 'property_clique_limit', FILTER_SANITIZE_NUMBER_INT ) ),
  "SCR_COMPLETE" => filter_input( INPUT_POST, 'scr_complete', FILTER_VALIDATE_BOOLEAN ),
  "SCR_CONSECUTIVE" => filter_input( INPUT_POST, 'scr_consecutive', FILTER_VALIDATE_BOOLEAN ),
  "SCR_ORDERED" => filter_input( INPUT_POST, 'scr_ordered', FILTER_VALIDATE_BOOLEAN ),
  "OUTPUT_MIN_COVERAGE" => intval( filter_input( INPUT_POST, 'output_min_coverage', FILTER_SANITIZE_NUMBER_INT ) ),
  "OUTPUT_USE_ALIAS" => filter_input( INPUT_POST, 'output_use_alias', FILTER_VALIDATE_BOOLEAN ),
  "OUTPUT_GAPS" => filter_input( INPUT_POST, 'output_gaps', FILTER_VALIDATE_BOOLEAN ),
  "OUTPUT_FRAGMENTS" => filter_input( INPUT_POST, 'output_fragments', FILTER_VALIDATE_BOOLEAN ),
  "OUTPUT_GIVE_COMPLEMENT" => filter_input( INPUT_POST, 'output_give_complement', FILTER_VALIDATE_BOOLEAN ),
  "OUTPUT_MIN_COMPLEMENT_LENGTH" => intval( filter_input( INPUT_POST, 'output_min_complement_length', FILTER_SANITIZE_NUMBER_INT ) ),
  "OUTPUT_GIVE_SUMMARY" => filter_input( INPUT_POST, 'output_give_summary', FILTER_VALIDATE_BOOLEAN )
);

// validate user inputs
validate( $_POST, $INPUT, $ERRORS, $BASE_DIRECTORY );

if ( $ERRORS === $NO_ERRORS ) {

  // test database connection
  test_connection( $INPUT, $LINK, $ERRORS );

  // prepare folder for output
  exec( "mkdir -p " . $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] );

  // get directories for all input, temporary and output files
  $DIRECTORIES = get_directories( $INPUT, $BASE_DIRECTORY );

}

// terminate script
if ( $ERRORS !== $NO_ERRORS ) {

  $RESPONSE['success'] = false;
  $RESPONSE['errors'] = $ERRORS;

  die ( json_encode( $RESPONSE ) );

}

?>
