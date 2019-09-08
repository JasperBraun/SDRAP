<?php

// load helper functions
require_once( __DIR__ . "/chop_convert_hsp.php" );
require_once( __DIR__ . "/read_pre_matches.php" );
require_once( __DIR__ . "/output_add_matches.php" );
require_once( __DIR__ . "/append_matches.php" );

// computes and outputs additional matches, and fragments of product for all matching precursors
function compute_add_annotations( array $inter_tel_interval, $prod_nuc_id, array &$nuc_id_to_alias,
    array &$max_id, array &$num_files, &$num_match_row, array $INPUT, $LINK, array &$ERRORS,
    array &$DIRECTORIES, $MAX_NUM_TABLE_FILE_ROWS ) {

  // get table of all HSPs on prod['nuc_id'] with sufficient bitscore and pident
  // ordered by prec_nuc_id, bitscore and pident as primary, secondary and tertiary keys
  $hsp_table = mysqli_query ( $LINK,
    "SELECT `hsp_id`, `prec_nuc_id`, `prec_start`, `prec_end`,
        `prod_start`, `prod_end`, `orientation`, `length`, `bitscore`, `pident`
    FROM `hsp` AS H
    WHERE `prod_nuc_id` = '{$prod_nuc_id}'
    AND `bitscore` >= '{$INPUT['ADD_MATCH_MIN_BITSCORE']}'
    AND `pident` >= '{$INPUT['ADD_MATCH_MIN_PIDENT']}'
    ORDER BY `prec_nuc_id` ASC, `prod_start` ASC;"
  );
  if ( $hsp_table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // return if product sequence has no suitable hsp
  if ( mysqli_num_rows( $hsp_table ) === 0 ) {
    return true;
  }

  // initialize variables
  $prod_alias = $nuc_id_to_alias[ $prod_nuc_id ];
  $match_file = NULL;
  $add_match_array = array();
  $prec_nuc_id = "";
  $prec_alias = "";

  while ( $full_hsp = mysqli_fetch_assoc( $hsp_table ) ) { // iterate over HSPs

    // chop off inter-telomeric portions and convert to correct data types
    $hsp = chop_convert_hsp( $full_hsp, $inter_tel_interval );

    // if next precursor sequence, process matches and (re-)initialize variables
    if ( $hsp['prec_nuc_id'] !== $prec_nuc_id ) {

      // process matches of previous precursor sequence, if any
      if ( sizeof( $add_match_array ) > 0 ) {

        // output rows for additional matches and fragments for `match` table
        output_add_matches( $add_match_array, $prec_nuc_id, $prec_alias, $prod_nuc_id, $prod_alias,
            $INPUT['OUTPUT_USE_ALIAS'], $max_id, $match_file, $num_files, $num_match_row,
            $DIRECTORIES['MATCH_FILE_PATH'], $MAX_NUM_TABLE_FILE_ROWS );

      }

      // (re-)initialize variables
      $add_match_array = array();
      $prec_nuc_id = $hsp['prec_nuc_id'];
      $prec_alias = $nuc_id_to_alias[ $prec_nuc_id ];
      $pre_match_array = array();
      $pre_match_hsp_id_array = array();
      read_pre_matches( $DIRECTORIES['TMP_MATCH_FILE_PATH'] . $prec_nuc_id . ".tsv",
          $pre_match_array, $pre_match_hsp_id_array );

    }

    // skip hsp if it was used for the formation of a preliminary match
    if ( isset( $pre_match_hsp_id_array[ $hsp['hsp_id'][0] ] ) === true ) {
      continue;
    }

    // check which previously established matches should be merged with the hsp
    // and merge them with the hsp removing them from the new_match_array
    $merged_hsp = merge( $hsp, $add_match_array, $INPUT['MERGE_TOLERANCE'],
        $INPUT['MERGE_MAX_GAP'] );

    // add a match or fragment to add_match_array for each preliminary match the hsp sufficiently
    // overlaps with
    append_matches( $merged_hsp, $pre_match_array, $add_match_array,
        $INPUT['ADD_MATCH_MIN_PROD_SEGMENT_OVERLAP'], $INPUT['FRAGMENT_MIN_PROD_SEGMENT_OVERLAP'] );

  }

  // process matches of last precursor sequence, if any
  if ( sizeof( $add_match_array ) > 0 ) {

    // output rows for additional matches and fragments for `match` table
    output_add_matches( $add_match_array, $prec_nuc_id, $prec_alias, $prod_nuc_id, $prod_alias,
        $INPUT['OUTPUT_USE_ALIAS'], $max_id, $match_file, $num_files, $num_match_row,
        $DIRECTORIES['MATCH_FILE_PATH'], $MAX_NUM_TABLE_FILE_ROWS );

  }

  if ( $match_file !== NULL ) { fclose($match_file); }

  return true;

}

?>