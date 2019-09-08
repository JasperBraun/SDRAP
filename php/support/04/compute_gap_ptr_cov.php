<?php

// load helper functions
require_once( __DIR__ . "/process_gap_coverage.php" );
require_once( __DIR__ . "/output_coverage_row.php" );
require_once( __DIR__ . "/output_pre_match_row.php" );
require_once( __DIR__ . "/process_pointer.php" );

// computes gaps, pointers and coverage for matches and outputs contents
// of `match`, `pointer`, `gap`, and `coverage` tables
function compute_gap_ptr_cov( array &$pre_match_array, array $inter_tel_interval, $prod_nuc_id,
    $prod_alias, $prec_nuc_id, $prec_alias, array &$max_id, array &$num_files, &$num_match_row,
    array &$INPUT, array &$DIRECTORIES, $MAX_NUM_TABLE_FILE_ROWS ) {

  // initialize variables
  $match_aux = array(
    "file" => NULL,
    "num_row" => 0,
    "index" => 0
  );
  $gap_aux = $match_aux;
  $ptr_aux = $match_aux;
  $cov_aux = $match_aux;
  $match_aux['tmp_file'] = NULL;

  // go through preliminary matches of prec_nuc_id one-by-one
  $is_first = true;
  foreach ( $pre_match_array as &$match ) {

    if ( $is_first ) {

      // (re-)initialize variables
      $covered = $inter_tel_interval['end'] - $inter_tel_interval['start'] + 1;
      $gap_aux['index'] = 0;
      $match_aux['index'] = 0;
      if ( $match_aux['tmp_file'] !== NULL ) { fclose( $match_aux['tmp_file'] ); }
      $match_aux['tmp_file'] = fopen( $DIRECTORIES['TMP_MATCH_FILE_PATH'] . $prec_nuc_id .
          ".tsv", "w" );

      // determine presence of (left-)terminal gap and output row for `gap` table, if any
      $dummy_last_match = array( "prod_end" => $inter_tel_interval['start'] - 1 );
      process_gap_coverage( $match, $dummy_last_match, $prec_nuc_id, $prec_alias, $prod_nuc_id,
          $prod_alias, 1, $INPUT['OUTPUT_USE_ALIAS'], $gap_aux, $max_id, $num_files, $covered,
          $INPUT['GAP_MIN_LENGTH'], $DIRECTORIES['GAP_FILE_PATH'], $MAX_NUM_TABLE_FILE_ROWS );

      // output row for `match` table
      output_pre_match_row( $match, $prec_nuc_id, $prec_alias, $prod_nuc_id, $prod_alias, $is_first,
          $INPUT['OUTPUT_USE_ALIAS'], $match_aux, $max_id, $num_files, $num_match_row,
          $DIRECTORIES['MATCH_FILE_PATH'], $MAX_NUM_TABLE_FILE_ROWS );

      // attach index to match
      $match['index'] = $match_aux['index'];

      // move on to next match
      $last_match = $match;
      $is_first = false;

    } else {

      // determine presence of (non-terminal) gap and ouput row for `gap` table, if any
      process_gap_coverage( $match, $last_match, $prec_nuc_id, $prec_alias, $prod_nuc_id,
          $prod_alias, 0, $INPUT['OUTPUT_USE_ALIAS'], $gap_aux, $max_id, $num_files, $covered,
          $INPUT['GAP_MIN_LENGTH'], $DIRECTORIES['GAP_FILE_PATH'], $MAX_NUM_TABLE_FILE_ROWS );

      // ouput row for `match` table
      output_pre_match_row( $match, $prec_nuc_id, $prec_alias, $prod_nuc_id, $prod_alias, $is_first,
          $INPUT['OUTPUT_USE_ALIAS'], $match_aux, $max_id, $num_files, $num_match_row,
          $DIRECTORIES['MATCH_FILE_PATH'], $MAX_NUM_TABLE_FILE_ROWS );

      // attach index to match
      $match['index'] = $match_aux['index'];

      // compute and output row for `pointer` table, if any
      if ( $INPUT['COMPUTE_POINTERS'] === true ) {

        process_pointer( $match, $last_match, $prec_nuc_id, $prec_alias, $prod_nuc_id, $prod_alias,
            $INPUT['OUTPUT_USE_ALIAS'], $ptr_aux, $max_id, $num_files, $match_aux,
            $INPUT['POINTER_MIN_LENGTH'], $DIRECTORIES['POINTER_FILE_PATH'],
            $MAX_NUM_TABLE_FILE_ROWS );

      }

      // move on to next match
      $last_match = $match;

    }

  }

  // determine presence of (right-)terminal gap and output row for `gap` table, if any
  $dummy_match = array( "prod_start" => $inter_tel_interval['end'] + 1 );
  process_gap_coverage( $dummy_match, $last_match, $prec_nuc_id, $prec_alias, $prod_nuc_id,
      $prod_alias, 1, $INPUT['OUTPUT_USE_ALIAS'], $gap_aux, $max_id, $num_files, $covered,
      $INPUT['GAP_MIN_LENGTH'], $DIRECTORIES['GAP_FILE_PATH'], $MAX_NUM_TABLE_FILE_ROWS );

  // output row for `coverage` table
  output_coverage_row( $covered, $inter_tel_interval, $prec_nuc_id, $prod_nuc_id, $cov_aux, $max_id,
      $num_files, $DIRECTORIES['COVERAGE_FILE_PATH'], $MAX_NUM_TABLE_FILE_ROWS );

  // close files, if necessary
  if ( $match_aux['file'] !== NULL ) { fclose( $match_aux['file'] ); }
  if ( $match_aux['tmp_file'] !== NULL ) { fclose( $match_aux['tmp_file'] ); }
  if ( $gap_aux['file'] !== NULL ) { fclose( $gap_aux['file'] ); }
  if ( $ptr_aux['file'] !== NULL ) { fclose( $ptr_aux['file'] ); }
  if ( $cov_aux['file'] !== NULL ) { fclose( $cov_aux['file'] ); }

  return true;

}

?>