<?php
/**
 *
 * SDRAP step 01 of 06: database creation
 * creates MySQL database and tables
 *
 * written by Jasper Braun (jasperbraun@mail.usf.edu)
 * based on phpMIDAS written by Jonathan Burns
 * last updated by Jasper Braun (jasperbraun@mail.usf.edu) on March 04, 2019
 *
 **/

// get sanitized inputs and connect to database
require_once( __DIR__ . "/validate_inputs.php" );

// load helper functions
require_once( __DIR__ . "/../support/01/database_exists.php" );
require_once( __DIR__ . "/../support/01/create_tables.php" );

ini_set( 'max_execution_time', 3600 );
ini_set( 'memory_limit', '20000M' );

// check whether database already exists; note that this function
// returns booleans true, or false, or the string "failure"
if ( database_exists( $INPUT['DATABASE'], $ERRORS, $LINK ) === true ) {

  $ERRORS['other'][] = "Database" .  $INPUT['DATABASE'] . "already exists in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";

} else {

  // create new database  
  if ( mysqli_query( $LINK, "CREATE DATABASE IF NOT EXISTS `{$INPUT['DATABASE']}`;" ) ) { 

    // set INPUT['DATABASE'] as default database  
    if ( mysqli_select_db( $LINK, $INPUT['DATABASE'] ) === false ) {
      $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    }

    // add tables
    create_parameter_table( $LINK, $ERRORS );
    create_organism_table( $LINK, $ERRORS );
    create_feature_table( $LINK, $ERRORS );
    create_nucleotide_table( $LINK, $ERRORS );
    create_alias_table( $LINK, $ERRORS );
    create_telomere_table( $LINK, $ERRORS );
    create_hsp_table( $LINK, $ERRORS );
    create_match_table( $LINK, $ERRORS );
    create_gap_table( $LINK, $ERRORS );
    create_pointer_table( $LINK, $ERRORS );
    create_coverage_table( $LINK, $ERRORS );
    create_properties_table( $LINK, $ERRORS );

  } else {

    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";

  }

}

// return valid variables
require_once( __DIR__ . "/return_message.php" );

?>
