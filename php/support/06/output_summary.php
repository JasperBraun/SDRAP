<?php

// load helper functions
require_once( __DIR__ . "/add_telomere_data.php" );
require_once( __DIR__ . "/add_hsp_data.php" );
require_once( __DIR__ . "/add_match_data.php" );
require_once( __DIR__ . "/add_arr_data.php" );
require_once( __DIR__ . "/add_data.php" );

// outputs comprehensive summary of computational outcome
function output_summary( $total_time, array $INPUT, array &$ERRORS, $LINK, array $DIRECTORIES ) {

  // initialize variables
  // matches, fragments and arrangements labelled "high_cov" are between sequences with coverage
  // above threshold for property computation
  $descriptions = array(
      "telo_num_both", // number of product sequences with nonzero telomeres at both ends
      "telo_num_five", // number of product sequences with nonzero telomere only at 5' end
      "telo_num_three", // number of product sequences with nonzero telomere only at 3' end
      "telo_num_neither", // number of product sequences with nonzero telomeres at neither end
      "telo_num", // total number of nonzero telomeres detected
      "telo_avg_length", // avg length of nonzero telomeres
      "telo_avg_length_five", // avg length of nonzero telomeres at 5' ends
      "telo_avg_length_three", // avg length of nonzero telomeres at 3' ends
      "telo_min_length", // minimum length of detected nonzero telomeres
      "telo_max_length", // maximum length of detected nonzero telomeres
      "telo_num_min_length", // number of telomeres at minimum length
      "telo_num_max_length", // number of telomeres at maximum length
      "telo_max_offset", // maximum offset of detected nonzero telomeres
      "hsp_num", // total number of hsp's long enough to be uploaded into database
      "hsp_avg_length", // avg length of hsp's in database
      "hsp_min_length", // minimum length of hsp's in database
      "hsp_max_length", // maximum length of hsp's in database
      "hsp_avg_bitscore", // avg bitscore of hsp's in database
      "hsp_min_bitscore", // minimum bitscore of hsp's in database
      "hsp_max_bitscore", // maximum bitscore of hsp's in database
      "hsp_num_bitscore_pre", // number of hsp's with bitscore sufficient for preliminary matches
      "hsp_num_bitscore_add", // number of hsp's with bitscore sufficient for additional matches
      "hsp_avg_pident", // avg pident of hsp's in database
      "hsp_min_pident", // minimum pident of hsp's in database
      "hsp_max_pident", // maximum pident of hsp's in database
      "hsp_num_pident_pre", // number of hsp's with pident sufficient for preliminary matches
      "hsp_num_pident_add", // number of hsp's with pident sufficient for additional matches
      "match_num", // total number of matches
      "match_num_high_cov", // number of matches in high cov arrangements
      "match_pre_num", // total number of preliminary matches
      "match_pre_num_high_cov", // number of preliminary matches in high cov arrangements
      "match_frag_num", // total number of fragments
      "match_frag_num_high_cov", // number of fragments between sequences with high cov
      "match_frag_zero_num", // number of fragments with index 0
      "match_frag_zero_num_high_cov", // number of fragments with index 0 between sequences with
                                      // high cov
      "match_merged_num", // number of merged matches
      "match_merged_num_high_cov", // number of merged matches in high cov arrangements
      "match_ptr_num", // number of pointers
      "match_ptr_num_high_cov", // number of pointers in high cov arrangements
      "match_gap_num", // number of gaps
      "match_gap_num_high_cov", // number of gaps in high cov arrangements
      "match_terminal_gap_num", // number of terminal gaps
      "match_terminal_gap_num_high_cov", // number of terminal gaps between high cov sequences
      "arr_num", // number of arrangements
      "arr_num_high_cov", // number of high cov arrangements
      "arr_avg_cov", // avg coverage of arrangements
      "arr_avg_cov_high_cov", // avg coverage of high cov arrangements
      "arr_num_gapped", // number of arrangements with gaps
      "arr_num_gapped_high_cov", // number of high cov arrangements with gaps
      "arr_num_terminal_gap", // number of arrangements with terminal gaps
      "arr_num_terminal_gap_high_cov", // number of high cov arrangements with terminal gaps
      "arr_avg_match", // avg number of matches per arrangement
      "arr_avg_match_high_cov", // avg number of matches per high cov arrangement
      "arr_avg_pre_match", // avg number of preliminary matches per arrangement
      "arr_avg_pre_match_high_cov", // avg number of preliminary matches per high cov arrangement
      "arr_avg_ptr", // avg number of pointers per arrangement
      "arr_avg_ptr_high_cov", // avg number of pointers per high cov arrangement
      "arr_num_repeat", // number of high cov arrangements with repeating matches
      "arr_num_overlap", // number of high cov arrangements with overlapping matches
      "arr_num_clique", // number of high cov arrangements without overlaps and repeats
      "arr_num_clique_limit", // number of high cov arrangements exceeding clique limit
      "arr_num_weakly_complete", // number of weakly complete high cov arrangements
      "arr_num_strongly_complete", // number of strongly complete high cov arrangements
      "arr_num_weakly_consecutive", // number of weakly consecutive high cov arrangements
      "arr_num_strongly_consecutive", // number of strongly consecutive high cov arrangements
      "arr_num_weakly_ordered", // number of weakly ordered high cov arrangements
      "arr_num_strongly_ordered", // number of strongly ordered high cov arrangements
      "arr_num_weakly_scrambled", // number of weakly scrambled high cov arrangements
      "arr_num_strongly_scrambled", // number of strongly scrambled high cov arrangements
      "arr_num_output" // number of arrangements with sufficient coverage for output
  );
  $summary_data = array_flip( $descriptions );

  // fill summary_data with all data regarding telomeres
  $add_telomere_data_result = add_telomere_data( $summary_data, $INPUT, $ERRORS, $LINK );
  if ( $add_telomere_data_result === false ) {
    $ERRORS['other'][] = "Error while outputting telomere summary in " .
        basename(__FILE__,".php") . " near line " . __LINE__ . ".";
        return false;
  }

  // fill summary_data with all data regarding hsp's
  $add_hsp_data_result = add_hsp_data( $summary_data, $INPUT, $ERRORS, $LINK );
  if ( $add_hsp_data_result === false ) {
    $ERRORS['other'][] = "Error while outputting hsp summary in " .
        basename(__FILE__,".php") . " near line " . __LINE__ . ".";
        return false;
  }

  // fill summary_data with all data regarding matches
  $add_match_data_result = add_match_data( $summary_data, $INPUT, $ERRORS, $LINK );
  if ( $add_match_data_result === false ) {
    $ERRORS['other'][] = "Error while outputting match summary in " .
        basename(__FILE__,".php") . " near line " . __LINE__ . ".";
        return false;
  }

  // fill summary_data with all data regarding arrangements
  $add_arr_data_result = add_arr_data( $summary_data, $INPUT, $ERRORS, $LINK );
  if ( $add_arr_data_result === false ) {
    $ERRORS['other'][] = "Error while outputting arrangements summary in " .
        basename(__FILE__,".php") . " near line " . __LINE__ . ".";
        return false;
  }

  // ouput summary in tsv format with header
  $summary_file = fopen( $DIRECTORIES['SUMMARY_FILE'], "w" );
  $is_first = true;
  foreach ( $summary_data as $description => $value ) {
    if ( !$is_first ) { fwrite( $summary_file, "\n" ); }
    fwrite( $summary_file, $description . "\t" . strval( $value ) );
    $is_first = false;
  }
  fclose( $summary_file );

  return true;

}

?>