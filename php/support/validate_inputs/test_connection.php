<?php

// tests database connection
function test_connection( array $INPUT, &$LINK, array &$ERRORS ) {

  $LINK = mysqli_connect( $INPUT['HOSTNAME'], $INPUT['USERNAME'], $INPUT['PASSWORD'] );
  if ( $LINK ) {

    if ( mysqli_num_rows( mysqli_query( $LINK, "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$INPUT['DATABASE']}';" ) ) > 0 ) {
      mysqli_select_db( $LINK, $INPUT['DATABASE'] );
    }

  } else {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

  return true;

}

?>