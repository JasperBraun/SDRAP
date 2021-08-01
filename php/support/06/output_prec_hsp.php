<?php

// outputs precursor segments of hsp's between sequences with sufficient coverage of the product
function output_prec_hsp( $OUTPUT_USE_ALIAS, $OUTPUT_MIN_COVERAGE, array &$ERRORS, $LINK,
    array $DIRECTORIES ) {

  // get hsp's from the `hsp` table
  $prec_hsp_table = mysqli_query( $LINK,
      "SELECT H.`prec_nuc_id`, A_PREC.`alias` AS `prec_alias`,
          H.`prod_nuc_id`, A_PROD.`alias` AS `prod_alias`,
          H.`prec_start`, H.`prec_end`, H.`orientation`,
          H.`prod_start`, H.`prod_end`, H.`bitscore`, H.`pident`,
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
      ORDER BY H.`prec_nuc_id`, H.`prec_start` ASC;"
  );
  if ( $prec_hsp_table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
        " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // output precursor annotation of hsp's in BED format
  $prec_hsp_file = fopen( $DIRECTORIES['PREC_HSP_FILE'], "w" );
  fwrite( $prec_hsp_file, '#track name="prec_HSPs" description="precursor segments of HSPs ' .
      'labelled hsp_[prod-id]_[prod-start]_[prod-end]_[bitscore]_[pident]_' .
      '[arrangement-coverage]_[non-gapped]_[exceeded-clique-limit]_[weakly-scrambled]_' .
      '[strongly-scrambled]" itemRgb-"On"' );
  while ( $hsp = mysqli_fetch_assoc( $prec_hsp_table ) ) {

    $prec_id = $OUTPUT_USE_ALIAS === true ? $hsp['prec_nuc_id'] : $hsp['prec_alias'];
    $prod_id = $OUTPUT_USE_ALIAS === true ? $hsp['prod_nuc_id'] : $hsp['prod_alias'];
    if ( $hsp['non_gapped'] === NULL ) {
      $suffix = "_" . $hsp['coverage'] . "_2_2_2_2";
    } else {
      if ( $hsp['weakly_non_scrambled'] === 0 && $hsp['strongly_non_scrambled'] === 0 ) {
        $strongly_scrambled = 1;
        $weakly_scrambled = 1;
      } else if ( $hsp['weakly_non_scrambled'] === 1 && $hsp['strongly_non_scrambled'] === 0 ) {
        $strongly_scrambled = 0;
        $weakly_scrambled = 1;
      } else {
        $strongly_scrambled = 0;
        $weakly_scrambled = 0;
      }
      $suffix = "_" . $hsp['coverage'] . "_" . $hsp['non_gapped'] . "_" .
          $hsp['exceeded_clique_limit'] . "_" . $weakly_scrambled . "_" .
          $strongly_scrambled;
    }

    $hsp_alias = implode( "_", array( "hsp", $prod_id, $hsp['prod_start'], 
        $hsp['prod_end'], $hsp['bitscore'], $hsp['pident'] ) ) . $suffix;
    $prec_hsp_row = array( $prec_id, $hsp['prec_start'], $hsp['prec_end'],
          $hsp_alias, "0", $hsp['orientation'],
          $hsp['prec_start'], $hsp['prec_end'], "255,153,0" );

    fwrite( $prec_hsp_file, "\n" . implode( "\t", $prec_hsp_row ) );

  }
  mysqli_free_result( $prec_hsp_table );
  fclose( $prec_hsp_file );

  return true;

}

?>