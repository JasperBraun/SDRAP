<?php

// reads in the preliminary matches corresponding to the current precursor
function read_pre_matches( $file_name, array &$pre_match_array, array &$pre_match_hsp_id_array ) {

  if ( file_exists( $file_name ) ) {

    $file_handle = fopen( $file_name, "r" );
    $keys = array( "prec_nuc_id", "prod_nuc_id", "prod_start", "prod_end", "length", "index",
        "hsp_id" );
    while ( ( $line = fgets( $file_handle ) ) !== false ) {

      // parse line
      $values = explode( "\t", $line );
      $match = array_combine( $keys, $values );
      $match['prod_start'] = (int)$match['prod_start'];
      $match['prod_end'] = (int)$match['prod_end'];
      $match['length'] = (int)$match['length'];
      $match['index'] = (int)$match['index'];
      $match['hsp_id'] = explode( "_", $match['hsp_id'] );

      $pre_match_array[] = $match;
      foreach ( $match['hsp_id'] as $hsp_id ) {
        $pre_match_hsp_id_array[ trim( $hsp_id ) ] = true;
      }

    }

    fclose( $file_handle );

  }

  return true;

}

?>