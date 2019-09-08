<?php

// outputs aliases for reference
function output_aliases( $OUTPUT_PRECURSOR_DELIMITER, $OUTPUT_PRODUCT_DELIMITER, array &$ERRORS,
    $LINK, array $DIRECTORIES ) {

  // get nuc_id's and their aliases and output correspondence in tsv format
  $alias_table = mysqli_query( $LINK,
    "SELECT A.`nuc_id`, A.`alias`, N.`feat_id`
    FROM `alias` AS A
    LEFT JOIN `nucleotide` AS N
    ON A.`nuc_id` = N.`nuc_id`
    ORDER BY A.`nuc_id` ASC;"
  );
  if ( $alias_table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
        " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  $alias_file = fopen( $DIRECTORIES['ALIAS_FILE'], "w" );

  $nuc_id = "";
  $alias = array();
  $is_first = true;
  while ( $alias_row = mysqli_fetch_assoc( $alias_table ) ) {

    if ( $alias_row['nuc_id'] !== $nuc_id ) {

      if ( $nuc_id !== "" ) {
        if ( $is_first ) {
          fwrite( $alias_file, $nuc_id . "\t" . implode( $delim, $alias ) );
          $is_first = false;
        } else {
          fwrite( $alias_file, "\n" . $nuc_id . "\t" . implode( $delim, $alias ) );
        }
      }

      $nuc_id = $alias_row['nuc_id'];
      $alias = array( $alias_row['alias'] );
      $delim = $alias_row['feat_id'] = 1 ? $OUTPUT_PRECURSOR_DELIMITER : $OUTPUT_PRODUCT_DELIMITER;

    } else {
      $alias[] = $alias_row['alias'];
    }

  }
  fwrite( $alias_file, "\n" . $nuc_id . "\t" . implode( $delim, $alias ) );

  mysqli_free_result( $alias_table );
  fclose( $alias_file );

  return true;

}

?>