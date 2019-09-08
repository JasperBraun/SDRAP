<?php

// gets aliases for quick access
function get_aliases( array &$nuc_id_to_alias, array &$ERRORS, $LINK ) {

  $alias_table = mysqli_query( $LINK,
    "SELECT `nuc_id`, `alias` FROM `alias` WHERE `is_primary` = 1;"
  );
  if ( $alias_table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  while ( $row = mysqli_fetch_assoc( $alias_table ) ) {
    $nuc_id_to_alias[ $row['nuc_id'] ] = trim( $row['alias'] );
  }

  return true;

}

?>