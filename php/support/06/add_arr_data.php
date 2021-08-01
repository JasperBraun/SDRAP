<?php

// load helper function
require_once( __DIR__ . "/add_data.php" );

// fills summary_data with all data regarding arrangements
function add_arr_data( array &$summary_data, array $INPUT, array &$ERRORS, $LINK ) {

  // obtain number of arrangements
  $query = "SELECT COUNT(*) FROM `coverage`;";

  if ( add_data( $summary_data['arr_num'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of high coverage arrangements
  $query = "SELECT COUNT(*) FROM `coverage`
      WHERE `coverage` >= '{$INPUT['PROPERTY_MIN_COVERAGE']}';";

  if ( add_data( $summary_data['arr_num_high_cov'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain average coverage of arrangements
  $query = "SELECT AVG(`coverage`) FROM `coverage`;";

  if ( add_data( $summary_data['arr_avg_cov'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain average coverage of high coverage arrangements
  $query = "SELECT AVG(`coverage`) FROM `coverage`
      WHERE `coverage` >= '{$INPUT['PROPERTY_MIN_COVERAGE']}';";

  if ( add_data( $summary_data['arr_avg_cov_high_cov'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of arrangements with gaps
  $query = "SELECT COUNT(*) FROM (SELECT DISTINCT `prec_nuc_id`, `prod_nuc_id` FROM `gap`) AS G;";

  if ( add_data( $summary_data['arr_num_gapped'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of high coverage arrangements with gaps
  $query = "SELECT COUNT(*) FROM `properties` WHERE `non_gapped` = 0;";

  if ( add_data( $summary_data['arr_num_gapped_high_cov'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of arrangements with terminal gaps
  $query = "SELECT COUNT(*) FROM (
      SELECT DISTINCT `prec_nuc_id`, `prod_nuc_id` FROM `gap` WHERE `is_terminal` = 1) AS G;";

  if ( add_data( $summary_data['arr_num_terminal_gap'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of high coverage arrangements with terminal gaps
  $query = "SELECT COUNT(*) FROM (
      SELECT DISTINCT G.`prec_nuc_id`, G.`prod_nuc_id` FROM `gap` AS G
      LEFT JOIN `coverage` AS C
      ON G.`prec_nuc_id` = C.`prec_nuc_id` AND G.`prod_nuc_id` = C.`prod_nuc_id`
      WHERE G.`is_terminal` = 1 AND C.`coverage` >= '{$INPUT['PROPERTY_MIN_COVERAGE']}' ) AS T;";

  if ( add_data( $summary_data['arr_num_terminal_gap_high_cov'], $query, $ERRORS, $LINK ) ===
      false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain average number of matches per arrangement
  $query = "SELECT AVG(T.`num_matches`) FROM (
      SELECT COUNT(*) AS `num_matches` FROM `match` WHERE `is_fragment` = 0
      GROUP BY `prec_nuc_id`, `prod_nuc_id`) AS T;";

  if ( add_data( $summary_data['arr_avg_match'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain average number of matches per high coverage arrangement
  $query = "SELECT AVG(`total_match_number`) FROM `properties`;";

  if ( add_data( $summary_data['arr_avg_match_high_cov'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain average number of preliminary matches per arrangement
  $query = "SELECT AVG(T.`num_matches`) FROM (
      SELECT COUNT(*) AS `num_matches` FROM `match` WHERE `is_preliminary` = 1
      GROUP BY `prec_nuc_id`, `prod_nuc_id`) AS T;";

  if ( add_data( $summary_data['arr_avg_pre_match'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain average number of preliminary matches per high coverage arrangement
  $query = "SELECT AVG(`preliminary_match_number`) FROM `properties`;";

  if ( add_data( $summary_data['arr_avg_pre_match_high_cov'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain average number of pointers per arrangement
  $query = "SELECT AVG(T.`num_pointers`) FROM (
      SELECT COUNT(*) AS `num_pointers` FROM `pointer`
      GROUP BY `prec_nuc_id`, `prod_nuc_id`) AS T;";

  if ( add_data( $summary_data['arr_avg_ptr'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain average number of pointers per high coverage arrangement
  $query = "SELECT AVG(T.`num_pointers`) FROM (
      SELECT COUNT(*) AS `num_pointers` FROM `pointer` AS P
      LEFT JOIN `coverage` AS C
      ON P.`prec_nuc_id` = C.`prec_nuc_id` AND P.`prod_nuc_id` = C.`prod_nuc_id`
      WHERE C.`coverage` >= '{$INPUT['PROPERTY_MIN_COVERAGE']}'
      GROUP BY P.`prec_nuc_id`, P.`prod_nuc_id`
      ) AS T;";

  if ( add_data( $summary_data['arr_avg_ptr_high_cov'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of high coverage arrangements with repeating matches
  $query = "SELECT COUNT(*) FROM `properties` WHERE `non_repeating` = 0;";

  if ( add_data( $summary_data['arr_num_repeat'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of high coverage arrangements with overlapping matches
  $query = "SELECT COUNT(*) FROM `properties` WHERE `non_overlapping` = 0;";

  if ( add_data( $summary_data['arr_num_overlap'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of high coverage arrangements without repeating, or overlapping matches
  $query = "SELECT COUNT(*) FROM `properties`
      WHERE `non_repeating` = 1 AND `non_overlapping` = 1;";

  if ( add_data( $summary_data['arr_num_clique'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of high coverage arrangements exceeding clique limit
  $query = "SELECT COUNT(*) FROM `properties` WHERE `exceeded_clique_limit` = 1;";

  if ( add_data( $summary_data['arr_num_clique_limit'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of weakly complete high coverage arrangements
  $query = "SELECT COUNT(*) FROM `properties` WHERE `weakly_complete` = 1;";

  if ( add_data( $summary_data['arr_num_weakly_complete'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of strongly complete high coverage arrangements
  $query = "SELECT COUNT(*) FROM `properties` WHERE `strongly_complete` = 1;";

  if ( add_data( $summary_data['arr_num_strongly_complete'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of weakly consecutive high coverage arrangements
  $query = "SELECT COUNT(*) FROM `properties` WHERE `weakly_consecutive` = 1;";

  if ( add_data( $summary_data['arr_num_weakly_consecutive'], $query, $ERRORS, $LINK ) ===
      false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of strongly consecutive high coverage arrangements
  $query = "SELECT COUNT(*) FROM `properties` WHERE `strongly_consecutive` = 1;";

  if ( add_data( $summary_data['arr_num_strongly_consecutive'], $query, $ERRORS, $LINK ) ===
      false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of weakly ordered high coverage arrangements
  $query = "SELECT COUNT(*) FROM `properties` WHERE `weakly_ordered` = 1;";

  if ( add_data( $summary_data['arr_num_weakly_ordered'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of strongly ordered high coverage arrangements
  $query = "SELECT COUNT(*) FROM `properties` WHERE `strongly_ordered` = 1;";

  if ( add_data( $summary_data['arr_num_strongly_ordered'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of weakly scrambled high coverage arrangements
  $query = "SELECT COUNT(*) FROM `properties` WHERE 'strongly_non_scrambled' = 0;";

  if ( add_data( $summary_data['arr_num_weakly_scrambled'], $query, $ERRORS, $LINK ) ===
      false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of strongly scrambled high coverage arrangements
  $query = "SELECT COUNT(*) FROM `properties` WHERE 'weakly_non_scrambled' = 0 AND `strongly_non_scrambled` = 0;";

  if ( add_data( $summary_data['arr_num_strongly_scrambled'], $query, $ERRORS, $LINK ) ===
      false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of arrangements with sufficient coverage for output
  $query = "SELECT COUNT(*) FROM `coverage`
      WHERE `coverage` >= '{$INPUT['OUTPUT_MIN_COVERAGE']}';";

  if ( add_data( $summary_data['arr_num_output'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  return true;

}

?>