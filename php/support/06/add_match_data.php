<?php

// load helper function
require_once( __DIR__ . "/add_data.php" );

// fills summary_data with all data regarding matches
function add_match_data( array &$summary_data, array $INPUT, array &$ERRORS, $LINK ) {

  // obtain number of matches
  $query = "SELECT COUNT(*) FROM `match` WHERE `is_fragment` = 0;";

  if ( add_data( $summary_data['match_num'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of matches in arrangements with high coverage
  $query = "SELECT COUNT(*) FROM `match` AS M LEFT JOIN `coverage` AS C
      ON M.`prec_nuc_id` = C.`prec_nuc_id` AND M.`prod_nuc_id` = C.`prod_nuc_id`
      WHERE M.`is_fragment` = 0 AND C.`coverage` >= '{$INPUT['PROPERTY_MIN_COVERAGE']}';";

  if ( add_data( $summary_data['match_num_high_cov'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of preliminary matches
  $query = "SELECT COUNT(*) FROM `match` WHERE `is_preliminary` = 1;";

  if ( add_data( $summary_data['match_pre_num'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of preliminary matches in arrangements with high coverage
  $query = "SELECT COUNT(*) FROM `match` AS M LEFT JOIN `coverage` AS C
      ON M.`prec_nuc_id` = C.`prec_nuc_id` AND M.`prod_nuc_id` = C.`prod_nuc_id`
      WHERE M.`is_preliminary` = 1 AND C.`coverage` >= '{$INPUT['PROPERTY_MIN_COVERAGE']}';";

  if ( add_data( $summary_data['match_pre_num_high_cov'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of fragments
  $query = "SELECT COUNT(*) FROM `match` WHERE `is_fragment` = 1;";

  if ( add_data( $summary_data['match_frag_num'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of fragments between sequences with high coverage
  $query = "SELECT COUNT(*) FROM `match` AS M LEFT JOIN `coverage` AS C
      ON M.`prec_nuc_id` = C.`prec_nuc_id` AND M.`prod_nuc_id` = C.`prod_nuc_id`
      WHERE M.`is_fragment` = 1 AND C.`coverage` >= '{$INPUT['PROPERTY_MIN_COVERAGE']}';";

  if ( add_data( $summary_data['match_frag_num_high_cov'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of fragments with index 0
  $query = "SELECT COUNT(*) FROM `match` WHERE `index` = 0;";

  if ( add_data( $summary_data['match_frag_zero_num'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of fragments with index 0 between sequences with high coverage
  $query = "SELECT COUNT(*) FROM `match` AS M LEFT JOIN `coverage` AS C
      ON M.`prec_nuc_id` = C.`prec_nuc_id` AND M.`prod_nuc_id` = C.`prod_nuc_id`
      WHERE M.`index` = 0 AND C.`coverage` >= '{$INPUT['PROPERTY_MIN_COVERAGE']}';";

  if ( add_data( $summary_data['match_frag_zero_num_high_cov'], $query, $ERRORS, $LINK ) ===
      false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of merged matches
  $query = "SELECT COUNT(*) FROM `match` WHERE `is_fragment` = 0 AND `hsp_id` LIKE '%\_%';";

  if ( add_data( $summary_data['match_merged_num'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of merged matches in arrangements with high coverage
  $query = "SELECT COUNT(*) FROM `match` AS M LEFT JOIN `coverage` AS C
      ON M.`prec_nuc_id` = C.`prec_nuc_id` AND M.`prod_nuc_id` = C.`prod_nuc_id`
      WHERE M.`is_fragment` = 0 AND M.`hsp_id` LIKE '%\_%'
      AND C.`coverage` >= '{$INPUT['PROPERTY_MIN_COVERAGE']}';";

  if ( add_data( $summary_data['match_merged_num_high_cov'], $query, $ERRORS, $LINK ) ===
      false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of pointers
  $query = "SELECT COUNT(*) FROM `pointer`;";

  if ( add_data( $summary_data['match_ptr_num'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of pointers in arrangements with high coverage
  $query = "SELECT COUNT(*) FROM `pointer` AS P LEFT JOIN `coverage` AS C
      ON P.`prec_nuc_id` = C.`prec_nuc_id` AND P.`prod_nuc_id` = C.`prod_nuc_id`
      WHERE C.`coverage` >= '{$INPUT['PROPERTY_MIN_COVERAGE']}';";

  if ( add_data( $summary_data['match_ptr_num_high_cov'], $query, $ERRORS, $LINK ) ===
      false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of gaps
  $query = "SELECT COUNT(*) FROM `gap`;";

  if ( add_data( $summary_data['match_gap_num'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of gaps in arrangements with high coverage
  $query = "SELECT COUNT(*) FROM `gap` AS G LEFT JOIN `coverage` AS C
      ON G.`prec_nuc_id` = C.`prec_nuc_id` AND G.`prod_nuc_id` = C.`prod_nuc_id`
      WHERE C.`coverage` >= '{$INPUT['PROPERTY_MIN_COVERAGE']}';";

  if ( add_data( $summary_data['match_gap_num_high_cov'], $query, $ERRORS, $LINK ) ===
      false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of terminal gaps
  $query = "SELECT COUNT(*) FROM `gap` WHERE `is_terminal` = 1;";

  if ( add_data( $summary_data['match_terminal_gap_num'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of terminal gaps in arrangements with high coverage
  $query = "SELECT COUNT(*) FROM `gap` AS G LEFT JOIN `coverage` AS C
      ON G.`prec_nuc_id` = C.`prec_nuc_id` AND G.`prod_nuc_id` = C.`prod_nuc_id`
      WHERE `is_terminal` = 1 AND C.`coverage` >= '{$INPUT['PROPERTY_MIN_COVERAGE']}';";

  if ( add_data( $summary_data['match_terminal_gap_num_high_cov'], $query, $ERRORS, $LINK ) ===
      false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

return true;

}

?>