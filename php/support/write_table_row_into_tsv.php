<?php

// writes a table row represented by an array into tsv file
// splitting output into files of specified number of rows
function write_table_row_into_tsv( $row, &$num_row, &$file_handle, $file_path, &$num_files, $MAX_NUM_TABLE_FILE_ROWS ) {

  $num_row += 1;
  if ( $num_row > $MAX_NUM_TABLE_FILE_ROWS * $num_files || $num_files === 0 || $file_handle === NULL ) {

    if ( $num_files !== 0 && $file_handle !== NULL ) {
      fclose( $file_handle );
    }
    if ( $num_row > $MAX_NUM_TABLE_FILE_ROWS * $num_files ) {
      $num_files += 1;
    }
    $new_file_name = $file_path . strval($num_files) . ".tsv";
    $file_handle = fopen( $new_file_name, "a" );
    if ( $file_handle === false ) {
      $ERRORS['other'][] = "Could not open file " . $new_file_name . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
      return false;
    }

  }
  fputcsv( $file_handle, $row, "\t", chr(0) );

  return true;

}

?>