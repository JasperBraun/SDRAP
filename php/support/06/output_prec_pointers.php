<?php

// outputw precursor pointers of matches between sequences with sufficient coverage of the product
function output_prec_pointers( $OUTPUT_USE_ALIAS, $OUTPUT_MIN_COVERAGE, array &$ERRORS, $LINK,
    array $DIRECTORIES ) {

  // get pointers from the `pointer` table
  $prec_pointers_table = mysqli_query( $LINK,
      "SELECT * FROM (
      SELECT P.`prec_nuc_id`, A.`alias` AS `prec_alias`, P.`prec_left_alias` AS `ptr_alias`,
          P.`left_prec_start` AS `start`, P.`left_prec_end` AS `end`,
          P.`left_match_orientation` AS `orientation`,
          C.`coverage`, O.`non_gapped`, O.`exceeded_clique_limit`,
          O.`weakly_non_scrambled`, O.`strongly_non_scrambled`
      FROM `pointer` AS P
      LEFT JOIN `coverage` AS C
      ON P.`prec_nuc_id` = C.`prec_nuc_id`
      AND P.`prod_nuc_id` = C.`prod_nuc_id`
      LEFT JOIN `alias` AS A
      ON P.`prec_nuc_id` = A.`nuc_id`
      LEFT JOIN `properties` AS O
      ON P.`prec_nuc_id` = O.`prec_nuc_id`
      AND P.`prod_nuc_id` = O.`prod_nuc_id`
      WHERE C.`coverage` >= '{ $OUTPUT_MIN_COVERAGE }'
      AND A.`is_primary` = 1
      UNION ALL
      SELECT P.`prec_nuc_id`, A.`alias` AS `prec_alias`, P.`prec_right_alias` AS `ptr_alias`,
          P.`right_prec_start` AS `start`, P.`right_prec_end` AS `end`,
          P.`right_match_orientation` AS `orientation`,
          C.`coverage`, O.`non_gapped`, O.`exceeded_clique_limit`,
          O.`weakly_non_scrambled`, O.`strongly_non_scrambled`
      FROM `pointer` AS P
      LEFT JOIN `coverage` AS C
      ON P.`prec_nuc_id` = C.`prec_nuc_id`
      AND P.`prod_nuc_id` = C.`prod_nuc_id`
      LEFT JOIN `alias` AS A
      ON P.`prec_nuc_id` = A.`nuc_id`
      LEFT JOIN `properties` AS O
      ON P.`prec_nuc_id` = O.`prec_nuc_id`
      AND P.`prod_nuc_id` = O.`prod_nuc_id`
      WHERE C.`coverage` >= '{ $OUTPUT_MIN_COVERAGE }'
      AND A.`is_primary` = 1
      ) AS T
      ORDER BY T.`prec_nuc_id`, T.`start` ASC;"
  );
  if ( $prec_pointers_table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
        " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // output precursor annotation of pointers in BED format
  $prec_pointers_file = fopen( $DIRECTORIES['PREC_POINTERS_FILE'], "w" );
  fwrite( $prec_pointers_file, '#track name="prec_pointers" description="precursor pointers ' .
      'labelled ptr_[prod-id]_[prod-start]_[prod-end]_[attached-match-index]_' .
      '[other-match-index]_[other-match-ptr-prec-start]_[other-match-ptr-prec-end]_' .
      '[arrangement-coverage]_[non-gapped]_[exceeded-clique-limit]_[weakly-non-scrambled]_' .
      '[strongly-non-scrambled]" itemRgb-"On"' );
  while ( $pointer = mysqli_fetch_assoc( $prec_pointers_table ) ) {

    $prec_id = $OUTPUT_USE_ALIAS === true ? $pointer['prec_nuc_id'] : $pointer['prec_alias'];
    if ( $pointer['non_gapped'] === NULL ) {
      $suffix = "_" . $pointer['coverage'] . "_2_2_2_2";
    } else {
      $suffix = "_" . $pointer['coverage'] . "_" . $pointer['non_gapped'] . "_" .
          $pointer['exceeded_clique_limit'] . "_" . $pointer['weakly_non_scrambled'] . "_" .
          $pointer['strongly_non_scrambled'];
    }

    $prec_pointers_row = array( $prec_id, $pointer['start'], $pointer['end'],
        $pointer['ptr_alias'] . $suffix, "0", $pointer['orientation'],
        $pointer['start'], $pointer['end'], "153,0,204" );

    fwrite( $prec_pointers_file, "\n" . implode( "\t", $prec_pointers_row ) );

  }
  mysqli_free_result( $prec_pointers_table );
  fclose( $prec_pointers_file );

  return true;

}

?>