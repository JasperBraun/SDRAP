<?php

// outputs properties of arrangements between sequences with sufficient coverage of the product
function output_properties( $OUTPUT_USE_ALIAS, $OUTPUT_MIN_COVERAGE, array &$ERRORS, $LINK,
    array $DIRECTORIES ) {

  // get properties from the `properties` table
  $properties_table = mysqli_query( $LINK,
      "SELECT P.`prec_nuc_id`, A_PREC.`alias` AS `prec_alias`,
          P.`prod_nuc_id`, A_PROD.`alias` AS `prod_alias`, C.`coverage`,
          P.`preliminary_match_number`, P.`total_match_number`, P.`non_gapped`,
          P.`non_overlapping`, P.`non_repeating`, P.`exceeded_clique_limit`, P.`weakly_complete`,
          P.`strongly_complete`, P.`weakly_consecutive`, P.`strongly_consecutive`,
          P.`weakly_ordered`, P.`strongly_ordered`, P.`weakly_non_scrambled`,
          P.`strongly_non_scrambled`
      FROM `properties` AS P
      LEFT JOIN `coverage` AS C
      ON P.`prec_nuc_id` = C.`prec_nuc_id`
      AND P.`prod_nuc_id` = C.`prod_nuc_id`
      LEFT JOIN `alias` AS A_PREC
      ON P.`prec_nuc_id` = A_PREC.`nuc_id`
      LEFT JOIN `alias` AS A_PROD
      ON P.`prod_nuc_id` = A_PROD.`nuc_id`
      WHERE C.`coverage` >= '{ $OUTPUT_MIN_COVERAGE }'
      AND A_PREC.`is_primary` = 1
      AND A_PROD.`is_primary` = 1;"
  );
  if ( $properties_table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
        " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // output properties in tsv format with header
  $properties_file = fopen( $DIRECTORIES['PROPERTIES_FILE'], "w" );
  $header = array( "prec_id", "prod_id", "coverage", "preliminary_match_number",
      "total_match_number", "non_gapped", "non_overlapping", "non_repeating",
      "exceeded_clique_limit", "weakly_complete", "strongly_complete",
      "weakly_consecutive", "strongly_consecutive", "weakly_ordered",
      "strongly_ordered", "weakly_scrambled", "strongly_scrambled"
  );
  fwrite( $properties_file, implode( "\t", $header ) );
  while ( $table_row = mysqli_fetch_assoc( $properties_table ) ) {

    $prec_id = $OUTPUT_USE_ALIAS === true ? $table_row['prec_nuc_id'] : $table_row['prec_alias'];
    $prod_id = $OUTPUT_USE_ALIAS === true ? $table_row['prod_nuc_id'] : $table_row['prod_alias'];

    if ( $table_row['weakly_non_scrambled'] === 0 && $table_row['strongly_non_scrambled'] === 0 ) {
      $strongly_scrambled = 1;
      $weakly_scrambled = 1;
    } else if ( $table_row['weakly_non_scrambled'] === 1 && $table_row['strongly_non_scrambled'] === 0 ) {
      $strongly_scrambled = 0;
      $weakly_scrambled = 1;
    } else {
      $strongly_scrambled = 0;
      $weakly_scrambled = 0;
    }

    $file_row = array( $prec_id, $prod_id, $table_row['coverage'],
      $table_row['preliminary_match_number'], $table_row['total_match_number'],
      $table_row['non_gapped'], $table_row['non_overlapping'], $table_row['non_repeating'],
      $table_row['exceeded_clique_limit'], $table_row['weakly_complete'],
      $table_row['strongly_complete'], $table_row['weakly_consecutive'],
      $table_row['strongly_consecutive'], $table_row['weakly_ordered'],
      $table_row['strongly_ordered'], $weakly_scrambled, $strongly_scrambled
    );

    fwrite( $properties_file, "\n" . implode( "\t", $file_row ) );

  }
  mysqli_free_result( $properties_table );
  fclose( $properties_file );

  return true;

}

?>