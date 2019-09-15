<?php

//load helper functions
require_once( __DIR__ . "/read_sequence.php" );
require_once( __DIR__ . "/telomere_helper.php" );
require_once( __DIR__ . "/../write_table_row_into_tsv.php" );

// reads input sequence file and ouputs into blast_sequence_file
// and `nucleotide` and `telomere` table content files
function read_input_sequence_file( array $INPUT, array &$ERRORS, $MAX_NUM_TABLE_FILE_ROWS, $nucleus, $max_nuc_id, $max_alias_id, $input_sequence_file, $blast_sequence_file, array $file_paths ) {

  $result = array(
    "success" => true,
    "max_nuc_id" => $max_nuc_id,
    "max_alias_id" => $max_alias_id,
    "num_nuc_files" => 0,
    "num_alias_files" => 0,
    "num_tel_files" => 0
  );

  // prepare file for contents of `nucleotide` table for bulk upload
  $num_nuc_files = 0;
  $nucleotide_file = NULL;

  // prepare file for contents of `alias` table for bulk upload
  $num_alias_files = 0;
  $alias_file = NULL;

  // prepare file for contents of `telomere` table for bulk upload
  $num_tel_files = 0;
  $telomere_file = NULL;

  // initialize variables
  $last = false;
  $num_nuc_row = 0;
  $num_alias_row = 0;
  $num_tel_row = 0;
  $nuc_id = $max_nuc_id;
  $feat_id = $nucleus === "PRODUCT" ? '2' : '1';
  $first_line = fgets( $input_sequence_file );
  $delim = $nucleus === "PRODUCT" ? $INPUT['PRODUCT_DELIMITER'] : $INPUT['PRECURSOR_DELIMITER'];

  // handle bad input
  if ( $first_line === false ) {

    $ERRORS['other'][] = "Input file " . $file_paths['input_sequence_file_name'] . " seems to be empty in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    $result['success'] = false;
    return $result;

  } elseif ( substr($first_line, 0, 1) !== ">" ) {

    $ERRORS['other'][] = "Input file " . $file_paths['input_sequence_file_name'] . " seems to be in the wrong format; '>' expected as first character in description lines of FASTA files in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    $result['success'] = false;
    return $result;

  } else {

    // initialize more variables
    if ( $delim === "" ) {
      $current_header = array( trim( substr($first_line, 1) ) );
    } else {
      $current_header = array_map( 'trim', explode( $delim, substr($first_line, 1) ) );
    }
    $nuc_id += 1;

  }

  // read input sequence file line by line
  while ( ! $last ) {

    $sequence_result = read_sequence( $input_sequence_file, $delim );
    if ( $sequence_result['length'] > 0 ) {

      // compute telomeres, if product sequence
      if ( $nucleus === "PRODUCT" ) {

        // find and mask telomeres
        $telomeres = get_telomeres( $INPUT, $sequence_result );

        // output entry for `telomere` table
        $five_length = $telomeres['left_telo']['length'];
        $three_length = $telomeres['right_telo']['length'];
        $tel_row = array(
          $nuc_id, // `tel_id` // should always be the same as `nuc_id`
          $nuc_id, // `nuc_id`
          $five_length === 0 ? 0 : $telomeres['left_telo']['start'] + 1, // `five_start`
          $five_length, // `five_length`
          $three_length === 0 ? $sequence_result['length'] + 1 : $telomeres['right_telo']['start'] + 1, // `three_start`
          $telomeres['right_telo']['length'] // `three_length`
        );
        write_table_row_into_tsv( $tel_row, $num_tel_row, $tel_file, $file_paths['telomere_file_path'], $num_tel_files, $MAX_NUM_TABLE_FILE_ROWS );

      }

      // add sequence to blast sequence file
      fwrite ( $blast_sequence_file, ">{$nuc_id}\n" . chunk_split ( $sequence_result['sequence'], 60, "\n" ) );

      // output entry for `nucleotide` table
      $nuc_row = array(
        $nuc_id, // `nuc_id`
        $feat_id, // `feat_id`
        $sequence_result['length'], // `length`
        $sequence_result['sequence'] // `sequence`
      );
      write_table_row_into_tsv( $nuc_row, $num_nuc_row, $nucleotide_file, $file_paths['nucleotide_file_path'], $num_nuc_files, $MAX_NUM_TABLE_FILE_ROWS );

      // output entries for `alias` table
      foreach ( $current_header as $key => $alias ) {

        $result['max_alias_id'] += 1;
        $alias_row = array(
          $result['max_alias_id'], // `alias_id`
          $nuc_id, // `nuc_id`
          $alias, // `alias`
          $key === 0 ? 1 : 0 // `is_primary`
        );
        write_table_row_into_tsv( $alias_row, $num_alias_row, $alias_file, $file_paths['alias_file_path'], $num_alias_files, $MAX_NUM_TABLE_FILE_ROWS );

      }

    } else {

      $ERRORS['other'][] = "Empty sequence for " . $current_header[0] . " read in input file " . $file_paths['input_sequence_file_name'] . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";

    }
    $last = $sequence_result['last'];
    $current_header = $sequence_result['next_header'];
    $nuc_id += 1;

  }

  // close files
  if ( $nucleotide_file !== NULL ) {
    fclose( $nucleotide_file );
  }
  if ( $alias_file !== NULL ) {
    fclose( $alias_file );
  }
  if ( $telomere_file !== NULL ) {
    fclose( $telomere_file );
  }

  $result['max_nuc_id'] = $nuc_id - 1;
  $result['num_nuc_files'] = $num_nuc_files;
  $result['num_alias_files'] = $num_alias_files;
  $result['num_tel_files'] = $num_tel_files;

  return $result;

}

?>