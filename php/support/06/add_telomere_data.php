<?php

// load helper function
require_once( __DIR__ . "/add_data.php" );

// fills summary_data with all data regarding telomeres
function add_telomere_data( array &$summary_data, array $INPUT, array &$ERRORS, $LINK ) {

  // obtain number of product sequences with nonzero telomeres at both ends
  $query = "SELECT COUNT(*) FROM `telomere` WHERE `five_length` > 0 AND `three_length` > 0;";

  if ( add_data( $summary_data['telo_num_both'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of product sequences with nonzero telomeres only at 5' end
  $query = "SELECT COUNT(*) FROM `telomere` WHERE `five_length` > 0 AND `three_length` = 0;";

  if ( add_data( $summary_data['telo_num_five'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of product sequences with nonzero telomeres only at 3' end
  $query = "SELECT COUNT(*) FROM `telomere` WHERE `five_length` = 0 AND `three_length` > 0;";

  if ( add_data( $summary_data['telo_num_three'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of product sequences with nonzero telomeres at neither end
  $query = "SELECT COUNT(*) FROM `telomere` WHERE `five_length` = 0 AND `three_length` = 0;";

  if ( add_data( $summary_data['telo_num_neither'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of nonzero telomeres
  $query = "SELECT COUNT(*) FROM
      ( (SELECT `five_length` AS `length` FROM `telomere` WHERE `five_length` > 0)
      UNION ALL
      (SELECT `three_length` AS `length` FROM `telomere` WHERE `three_length` > 0) ) AS T;";

  if ( add_data( $summary_data['telo_num'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain average length of nonzero telomeres
  $query = "SELECT AVG(T.`length`) FROM
      ( (SELECT `five_length` AS `length` FROM `telomere` WHERE `five_length` > 0)
      UNION ALL
      (SELECT `three_length` AS `length` FROM `telomere` WHERE `three_length` > 0) ) AS T;";

  if ( add_data( $summary_data['telo_avg_length'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain average length of nonzero telomeres at 5' ends
  $query = "SELECT AVG(`five_length`) FROM `telomere` WHERE `five_length` > 0;";

  if ( add_data( $summary_data['telo_avg_length_five'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain average length of nonzero telomeres at 3' ends
  $query = "SELECT AVG(`three_length`) FROM `telomere` WHERE `three_length` > 0;";

  if ( add_data( $summary_data['telo_avg_length_three'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain minimum length of nonzero telomeres
  $query = "SELECT MIN(T.`length`) FROM
      ( (SELECT `five_length` AS `length` FROM `telomere` WHERE `five_length` > 0)
      UNION ALL
      (SELECT `three_length` AS `length` FROM `telomere` WHERE `three_length` > 0) ) AS T;";

  if ( add_data( $summary_data['telo_min_length'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain maximum length of nonzero telomeres
  $query = "SELECT MAX(T.`length`) FROM
      ( (SELECT `five_length` AS `length` FROM `telomere` WHERE `five_length` > 0)
      UNION ALL
      (SELECT `three_length` AS `length` FROM `telomere` WHERE `three_length` > 0) ) AS T;";

  if ( add_data( $summary_data['telo_max_length'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain number of telomeres at minimum length
  $query = "SELECT COUNT(*) FROM
      ( (SELECT `five_length` AS `length` FROM `telomere`
      WHERE `five_length` = '{$INPUT['TELO_MIN_LENGTH']}')
      UNION ALL
      (SELECT `three_length` AS `length` FROM `telomere`
      WHERE `three_length` = '{$INPUT['TELO_MIN_LENGTH']}') ) AS T;";

  if ( add_data( $summary_data['telo_num_min_length'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }
  
  // obtain number of telomeres at maximum length
  $query = "SELECT COUNT(*) FROM
      ( (SELECT `five_length` AS `length` FROM `telomere`
      WHERE `five_length` = '{$INPUT['TELO_MAX_LENGTH']}')
      UNION ALL
      (SELECT `three_length` AS `length` FROM `telomere`
      WHERE `three_length` = '{$INPUT['TELO_MAX_LENGTH']}') ) AS T;";

  if ( add_data( $summary_data['telo_num_max_length'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  // obtain maximum offset of nonzero telomeres
  $query = "SELECT MAX(A.`offset`) FROM
      ( (SELECT `five_start` - 1 AS `offset` FROM `telomere` WHERE `five_length` > 0)
      UNION ALL
      (SELECT N.`length` - T.`three_length` - T.`three_start` + 1 AS `offset` FROM `telomere` AS T
      LEFT JOIN `nucleotide` AS N ON T.`nuc_id` = N.`nuc_id`
      WHERE T.`three_length` > 0) ) AS A;";

  if ( add_data( $summary_data['telo_max_offset'], $query, $ERRORS, $LINK ) === false ) {
    $ERRORS['other'][] = "Error while extracting data in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  return true;

}

?>