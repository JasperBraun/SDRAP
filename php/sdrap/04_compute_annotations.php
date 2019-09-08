<?php
/**
 *
 * SDRAP step 04 of 06: annotation of matches
 * annotating precursor and product segments of matches and uploading annotation to the MySQL database
 *
 * written by Jasper Braun (jasperbraun@mail.usf.edu)
 * based on phpMIDAS written by Jonathan Burns
 * last updated by Jasper Braun (jasperbraun@mail.usf.edu) on March 17, 2019
 *
 **/

// get sanitized inputs and connect to database
require_once( __DIR__ . "/validate_inputs.php" );

// load helper functions
require_once( __DIR__ . "/../support/04/annotate.php" );

ini_set( 'max_execution_time', 60000 );
ini_set( 'memory_limit', '50000M' );

// create index to speed up mysql queries on `hsp` table
$index_query_result = mysqli_query( $LINK,
  "CREATE INDEX hsp_bitscore_pident
   ON `hsp` ( `bitscore`, `pident` );"
);
if ( $index_query_result === false ) {
  $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// compute matches and gaps
$annotate_result = annotate( $INPUT, $ERRORS, $LINK, $DIRECTORIES, $MAX_NUM_TABLE_FILE_ROWS );
if ( $annotate_result === false ) {
  $ERRORS['other'][] = "Error while computing annotations in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// return valid variables
require_once( __DIR__ . "/return_message.php" );

?>