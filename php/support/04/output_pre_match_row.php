<?php

// load helper functions
require_once( __DIR__ . "/../write_table_row_into_tsv.php" );

// outputs row for `match` table
function output_pre_match_row( array $match, $prec_nuc_id, $prec_alias, $prod_nuc_id, $prod_alias,
    $is_first, $OUTPUT_USE_ALIAS, array &$match_aux, array &$max_id, array &$num_files,
    &$num_match_row, $MATCH_FILE_PATH, $MAX_NUM_TABLE_FILE_ROWS ) {

  // determine index
  $match_aux['index'] += 1;
  $index_prefix = $match['orientation'] === '+' ? "" : "-";
  $index = $index_prefix . strval( $match_aux['index'] );

  // determine aliases
  $prod_id = $OUTPUT_USE_ALIAS === true ? $prod_nuc_id : $prod_alias;
  $prec_id = $OUTPUT_USE_ALIAS === true ? $prec_nuc_id : $prec_alias;
  // pre_match_[prod_id]_[prod-start]_[prod-end]_[index]_[pre-cov]_[add-cov]
  $prec_segment_alias = implode( "_", array_map( "strval",
      array( "pre", "match", $prod_id, $match['prod_start'], $match['prod_end'], $index, 100.00,
          0.00 )
  ) );
  // pre_match_[prec_id]_[prec_start]_[prec_end]_[index]_[pre-cov]_[add-cov]
  $prod_segment_alias = implode( "_", array_map( "strval",
      array( "pre", "match", $prec_id, $match['prec_start'], $match['prec_end'], $index, 100.00,
          0.00 )
  ) );

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
    implode( "_", $match['hsp_id'] ), // `hsp_id`
    $index, // `index`
    100.00, // `pre_cov`
    0.00, // `add_cov`
    1, // `is_preliminary`
    0, // `is_additional`
    0, // `is_fragment`
    $prec_segment_alias, // `prec_segment_alias`
    $prod_segment_alias // `prod_segment_alias`
  );

  write_table_row_into_tsv( $match_row, $num_match_row, $match_aux['file'], $MATCH_FILE_PATH, $num_files['match'], $MAX_NUM_TABLE_FILE_ROWS );

  if ( !$is_first ) { fwrite( $match_aux['tmp_file'], "\n" ); }
  fwrite( $match_aux['tmp_file'], implode( "\t", array(
    $prec_nuc_id,
    $prod_nuc_id,
    (string)$match['prod_start'],
    (string)$match['prod_end'],
    (string)$match['length'],
    (string)$match_aux['index'],
    implode( "_", $match['hsp_id'] )
  ) ) );

  return true;

}

?>