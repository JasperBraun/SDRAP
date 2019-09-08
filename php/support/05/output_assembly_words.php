<?php

// load helper functions
require_once( __DIR__ . "/../write_table_row_into_tsv.php" );

// outputs row for `assembly_word` table
function output_assembly_words( array &$assembly_words, array $pair, $exceeded_clique_limit, 
    array &$output_aux, $ASSEMBLY_WORD_FILE_PATH, $MAX_NUM_TABLE_FILE_ROWS ) {

  foreach ( $assembly_words as $assembly_word_data ) {

    // convert arrays to strings
    $pointer_sequence = implode( "_",
        array_map( 'strval', $assembly_word_data['pointer_sequence'] )
    );
    $loop_reduced_pointer_sequence = implode( "_",
        array_map( 'strval', $assembly_word_data['loop_reduced_pointer_sequence'] )
    );
    $assembly_word = implode( "_",
        array_map( 'strval', $assembly_word_data['assembly_word'] )
    );
    $loop_reduced_assembly_word = implode( "_",
        array_map( 'strval', $assembly_word_data['loop_reduced_assembly_word'] )
    );

    // build `assembly_word` table row
    $output_aux['assembly_word_id'] += 1;
    $assembly_word_row = array(
        $output_aux['assembly_word_id'], // `assembly_word_id`
        $pair['prec_nuc_id'], // `prec_nuc_id`
        $pair['prod_nuc_id'], // `prod_nuc_id`
        $exceeded_clique_limit === true ? 1 : 0, // `exceeded_clique_limit`
        $pair['has_gap'] === NULL ? 1 : 0, // `non_gapped`
        $assembly_word_data['properties']['complete'] === true ? 1 : 0, // `complete`
        $assembly_word_data['properties']['consecutive'] === true ? 1 : 0, // `consecutive`
        $assembly_word_data['properties']['ordered'] === true ? 1 : 0, // `ordered`
        $assembly_word_data['properties']['non_scrambled'] === true ? 1 : 0, // `non_scrambled`
        $pointer_sequence, // `pointer_sequence`
        $loop_reduced_pointer_sequence, // `loop_reduced_pointer_sequence`
        $assembly_word, // `assembly_word`
        $loop_reduced_assembly_word // `loop_reduced_assembly_word`
    );

    write_table_row_into_tsv( $assembly_word_row, $output_aux['num_assembly_word_row'],
        $output_aux['assembly_word_file'], $ASSEMBLY_WORD_FILE_PATH,
        $output_aux['num_assembly_word_files'], $MAX_NUM_TABLE_FILE_ROWS );

  }
  
  return true;

}

?>