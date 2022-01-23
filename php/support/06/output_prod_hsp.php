<?php

// outputs product segments of hsp's between sequences with sufficient coverage of the product
function output_prod_hsp( $OUTPUT_USE_ALIAS, $OUTPUT_MIN_COVERAGE, array &$ERRORS, $LINK,
    array $DIRECTORIES ) {

  // get hsp's from the `hsp` table
  $prod_hsp_table = mysqli_query( $LINK,
      "SELECT H.`prec_nuc_id`, A_PREC.`alias` AS `prec_alias`,
          H.`prod_nuc_id`, A_PROD.`alias` AS `prod_alias`,
          H.`prec_start` - 1 AS `prec_start`, H.`prec_end` - 1 AS `prec_end`, H.`orientation`,
          H.`prod_start` - 1 AS `prod_start`, H.`prod_end` - 1 AS `prod_end`, H.`bitscore`, H.`pident`,
          C.`coverage`, P.`non_gapped`, P.`exceeded_clique_limit`,
          P.`weakly_non_scrambled`, P.`strongly_non_scrambled`
      FROM `hsp` AS H
      LEFT JOIN `coverage` AS C
      ON H.`prec_nuc_id` = C.`prec_nuc_id`
      AND H.`prod_nuc_id` = C.`prod_nuc_id`
      LEFT JOIN `alias` AS A_PREC
      ON H.`prec_nuc_id` = A_PREC.`nuc_id`
      LEFT JOIN `alias` AS A_PROD
      ON H.`prod_nuc_id` = A_PROD.`nuc_id`
      LEFT JOIN `properties` AS P
      ON H.`prec_nuc_id` = P.`prec_nuc_id`
      AND H.`prod_nuc_id` = P.`prod_nuc_id`
      WHERE C.`coverage` >= '{ $OUTPUT_MIN_COVERAGE }'
      AND A_PREC.`is_primary` = 1
      AND A_PROD.`is_primary` = 1
      ORDER BY H.`prod_nuc_id`, H.`prod_start` ASC;"
  );
  if ( $prod_hsp_table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
        " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // output product annotation of hsp's in BED format
  $prod_hsp_file = fopen( $DIRECTORIES['PROD_HSP_FILE'], "w" );
  fwrite( $prod_hsp_file, '#track name="prod_HSPs" description="product segments of HSPs ' .
      'labelled hsp_[prec-nuc-id]_[prec-start]_[prec-end]_[bitscore]_[pident]_' .
      '[arrangement-coverage]_[non-gapped]_[exceeded-clique-limit]_[weakly-non-scrambled]_' .
      '[strongly-non-scrambled]" itemRgb-"On"' );
  while ( $hsp = mysqli_fetch_assoc( $prod_hsp_table ) ) {

    $prec_id = $OUTPUT_USE_ALIAS === true ? $hsp['prec_nuc_id'] : $hsp['prec_alias'];
    $prod_id = $OUTPUT_USE_ALIAS === true ? $hsp['prod_nuc_id'] : $hsp['prod_alias'];
    if ( $hsp['non_gapped'] === NULL ) {
      $suffix = "_" . $hsp['coverage'] . "_2_2_2_2";
    } else {
      $suffix = "_" . $hsp['coverage'] . "_" . $hsp['non_gapped'] . "_" .
          $hsp['exceeded_clique_limit'] . "_" . $hsp['weakly_non_scrambled'] . "_" .
          $hsp['strongly_non_scrambled'];
    }

    $hsp_alias = implode( "_", array( "hsp", $prec_id, $hsp['prec_start'],
        $hsp['prec_end'], $hsp['bitscore'], $hsp['pident'] ) ) . $suffix;
    $prod_hsp_row = array( $prod_id, $hsp['prod_start'], $hsp['prod_end'],
        $hsp_alias, "0", $hsp['orientation'],
        $hsp['prod_start'], $hsp['prod_end'], "255,153,0" );

    fwrite( $prod_hsp_file, "\n" . implode( "\t", $prod_hsp_row ) );

  }
  mysqli_free_result( $prod_hsp_table );
  fclose( $prod_hsp_file );

  return true;

}

?>