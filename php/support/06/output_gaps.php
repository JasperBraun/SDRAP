<?php

// outputs product segments of matches between sequences with sufficient coverage of the product
function output_gaps( $OUTPUT_USE_ALIAS, $OUTPUT_MIN_COVERAGE, array &$ERRORS, $LINK,
    array $DIRECTORIES ) {

  // get gaps from the `gap` table
  $gaps_table = mysqli_query( $LINK,
      "SELECT G.`prod_nuc_id`, A.`alias` AS `prod_alias`, G.`start`, G.`end`, G.`index`,
          G.`gap_alias`, C.`coverage`, P.`non_gapped`, P.`exceeded_clique_limit`,
          P.`weakly_non_scrambled`, P.`strongly_non_scrambled`
      FROM `gap` AS G
      LEFT JOIN `coverage` AS C
      ON G.`prec_nuc_id` = C.`prec_nuc_id`
      AND G.`prod_nuc_id` = C.`prod_nuc_id`
      LEFT JOIN `alias` AS A
      ON G.`prod_nuc_id` = A.`nuc_id`
      LEFT JOIN `properties` AS P
      ON G.`prec_nuc_id` = P.`prec_nuc_id`
      AND G.`prod_nuc_id` = P.`prod_nuc_id`
      WHERE C.`coverage` >= '{ $OUTPUT_MIN_COVERAGE }'
      AND A.`is_primary` = 1
      ORDER BY G.`prod_nuc_id`, G.`start` ASC;"
  );
  if ( $gaps_table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
        " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // output product annotation of gaps in BED format
  $gaps_file = fopen( $DIRECTORIES['GAPS_FILE'], "w" );
  fwrite( $gaps_file, '#track name="gaps" description="gaps labelled ' .
      'gap_[prec-id]_[index]_[is-terminal]_[arrangement-coverage]_[non-gapped]_' .
      '[exceeded-clique-limit]_[weakly-non-scrambled]_[strongly-non-scrambled]" itemRgb-"On"' );
  while ( $gap = mysqli_fetch_assoc( $gaps_table ) ) {

    $prod_id = $OUTPUT_USE_ALIAS === true ? $gap['prod_nuc_id'] : $gap['prod_alias'];
    if ( $gap['non_gapped'] === NULL ) {
      $suffix = "_" . $gap['coverage'] . "_2_2_2_2";
    } else {
      $suffix = implode( "_", array(
          $gap['coverage'], $gap['non_gapped'], $gap['exceeded_clique_limit'],
          $gap['weakly_non_scrambled'], $gap['strongly_non_scrambled']
      ) );
    }

    $gaps_row = array( $prod_id, $gap['start'], $gap['end'], $gap['gap_alias'] . $suffix, "0", ".",
        $gap['start'], $gap['end'], "204,0,0" );

    fwrite( $gaps_file, "\n" . implode( "\t", $gaps_row ) );

  }
  mysqli_free_result( $gaps_table );
  fclose( $gaps_file );

  return true;

}

?>