<?php

// uploads specified tsv data files into specified mysql table
// with given column_names and deletes files if desired
function upload_tsv_to_table( $table_name, array $column_names, $file_path, $num_files, $delete, $LINK, array &$ERRORS ) {

  if ( $num_files === 0 ) { return true; }

  // prepare for large bulk upload
  if ( mysqli_query( $LINK, "SET autocommit=0;" ) === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }
  if ( mysqli_query( $LINK, "SET unique_checks=0;" ) === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }
  if ( mysqli_query( $LINK, "SET foreign_key_checks=0;" ) === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

  $i = 1;
  while ( $i <= $num_files ) {

    $file_name = $file_path . strval($i) . '.tsv';

    // build query
    $query = "LOAD DATA LOCAL INFILE '" . "$file_name" . "' INTO TABLE `" . "$table_name" . "` (`";
    $j = 0;
    while ( $j < count($column_names) - 1 ) {
      $query .= $column_names[$j] . "`, `";
      $j += 1;
    }
    $query .= $column_names[$j] . "`);";

    // upload data
    if ( mysqli_query ( $LINK, $query ) === false ) {
      $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
      return false;
    }

    // delete file
    if ( $delete === true ) {
      unlink( $file_name );
    }

    $i += 1;

  }

  // reset settings
  if ( mysqli_query( $LINK, "SET foreign_key_checks=1;" ) === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }
  if ( mysqli_query( $LINK, "SET unique_checks=1;" ) === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }
  if ( mysqli_query( $LINK, "COMMIT;" ) === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

  return true;

}

?>