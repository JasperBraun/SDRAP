<?php

// load helper functions
require_once( __DIR__ . "/../write_table_row_into_tsv.php" );

// outputs rows for additional matches and fragments for `match` table
function output_add_matches( array &$add_match_array, $prec_nuc_id, $prec_alias, $prod_nuc_id,
    $prod_alias, $OUTPUT_USE_ALIAS, array &$max_id, &$match_file, array &$num_files,
    &$num_match_row, $MATCH_FILE_PATH, $MAX_NUM_TABLE_FILE_ROWS ) {

  foreach ( $add_match_array as $match ) {

    $prec_id = $OUTPUT_USE_ALIAS === true ? $prec_nuc_id : $prec_alias;
    $prod_id = $OUTPUT_USE_ALIAS === true ? $prod_nuc_id : $prod_alias;

    // determine type
    $type = $match['is_fragment'] === true ? "frag" : "match";
    $prefix = $match['is_fragment'] === true ? "" : "add_";

    // determine index
    $index_prefix = ($match['orientation'] === "+" || $match['index']  === 0) ? "" : "-";
    $index = $index_prefix . strval( $match['index'] );

    // (add_)[type]_[prod_id]_[prod_start]_[prod_end]_[index]_[pre_cov]_[add_cov]
    $prec_segment_alias = $prefix . implode( "_", array_map( "strval", array(
        $type, $prod_id, $match['prod_start'], $match['prod_end'], $index, $match['pre_cov'],
        $match['add_cov']
    ) ) );

    // (add_)[type]_[prec_id]_[prec_start]_[prec_end]_[index]_[pre_cov]_[add_cov]
    $prod_segment_alias = $prefix . implode( "_", array_map( "strval", array(
        $type, $prec_id, $match['prec_start'], $match['prec_end'], $index, $match['pre_cov'],
        $match['add_cov']
    ) ) );

    // build `match` table row
    $max_id['match'] += 1;
    $match_row = array(
      $max_id['match'], // `match_id`
      $prec_nuc_id, // `prec_nuc_id`
      $prod_nuc_id, // `prod_nuc_id`
      $match['prec_start'], // `prec_start`
      $match['prec_end'], // `prec_end`
      $match['prod_start'], // `prod_start`
      $match['prod_end'], // `prod_end`
      $match['orientation'], // `orientation`
      $match['length'], // `length`
      implode( "_", array_map( 'strval', $match['hsp_id'] ) ), // `hsp_id`
      $index, // `index`
      $match['pre_cov'], // `pre_cov`
      $match['add_cov'], // `add_cov`
      0, // `is_preliminary`
      1, // `is_additional`
      $type === "frag" ? 1 : 0, // `is_fragment`
      $prec_segment_alias, // `prec_segment_alias`
      $prod_segment_alias // `prod_segment_alias`
    );

    write_table_row_into_tsv( $match_row, $num_match_row, $match_file, $MATCH_FILE_PATH, $num_files['match'], $MAX_NUM_TABLE_FILE_ROWS );

  }

  return true;

}

?>