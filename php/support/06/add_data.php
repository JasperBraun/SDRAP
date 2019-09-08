<?php

// extracts the data obtainable via the given query assumed to result in a single row, single
// field table and assigns it to the given data variable
function add_data( &$data, $query, array &$ERRORS, $LINK ) {

  $table = mysqli_query( $LINK, $query );
  if ( $table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " .
        mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ .
        ".";
    return false;
  }
  $table_row = mysqli_fetch_row( $table );
  $data = $table_row[0];
  mysqli_free_result( $table );

  return true;

}

?>