<?php

// load helper functions
require_once( __DIR__ . "/../write_table_row_into_tsv.php" );

// checks if there's a gap between match and last match, and outputs it, if any; update coverage appropriately
function process_gap_coverage( array $match, array $last_match, $prec_nuc_id, $prec_alias,
    $prod_nuc_id, $prod_alias, $is_terminal, $OUTPUT_USE_ALIAS, array &$gap_aux, array &$max_id,
    array &$num_files, &$covered, $GAP_MIN_LENGTH, $GAP_FILE_PATH, $MAX_NUM_TABLE_FILE_ROWS ) {
  
  $gap_length = $match['prod_start'] - $last_match['prod_end'] - 1;
  if ( $gap_length >= $GAP_MIN_LENGTH ) {

    // determine alias
    $gap_aux['index'] += 1;
    $prec_id = $OUTPUT_USE_ALIAS === true ? $prec_nuc_id : $prec_alias;

    // gap_[prec_id]_[index]_[is-terminal]
    $gap_alias = implode( "_", array_map( 'strval',
        array( "gap", $prec_id, $gap_aux['index'], $is_terminal )
    ) );

    // build `gap` table row
    $max_id['gap'] += 1;
    $gap_row = array(
      $max_id['gap'], // `gap_id`
      $prec_nuc_id, // `prec_nuc_id`
      $prod_nuc_id, // `prod_nuc_id`
      $gap_aux['index'], // `index`
      $last_match['prod_end'] + 1, // `start`
      $match['prod_start'] - 1, // `end`
      $gap_length, // `length`
      $is_terminal, // `is_terminal`
      $gap_alias // `gap_alias`
    );

    write_table_row_into_tsv( $gap_row, $gap_aux['num_row'], $gap_aux['file'], $GAP_FILE_PATH, $num_files['gap'], $MAX_NUM_TABLE_FILE_ROWS );

    // update coverage
    $covered -= $gap_length;

  }

  return true;

}

?>