<?php

// load helper functions
require_once( __DIR__ . "/chop_convert_hsp.php" );
require_once( __DIR__ . "/get_coverage.php" );
require_once( __DIR__ . "/merge.php" );
require_once( __DIR__ . "/replace.php" );
require_once( __DIR__ . "/match_order.php" );
require_once( __DIR__ . "/compute_gap_ptr_cov.php" );

// computes and outputs preliminary matches, gaps, pointers,
// and coverages of product for all matching precursors
function compute_pre_annotations( array $inter_tel_interval, $prod_nuc_id, array &$nuc_id_to_alias,
    array &$prec_nuc_id_array, array &$max_id, array &$num_files, &$num_match_row, array &$INPUT,
    $LINK, array &$ERRORS, array &$DIRECTORIES, $MAX_NUM_TABLE_FILE_ROWS ) {

  // get table of all HSPs on prod['nuc_id'] with sufficient bitscore and pident
  // ordered by prec_nuc_id, bitscore and pident as primary, secondary and tertiary keys
  $hsp_table = mysqli_query ( $LINK,
    "SELECT `hsp_id`, `prec_nuc_id`, `prec_start`, `prec_end`, `prod_start`, `prod_end`,
            `orientation`, `length`, `bitscore`, `pident`
     FROM `hsp` AS H
     WHERE `prod_nuc_id` = '{$prod_nuc_id}'
     AND `bitscore` >= '{$INPUT['PRE_MATCH_MIN_BITSCORE']}'
     AND `pident` >= '{$INPUT['PRE_MATCH_MIN_PIDENT']}'
     ORDER BY `prec_nuc_id` ASC, `bitscore` DESC, `pident` DESC;"
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
  $pre_match_array = array();
  $prec_nuc_id = "";
  $prec_alias = "";

  while ( $full_hsp = mysqli_fetch_assoc( $hsp_table ) ) { // iterate over HSPs

    // chop off inter-telomeric portions and convert to correct data types
    $hsp = chop_convert_hsp( $full_hsp, $inter_tel_interval );

    // if next precursor sequence, process matches and (re-)initialize variables
    if ( $hsp['prec_nuc_id'] !== $prec_nuc_id ) {

      // process matches of previous precursor sequence, if any
      if ( !empty( $pre_match_array ) ) {

        usort( $pre_match_array, "match_order" );

        // compute gaps, pointers and coverage and output contents of
        // `match`, `gap`, `pointer`, and `coverage` tables
        compute_gap_ptr_cov( $pre_match_array, $inter_tel_interval, $prod_nuc_id, $prod_alias,
            $prec_nuc_id, $prec_alias, $max_id, $num_files, $num_match_row, $INPUT, $DIRECTORIES,
            $MAX_NUM_TABLE_FILE_ROWS );

        $prec_nuc_id_array[] = $prec_nuc_id;

      }

      // (re-)initialize variables for matches of new precursor sequence
      $prec_nuc_id = $hsp['prec_nuc_id'];
      $prec_alias = $nuc_id_to_alias[ $prec_nuc_id ];
      $pre_match_array = array();

    }

    // skip HSP if it doesn't intersect inter-telomeric portion of product sequence sufficiently
    if ( $hsp['length'] < $INPUT['PRE_MATCH_MIN_COVERAGE_ADDITION'] ) { continue; }

    // obtain coverage the hsp adds to previously established matches
    $cov = get_coverage( $hsp, $pre_match_array, $INPUT['PRE_MATCH_MIN_COVERAGE_ADDITION'] );

    // skip HSP if it doesn't intersect inter-telomeric portion of product sequence sufficiently
    if ( $cov < $INPUT['PRE_MATCH_MIN_COVERAGE_ADDITION'] ) { continue; }

    // check which previously established matches should be merged with the hsp
    // and merge them with the hsp removing them from the new_match_array
    $merged_hsp = merge( $hsp, $pre_match_array, $INPUT['MERGE_TOLERANCE'],
        $INPUT['MERGE_MAX_GAP'] );

    // check which previously established matches should be replaced by merged hsp
    replace( $merged_hsp, $pre_match_array, $INPUT['PRE_MATCH_MIN_COVERAGE_ADDITION'] );

    // establish merged_hsp as new a new preliminary match
    $pre_match_array[] = $merged_hsp;

  } // end iterate over HSPs

  // process matches of last precursor sequence
  if ( !empty( $pre_match_array ) ) {
    
    usort( $pre_match_array, "match_order" );

    // compute gaps, pointers and coverage and output contents of
    // `match`, `gap`, `pointer`, and `coverage` tables
    compute_gap_ptr_cov( $pre_match_array, $inter_tel_interval, $prod_nuc_id, $prod_alias,
        $prec_nuc_id, $prec_alias, $max_id, $num_files, $num_match_row, $INPUT, $DIRECTORIES,
        $MAX_NUM_TABLE_FILE_ROWS );

    $prec_nuc_id_array[] = $prec_nuc_id;

  }

  mysqli_free_result( $hsp_table );

  return true;

}

?>