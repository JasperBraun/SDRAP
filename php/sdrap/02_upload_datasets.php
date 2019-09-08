<?php
/**
 *
 * SDRAP step 02 of 06: upload of initial data
 * uploads metadata, computes telomeres and uploads nucleotide sequences
 *
 * written by Jasper Braun (jasperbraun@mail.usf.edu)
 * based on phpMIDAS written by Jonathan Burns
 * last updated by Jasper Braun (jasperbraun@mail.usf.edu) on March 04, 2019
 *
 **/

// get sanitized inputs and connect to database
require_once( __DIR__ . "/validate_inputs.php" );

// load helper functions
require_once( __DIR__ . "/../support/02/upload_metadata.php" );
require_once( __DIR__ . "/../support/02/process_sequences.php" );

ini_set( 'max_execution_time', 3600 );
ini_set( 'memory_limit', '20000M' );

// upload metadata
if ( upload_features($LINK) === false ) {
  $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}
if ( upload_organism($LINK, $INPUT) === false ) {
  $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}
if ( upload_parameters($LINK, $INPUT) === false ) {
  $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// process input product sequences
$prod_sequences = process_sequences( $INPUT, $ERRORS, $LINK, $DIRECTORIES, $MAX_NUM_TABLE_FILE_ROWS, "PRODUCT", 0, 0 );
if ( $prod_sequences['success'] === false ) {
  $ERRORS['other'][] = "Failed to process product sequences in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// process input precursor sequences
$max_nuc_id = $prod_sequences['max_nuc_id'];
$max_alias_id = $prod_sequences['max_alias_id'];
$prec_sequences = process_sequences( $INPUT, $ERRORS, $LINK, $DIRECTORIES, $MAX_NUM_TABLE_FILE_ROWS, "PRECURSOR", $max_nuc_id, $max_alias_id );
if ( $prec_sequences['success'] === false ) {
  $ERRORS['other'][] = "Failed to process precursor sequences in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// return valid variables
require_once( __DIR__ . "/return_message.php" );

?>
