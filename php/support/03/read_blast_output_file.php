<?php

// load helper functions
require_once( __DIR__ . "/../write_table_row_into_tsv.php" );

// parses blast output and outputs contents of `hsp` table
function read_blast_output_file( $HSP_MIN_LENGTH, array &$ERRORS, array $DIRECTORIES, $MAX_NUM_TABLE_FILE_ROWS, $blast_output_file ) {

  $result = array(
    "success" => true,
    "num_hsp_files" => 0
  );

  // prepare file for contents of `hsp` table for bulk upload
  $num_hsp_files = 0;
  $hsp_file = NULL;

  // initialize variables
  $num_hsp_row = 0;
  $hsp_id = 0;

  while ( ( $blast_output_line = fgets( $blast_output_file ) ) !== false ) {

    // parse line
    $hsp_values = explode ( ",", trim ( $blast_output_line ) );
    $hsp_keys = array(
      "prod_nuc_id",
      "prec_nuc_id",
      "pident",
      "length",
      "mismatches",
      "gaps",
      "prod_coordinate_1",
      "prod_coordinate_2",
      "prec_coordinate_1",
      "prec_coordinate_2",
      "evalue",
      "bitscore"
    );
    $blast_hsp = array_combine( $hsp_keys, $hsp_values );

    if ( intval( $blast_hsp['length'] ) >= $HSP_MIN_LENGTH ) {

      // build row for `hsp` table
      $prod_start = min( $blast_hsp['prod_coordinate_1'], $blast_hsp['prod_coordinate_2'] );
      $prod_end = max( $blast_hsp['prod_coordinate_1'], $blast_hsp['prod_coordinate_2'] );
      $prec_start = min( $blast_hsp['prec_coordinate_1'], $blast_hsp['prec_coordinate_2'] );
      $prec_end = max( $blast_hsp['prec_coordinate_1'], $blast_hsp['prec_coordinate_2'] );
      $prod_orientation = $blast_hsp['prod_coordinate_1'] - $blast_hsp['prod_coordinate_2'];
      $prec_orientation = $blast_hsp['prec_coordinate_1'] - $blast_hsp['prec_coordinate_2'];
      $orientation = ( ( $prod_orientation < 0 ) xor ( $prec_orientation < 0 ) ) ? '-' : '+';

      // output entry for `hsp` table
      $hsp_id += 1;
      $hsp_row = array(
        $hsp_id, // `hsp_id`
        1, // `org_id`
        $blast_hsp['prec_nuc_id'], // `prec_nuc_id`
        $prec_start, // `prec_start`
        $prec_end, // `prec_end`
        $blast_hsp['prod_nuc_id'], // `prod_nuc_id`
        $prod_start, // `prod_start`
        $prod_end, // `prod_end`
        $orientation, // `orientation`
        $blast_hsp['length'], // `length`
        $blast_hsp['pident'], // `pident`
        $blast_hsp['mismatches'], // `mismatch`
        $blast_hsp['evalue'], // `evalue`
        $blast_hsp['bitscore'] // `bitscore`
      );
      $output_result = write_table_row_into_tsv( $hsp_row, $num_hsp_row, $hsp_file, $DIRECTORIES['HSP_FILE_PATH'], $num_hsp_files, $MAX_NUM_TABLE_FILE_ROWS );
      if ( $output_result === false ) {
        $ERRORS['other'][] = "Error while writing table data into file in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
        $result['success'] = false;
        return $result;
      }

    }

  }

  if ( $hsp_file !== NULL ) {
    fclose( $hsp_file );
  }

  $result['num_hsp_files'] = $num_hsp_files;
  return $result;

}

?>