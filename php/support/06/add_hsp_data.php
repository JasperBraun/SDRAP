<?php

// load helper function
require_once( __DIR__ . "/add_data.php" );

// fills summary_data with all data regarding hsp's
function add_hsp_data( array &$summary_data, array $INPUT, array &$ERRORS, $LINK ) {

  // obtain number of hsp's
  $query = "SELECT COUNT(*) FROM `hsp`;";

  if ( add_data( $summary_data['hsp_num'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain average length of hsp's
  $query = "SELECT AVG(`length`) FROM `hsp`;";

  if ( add_data( $summary_data['hsp_avg_length'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain minimum length of hsp's
  $query = "SELECT MIN(`length`) FROM `hsp`;";

  if ( add_data( $summary_data['hsp_min_length'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain maximum length of hsp's
  $query = "SELECT MAX(`length`) FROM `hsp`;";

  if ( add_data( $summary_data['hsp_max_length'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain average bitscore of hsp's
  $query = "SELECT AVG(`bitscore`) FROM `hsp`;";

  if ( add_data( $summary_data['hsp_avg_bitscore'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain minimum bitscore of hsp's
  $query = "SELECT MIN(`bitscore`) FROM `hsp`;";

  if ( add_data( $summary_data['hsp_min_bitscore'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain maximum bitscore of hsp's
  $query = "SELECT MAX(`bitscore`) FROM `hsp`;";

  if ( add_data( $summary_data['hsp_max_bitscore'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of hsp's with bitscore sufficient for preliminary matches
  $query = "SELECT COUNT(*) FROM `hsp` WHERE `bitscore` >= '{$INPUT['PRE_MATCH_MIN_BITSCORE']}';";

  if ( add_data( $summary_data['hsp_num_bitscore_pre'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of hsp's with bitscore sufficient for additional matches
  $query = "SELECT COUNT(*) FROM `hsp` WHERE `bitscore` >= '{$INPUT['ADD_MATCH_MIN_BITSCORE']}';";

  if ( add_data( $summary_data['hsp_num_bitscore_add'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain average pident of hsp's
  $query = "SELECT AVG(`pident`) FROM `hsp`;";

  if ( add_data( $summary_data['hsp_avg_pident'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain minimum pident of hsp's
  $query = "SELECT MIN(`pident`) FROM `hsp`;";

  if ( add_data( $summary_data['hsp_min_pident'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain maximum pident of hsp's
  $query = "SELECT MAX(`pident`) FROM `hsp`;";

  if ( add_data( $summary_data['hsp_max_pident'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of hsp's with pident sufficient for preliminary matches
  $query = "SELECT COUNT(*) FROM `hsp` WHERE `pident` >= '{$INPUT['PRE_MATCH_MIN_PIDENT']}';";

  if ( add_data( $summary_data['hsp_num_pident_pre'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of hsp's with pident sufficient for additional matches
  $query = "SELECT COUNT(*) FROM `hsp` WHERE `pident` >= '{$INPUT['ADD_MATCH_MIN_PIDENT']}';";

  if ( add_data( $summary_data['hsp_num_pident_add'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  return true;

}

?>