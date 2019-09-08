<?php

// sets file paths for nucleotide sequences and `nucleotide` and `telomere` table contents
function set_sequence_file_paths( array $DIRECTORIES, $nucleus, array &$file_paths ) {

  $file_paths['nucleotide_file_path'] = $DIRECTORIES['NUCLEOTIDE_FILE_PATH'];
  $file_paths['alias_file_path'] = $DIRECTORIES['ALIAS_FILE_PATH'];
  if ( $nucleus === "PRODUCT" ) {
    $file_paths['input_sequence_file_name'] = $DIRECTORIES['PROD_SEQUENCE_INPUT_FILE'];
    $file_paths['blast_sequence_file_name'] = $DIRECTORIES['BLAST_PROD_SEQUENCE_FILE'];
    $file_paths['telomere_file_path'] = $DIRECTORIES['TELOMERE_FILE_PATH'];
  } else {
    $file_paths['input_sequence_file_name'] = $DIRECTORIES['PREC_SEQUENCE_INPUT_FILE'];
    $file_paths['blast_sequence_file_name'] = $DIRECTORIES['BLAST_PREC_SEQUENCE_FILE'];
  }

  return true;

}

?>