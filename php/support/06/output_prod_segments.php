<?php

// outputs product segments of matches between sequences with sufficient coverage of the product
function output_prod_segments( $OUTPUT_USE_ALIAS, $OUTPUT_MIN_COVERAGE, array &$ERRORS, $LINK,
    array $DIRECTORIES ) {

  // get precursor segments and fragments from the `match` table
  $prod_segments_table = mysqli_query( $LINK,
      "SELECT M.`prod_nuc_id`, A.`alias` AS `prod_alias`, M.`prod_start` - 1 AS `prod_start`, M.`prod_end` - 1 AS `prod_end`, M.`index`,
          M.`orientation`, M.`prod_segment_alias`, M.`is_preliminary`, C.`coverage`, P.`non_gapped`,
          P.`exceeded_clique_limit`, P.`weakly_non_scrambled`, P.`strongly_non_scrambled`
      FROM `match` AS M
      LEFT JOIN `coverage` AS C
      ON M.`prec_nuc_id` = C.`prec_nuc_id`
      AND M.`prod_nuc_id` = C.`prod_nuc_id`
      LEFT JOIN `alias` AS A
      ON M.`prod_nuc_id` = A.`nuc_id`
      LEFT JOIN `properties` AS P
      ON M.`prec_nuc_id` = P.`prec_nuc_id`
      AND M.`prod_nuc_id` = P.`prod_nuc_id`
      WHERE C.`coverage` >= '{ $OUTPUT_MIN_COVERAGE }'
      AND M.`is_preliminary` = 1
      AND A.`is_primary` = 1
      ORDER BY M.`prod_nuc_id`, M.`prod_start` ASC;"
  );
  if ( $prod_segments_table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
        " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // output product annotation of matches in BED format
  $prod_segments_file = fopen( $DIRECTORIES['PROD_SEGMENTS_FILE'], "w" );
  fwrite( $prod_segments_file, '#track name="prod_segments" description="product segments ' .
      'labelled pre_match_[prec-id]_[prec-start]_[prec-end]_[index]_[pre-cov]_[add-cov]_' .
      '[arrangement-coverage]_[non-gapped]_[exceeded-clique-limit]_[weakly-non-scrambled]_' .
      '[strongly-non-scrambled]" itemRgb-"On"' );
  while ( $segment = mysqli_fetch_assoc( $prod_segments_table ) ) {

    $prod_id = $OUTPUT_USE_ALIAS === true ? $segment['prod_nuc_id'] : $segment['prod_alias'];

    if ( $segment['non_gapped'] === NULL ) {
      $suffix = "_" . $segment['coverage'] . "_2_2_2_2";
    } else {
      $suffix = "_" . $segment['coverage'] . "_" . $segment['non_gapped'] . "_" .
          $segment['exceeded_clique_limit'] . "_" . $segment['weakly_non_scrambled'] . "_" .
          $segment['strongly_non_scrambled'];
    }

    $prod_segments_row = array( $prod_id, $segment['prod_start'], $segment['prod_end'],
        $segment['prod_segment_alias'] . $suffix, "0", $segment['orientation'],
        $segment['prod_start'], $segment['prod_end'], "0,102,255" );

    fwrite( $prod_segments_file, "\n" . implode( "\t", $prod_segments_row ) );

  }
  mysqli_free_result( $prod_segments_table );
  fclose( $prod_segments_file );

  return true;

}

?>