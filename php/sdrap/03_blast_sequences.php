<?php
/**
 *
 * SDRAP step 03 of 06: blasting of sequences
 * blasts product sequenes against precursor sequences and uploads high scoring pairs
 *
 * written by Jasper Braun (jasperbraun@mail.usf.edu)
 * based on phpMIDAS written by Jonathan Burns
 * last updated by Jasper Braun (jasperbraun@mail.usf.edu) on March 06, 2019
 *
 **/

// get sanitized inputs and connect to database
require_once( __DIR__ . "/validate_inputs.php" );

// load helper functions
require_once( __DIR__ . "/../support/03/blast_sequences.php" );
require_once( __DIR__ . "/../support/03/parse_blast_output.php" );

ini_set( 'max_execution_time', 3600 );
ini_set( 'memory_limit', '20000M' );

// blast sequences
$blast_result = blast_sequences( $INPUT, $ERRORS, $DIRECTORIES, $BLAST_PARAMETERS,
    $BLAST_DATABASE_FILE_EXTENSIONS );
if ( $blast_result === false ) {
  $ERRORS['other'][] = "Error while attempting to run blast in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// parse blast output and upload data into `hsp` table
$parse_result = parse_blast_output( $INPUT['HSP_MIN_LENGTH'], $LINK, $ERRORS, $DIRECTORIES,
    $MAX_NUM_TABLE_FILE_ROWS, $INPUT['HSP_MIN_LENGTH'] );
if ( $parse_result === false ) {
  $ERRORS['other'][] = "Error while attempting to parse and upload blast output in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// return valid variables
require_once( __DIR__ . "/return_message.php" );

?>
