<?php
/**
 *
 * SDRAP step 05 of 06: computation of arrangement properties
 * computes arrangement properties
 *
 * written by Jasper Braun (jasperbraun@mail.usf.edu)
 * based on phpMIDAS written by Jonathan Burns
 * last updated by Jasper Braun (jasperbraun@mail.usf.edu) on March 18, 2019
 *
 **/

// get sanitized inputs and connect to database
require_once( __DIR__ . "/validate_inputs.php" );

// load helper functions
require_once( __DIR__ . "/../support/05/compute_properties.php" );

ini_set( 'max_execution_time', 18000 );
ini_set( 'memory_limit', '50000M' );

// create indices to speed up mysql queries on `match` table
$index_query_result = mysqli_query( $LINK,
    "CREATE INDEX match_multiple
    ON `match` ( `prec_nuc_id`, `prod_nuc_id`, `is_fragment`, `prec_start` );"
);
if ( $index_query_result === false ) {
  $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
      " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

$index_query_result = mysqli_query( $LINK,
    "CREATE INDEX match_is_preliminary
    ON `match` ( `is_preliminary` );"
);
if ( $index_query_result === false ) {
  $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
      " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// compute properties and assembly words
$properties_result = compute_properties( $INPUT, $ERRORS, $LINK, $DIRECTORIES,
    $MAX_NUM_TABLE_FILE_ROWS );
if ( $properties_result === false ) {
  $ERRORS['other'][] = "Error while computing arrangement properties in " .
      basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// return valid variables
require_once ( "return_message.php" );

?>
