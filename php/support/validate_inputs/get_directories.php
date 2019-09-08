<?php

// gets directories for all input, temporary and output files
function get_directories( $INPUT, $BASE_DIRECTORY ) {

  return array(
      "PREC_SEQUENCE_INPUT_FILE" => $BASE_DIRECTORY . "input/precursor/" .
          $INPUT['PRECURSOR_FILENAME'],
      "PROD_SEQUENCE_INPUT_FILE" => $BASE_DIRECTORY . "input/product/" . $INPUT['PRODUCT_FILENAME'],
      "BLAST_PREC_SEQUENCE_FILE" => $BASE_DIRECTORY . "tmp/" . $INPUT['DATABASE'] .
          "-precursor.fna",
      "BLAST_PROD_SEQUENCE_FILE" => $BASE_DIRECTORY . "tmp/" . $INPUT['DATABASE'] . "-product.fna",
      "NUCLEOTIDE_FILE_PATH" => $BASE_DIRECTORY . "tmp/" . $INPUT['DATABASE'] . "-nucleotide_",
      "ALIAS_FILE_PATH" => $BASE_DIRECTORY . "tmp/" . $INPUT['DATABASE'] . "-alias_",
      "TELOMERE_FILE_PATH" => $BASE_DIRECTORY . "tmp/" . $INPUT['DATABASE'] . "-telomere_",
      "BLAST_OUTPUT_FILE" => $BASE_DIRECTORY . "tmp/" . $INPUT['DATABASE'] . "-hsp.csv",
      "HSP_FILE_PATH" => $BASE_DIRECTORY . "tmp/" . $INPUT['DATABASE'] . "-hsp_",
      "MATCH_FILE_PATH" => $BASE_DIRECTORY . "tmp/" . $INPUT['DATABASE'] . "-match_",
      "TMP_MATCH_FILE_PATH" => $BASE_DIRECTORY . "tmp/" . $INPUT['DATABASE'] . "-tmp_match_",
      "GAP_FILE_PATH" => $BASE_DIRECTORY . "tmp/" . $INPUT['DATABASE'] . "-gap_",
      "COVERAGE_FILE_PATH" => $BASE_DIRECTORY . "tmp/" . $INPUT['DATABASE'] . "-coverage_",
      "POINTER_FILE_PATH" => $BASE_DIRECTORY . "tmp/" . $INPUT['DATABASE'] . "-pointer_",
      "PROPERTIES_FILE_PATH" => $BASE_DIRECTORY . "tmp/" . $INPUT['DATABASE'] . "-properties_",
      "ALIAS_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] . "/" .
          $INPUT['DATABASE'] . "_aliases.tsv",
      "NUCLEOTIDE_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] . "/" .
          $INPUT['DATABASE'] . "_all_nucleotide.fasta",
      "PREC_NUCLEOTIDE_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] . "/" .
          $INPUT['DATABASE'] . "_prec_nucleotide.fasta",
      "PROD_NUCLEOTIDE_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] . "/" .
          $INPUT['DATABASE'] . "_prod_nucleotide.fasta",
      "PREC_SEGMENTS_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] . "/" .
          $INPUT['DATABASE'] . "_prec_intervals.bed",
      "PREC_FRAGMENTS_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] . "/" .
          $INPUT['DATABASE'] . "_prec_fragments.bed",
      "PREC_POINTERS_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] . "/" .
          $INPUT['DATABASE'] . "_prec_pointers.bed",
      "PREC_HSP_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] . "/" .
          $INPUT['DATABASE'] . "_prec_hsp.bed",
      "PROD_SEGMENTS_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] . "/" .
          $INPUT['DATABASE'] . "_prod_intervals.bed",
      "GAPS_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] . "/" .
          $INPUT['DATABASE'] . "_gaps.bed",
      "PROD_POINTERS_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] . "/" .
          $INPUT['DATABASE'] . "_prod_pointers.bed",
      "PROD_HSP_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] . "/" .
          $INPUT['DATABASE'] . "_prod_hsp.bed",
      "PROPERTIES_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] . "/" .
          $INPUT['DATABASE'] . "_properties.tsv",
      "SUMMARY_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] . "/" .
          $INPUT['DATABASE'] . "_summary.tsv",
      "PREC_SEGMENTS_COMPLEMENT_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] .
          "/" . $INPUT['DATABASE'] . "_prec_eliminated_sequences.bed",
      "PARAMETERS_FILE" => $BASE_DIRECTORY . "/annotations/" . $INPUT['DATABASE'] . "/" .
          $INPUT['DATABASE'] . "_parameters.tsv"
  );

}

?>