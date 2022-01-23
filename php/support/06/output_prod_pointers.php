<?php

// output product pointers of matches between sequences with sufficient coverage of the product
function output_prod_pointers( $OUTPUT_USE_ALIAS, $OUTPUT_MIN_COVERAGE, array &$ERRORS, $LINK,
    array $DIRECTORIES ) {

  // get pointers from the `pointer` table
  $prod_pointers_table = mysqli_query( $LINK,
      "SELECT P.`prod_nuc_id`, A.`alias` AS `prod_alias`, P.`prod_start` - 1 AS `prod_start`, P.`prod_end` - 1 AS `prod_end`,
          P.`prod_alias` AS `ptr_alias`, C.`coverage`, O.`non_gapped`,
          O.`exceeded_clique_limit`, O.`weakly_non_scrambled`, O.`strongly_non_scrambled`
      FROM `pointer` AS P
      LEFT JOIN `coverage` AS C
      ON P.`prec_nuc_id` = C.`prec_nuc_id`
      AND P.`prod_nuc_id` = C.`prod_nuc_id`
      LEFT JOIN `alias` AS A
      ON P.`prod_nuc_id` = A.`nuc_id`
      LEFT JOIN `properties` AS O
      ON P.`prec_nuc_id` = O.`prec_nuc_id`
      AND P.`prod_nuc_id` = O.`prod_nuc_id`
      WHERE C.`coverage` >= '{ $OUTPUT_MIN_COVERAGE }'
      AND A.`is_primary` = 1
      ORDER BY P.`prod_nuc_id`, P.`prod_start` ASC;"
  );
  if ( $prod_pointers_table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
        " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // output product annotation of pointers in BED format
  $prod_pointers_file = fopen( $DIRECTORIES['PROD_POINTERS_FILE'], "w" );
  fwrite( $prod_pointers_file, '#track name="prod_pointers" description="product pointers ' .
      'labelled ptr_[prec-id]_[left-flanking-match-index]_' . 
      '[left-flanking-match-ptr-prec-start]_[left-flanking-match-ptr-prec-end]_' . 
      '[right-flanking-match-index]_[right-flanking-match-ptr-prec-start]_' .
      '[right-flanking-match-ptr-prec-end]_[arrangement-coverage]_[non-gapped]_' .
      '[exceeded-clique-limit]_[weakly-non-scrambled]_[strongly-non-scrambled]" itemRgb-"On"' );
  while ( $pointer = mysqli_fetch_assoc( $prod_pointers_table ) ) {

    $prod_id = $OUTPUT_USE_ALIAS === true ? $pointer['prod_nuc_id'] : $pointer['prod_alias'];

    if ( $pointer['non_gapped'] === NULL ) {
      $suffix = "_" . $pointer['coverage'] . "_2_2_2_2";
    } else {
      $suffix = "_" . $pointer['coverage'] . "_" . $pointer['non_gapped'] . "_" .
          $pointer['exceeded_clique_limit'] . "_" . $pointer['weakly_non_scrambled'] . "_" .
          $pointer['strongly_non_scrambled'];
    }

    $prod_pointers_row = array( $prod_id, $pointer['prod_start'], $pointer['prod_end'],
        $pointer['ptr_alias'] . $suffix, "0", "+", $pointer['prod_start'], $pointer['prod_end'],
        "153,0,204" );

    fwrite( $prod_pointers_file, "\n" . implode( "\t", $prod_pointers_row ) );

  }
  mysqli_free_result( $prod_pointers_table );
  fclose( $prod_pointers_file );

  return true;

}

?>
