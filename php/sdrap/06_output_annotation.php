<?php

/**
 *
 * SDRAP step 06 of 06: output of annotations
 * outputs annotations and relevant data
 *
 * written by Jasper Braun (jasperbraun@mail.usf.edu)
 * based on phpMIDAS written by Jonathan Burns
 * last updated by Jasper Braun (jasperbraun@mail.usf.edu) on March 04, 2019
 *
 **/

// get sanitized inputs and connect to database
require_once( __DIR__ . "/validate_inputs.php" );

// load helper functions
require_once( __DIR__ . "/../support/06/output_aliases.php" );
require_once( __DIR__ . "/../support/06/output_sequences.php" );
require_once( __DIR__ . "/../support/06/output_prec_segments_fragments.php" );
require_once( __DIR__ . "/../support/06/output_prec_pointers.php" );
require_once( __DIR__ . "/../support/06/output_prec_hsp.php" );
require_once( __DIR__ . "/../support/06/output_prod_segments.php" );
require_once( __DIR__ . "/../support/06/output_gaps.php" );
require_once( __DIR__ . "/../support/06/output_prod_pointers.php" );
require_once( __DIR__ . "/../support/06/output_prod_hsp.php" );
require_once( __DIR__ . "/../support/06/output_properties.php" );
require_once( __DIR__ . "/../support/06/output_summary.php" );

ini_set( 'max_execution_time', 3600 );
ini_set( 'memory_limit', '20000M' );

// create indices to speed up mysql queries on `alias` table
$index_query_result = mysqli_query( $LINK,
    "CREATE INDEX alias_is_primary
    ON `alias` ( `is_primary` );"
);
if ( $index_query_result === false ) {
  $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
      " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// output aliases for reference
$output_aliases_result = output_aliases( $INPUT['PRECURSOR_DELIMITER'],
    $INPUT['PRODUCT_DELIMITER'], $ERRORS, $LINK, $DIRECTORIES );
if ( $output_aliases_result === false ) {
  $ERRORS['other'][] = "Error while outputting aliases in " .
      basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// output nuceleotide sequences
$output_sequences_result = output_sequences( $INPUT['OUTPUT_USE_ALIAS'], $ERRORS, $LINK,
    $DIRECTORIES );
if ( $output_sequences_result === false ) {
  $ERRORS['other'][] = "Error while outputting nucleotide sequences in " .
      basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// output precursor segments of matches and fragments between sequences with sufficient coverage of
// the product
$output_prec_segments_fragments_result = output_prec_segments_fragments(
    $INPUT['OUTPUT_USE_ALIAS'], $INPUT['OUTPUT_FRAGMENTS'], $INPUT['OUTPUT_GIVE_COMPLEMENT'],
    $INPUT['OUTPUT_MIN_COMPLEMENT_LENGTH'], $INPUT['OUTPUT_MIN_COVERAGE'],
    $ERRORS, $LINK, $DIRECTORIES );
if ( $output_prec_segments_fragments_result === false ) {
  $ERRORS['other'][] = "Error while outputting precursor segments and fragments in " .
      basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// output precursor pointers of matches between sequences with sufficient coverage of the product
if ( $INPUT['COMPUTE_POINTERS'] === true ) {

  $output_prec_pointers_result = output_prec_pointers( $INPUT['OUTPUT_USE_ALIAS'],
      $INPUT['OUTPUT_MIN_COVERAGE'], $ERRORS, $LINK, $DIRECTORIES );
  if ( $output_prec_pointers_result === false ) {
    $ERRORS['other'][] = "Error while outputting precursor pointers in " .
        basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

}

// output precursor segments of hsp's between sequences with sufficient coverage of the product
$output_prec_hsp_result = output_prec_hsp( $INPUT['OUTPUT_USE_ALIAS'],
    $INPUT['OUTPUT_MIN_COVERAGE'], $ERRORS, $LINK, $DIRECTORIES );
if ( $output_prec_hsp_result === false ) {
  $ERRORS['other'][] = "Error while outputting prec segments of HSPs in " .
      basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// output product segments of matches between sequences with sufficient coverage of the product
$output_prod_segments_result = output_prod_segments(
    $INPUT['OUTPUT_USE_ALIAS'], $INPUT['OUTPUT_MIN_COVERAGE'], $ERRORS, $LINK, $DIRECTORIES );
if ( $output_prod_segments_result === false ) {
  $ERRORS['other'][] = "Error while outputting product segments in " . basename(__FILE__,".php") .
      " near line " . __LINE__ . ".";
}

// output gaps on products with respect to precursors who cover the product sufficiently
if ( $INPUT['OUTPUT_GAPS'] === true ) {
  
  $output_gaps_result = output_gaps(
    $INPUT['OUTPUT_USE_ALIAS'], $INPUT['OUTPUT_MIN_COVERAGE'], $ERRORS, $LINK, $DIRECTORIES );
  if ( $output_gaps_result === false ) {
  $ERRORS['other'][] = "Error while outputting gaps in " . basename(__FILE__,".php") .
      " near line " . __LINE__ . ".";
  }

}

// output product pointers of matches between sequences with sufficient coverage of the product
if ( $INPUT['COMPUTE_POINTERS'] === true ) {

  $output_prod_pointers_result = output_prod_pointers( $INPUT['OUTPUT_USE_ALIAS'],
      $INPUT['OUTPUT_MIN_COVERAGE'], $ERRORS, $LINK, $DIRECTORIES );
  if ( $output_prod_pointers_result === false ) {
    $ERRORS['other'][] = "Error while outputting product pointers in " .
        basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

}

// output product segments of hsp's between sequences with sufficient coverage of the product
$output_prod_hsp_result = output_prod_hsp( $INPUT['OUTPUT_USE_ALIAS'],
    $INPUT['OUTPUT_MIN_COVERAGE'], $ERRORS, $LINK, $DIRECTORIES );
if ( $output_prod_hsp_result === false ) {
  $ERRORS['other'][] = "Error while outputting prod segments of HSPs in " .
      basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// output properties of arrangements between sequences with sufficient coverage of the product
$output_properties_result = output_properties( $INPUT['OUTPUT_USE_ALIAS'],
    $INPUT['OUTPUT_MIN_COVERAGE'], $ERRORS, $LINK, $DIRECTORIES );
if ( $output_properties_result === false ) {
  $ERRORS['other'][] = "Error while outputting arrangements properties in " .
      basename(__FILE__,".php") . " near line " . __LINE__ . ".";
}

// output summary, if requested
if ( $INPUT['OUTPUT_GIVE_SUMMARY'] ) {

  // output comprehensive summary of computational outcome
  $output_summary_result = output_summary( $total_time, $INPUT, $ERRORS, $LINK, $DIRECTORIES );
  if ( $output_summary_result === false ) {
    $ERRORS['other'][] = "Error while outputting summary in " .
        basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

}


// output parameter values
$parameters_file = fopen( $DIRECTORIES['PARAMETERS_FILE'], "w" );
unset( $INPUT['PASSWORD'] ); // hide password in output
foreach ( $INPUT as $parameter => $value ) {
  fwrite( $parameters_file, $parameter . "\t" . json_encode( $value ) . "\n" );
}
fclose( $parameters_file );

// return valid variables
require_once( "return_message.php" );

?>