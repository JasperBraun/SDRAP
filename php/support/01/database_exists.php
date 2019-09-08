<?php

// checks whether database already exists
function database_exists( $DATABASE, array &$ERRORS, $LINK ) {

  $database_matches = mysqli_query ( $LINK, "SHOW DATABASES LIKE '{$DATABASE}';" );

  if ( $database_matches === false ) {

    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return "failure";

  } elseif ( mysqli_num_rows( $database_matches ) > 0 ) {

    return true;

  }

  return false;

}

?>