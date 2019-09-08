<?php

// outputs precursor segments of matches and fragments between sequences with sufficient coverage of
// the product
function output_prec_segments_fragments( $OUTPUT_USE_ALIAS, $OUTPUT_FRAGMENTS,
    $OUTPUT_GIVE_COMPLEMENT, $OUTPUT_MIN_COMPLEMENT_LENGTH, $OUTPUT_MIN_COVERAGE, array &$ERRORS,
    $LINK, array $DIRECTORIES ) {

  // get precursor segments and fragments from the `match` table
  $prec_segments_table = mysqli_query( $LINK,
      "SELECT M.`prec_nuc_id`, A.`alias` AS `prec_alias`, M.`prec_start`, M.`prec_end`, M.`index`,
          M.`orientation`, M.`prec_segment_alias`, M.`is_preliminary`, M.`is_additional`,
          M.`is_fragment`, C.`coverage`, P.`non_gapped`, P.`exceeded_clique_limit`,
          P.`weakly_non_scrambled`, P.`strongly_non_scrambled`
      FROM `match` AS M
      LEFT JOIN `coverage` AS C
      ON M.`prec_nuc_id` = C.`prec_nuc_id`
      AND M.`prod_nuc_id` = C.`prod_nuc_id`
      LEFT JOIN `alias` AS A
      ON M.`prec_nuc_id` = A.`nuc_id`
      LEFT JOIN `properties` AS P
      ON M.`prec_nuc_id` = P.`prec_nuc_id`
      AND M.`prod_nuc_id` = P.`prod_nuc_id`
      WHERE C.`coverage` >= '{ $OUTPUT_MIN_COVERAGE }'
      AND A.`is_primary` = 1
      ORDER BY M.`prec_nuc_id`, M.`prec_start` ASC;"
  );
  if ( $prec_segments_table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
        " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // output precursor annotation of matches and fragments in BED format
/*  $prec_segments_file = fopen( $DIRECTORIES['PREC_SEGMENTS_FILE'], "w" );
  fwrite( $prec_segments_file, '#track name="prec_segments" description="precursor segments ' .
      'labelled pre_match_[prod-id]_[prod-start]_[prod-end]_[index]_[pre-cov]_[add-cov]_' .
      '[arrangement-coverage]_[non-gapped]_[exceeded-clique-limit]_[weakly-non-scrambled]_' .
      '[strongly-non-scrambled]" itemRgb-"On"' );*/

  if ( $OUTPUT_FRAGMENTS === true ) {
/*    $prec_fragments_file = fopen( $DIRECTORIES['PREC_FRAGMENTS_FILE'], "w" );
    fwrite( $prec_fragments_file, '#track name="prec_fragments" description="precursor fragments ' .
        'labelled frag_[prod-id]_[prod-start]_[prod-end]_[index]_[pre-cov]_[add-cov]_' .
        '[arrangement-coverage]_[non-gapped]_[exceeded-clique-limit]_[weakly-non-scrambled]_' .
        '[strongly-non-scrambled]" itemRgb-"On"' );*/
  }

  if ( $OUTPUT_GIVE_COMPLEMENT === true ) {

    $prec_eliminated_sequences_file = fopen( $DIRECTORIES['PREC_SEGMENTS_COMPLEMENT_FILE'], "w" );
    fwrite( $prec_eliminated_sequences_file, '#track name="prec_eliminated_sequences" description=' .
        '"intervals comprising complement of the precursor segments labelled comp_' .
        '[left-flanking-segment-prod-id]_[left-flanking-segment-index]_' .
        '[right-flanking-segment-prod-id]_[right-flanking-segment-index]" itemRgb-"On"' );

    // get lengths of precursor sequences from `nucleotide` table
    $prec_seq_length_table = mysqli_query( $LINK,
      "SELECT N.`nuc_id`, A.`alias`, N.`length`
      FROM `nucleotide` AS N
      LEFT JOIN `alias` AS A
      ON N.`nuc_id` = A.`nuc_id`
      AND A.`is_primary` = 1;"
    );
    if ( $prec_seq_length_table === false ) {
      $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
          " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
      return false;
    }

    // prepare precursor sequence length data
    $prec_length = array();
    while ( $prec_length_row = mysqli_fetch_assoc( $prec_seq_length_table ) ) {

      $prec_id = $OUTPUT_USE_ALIAS === true ? $prec_length_row['nuc_id'] :
          $prec_length_row['alias'];
      $prec_length[ $prec_id ] = $prec_length_row['length'];

    }
    mysqli_free_result( $prec_seq_length_table );

    // prepare for iteration over precursor segments to detect complementary regions
    $old_prec_id = "";
    $length = 0;
    $previous_segment_end = 0;
    $previous_segment_alias = "none_0";

  }

  while ( $segment = mysqli_fetch_assoc( $prec_segments_table ) ) {

    $prec_id = $OUTPUT_USE_ALIAS === true ? $segment['prec_nuc_id'] : $segment['prec_alias'];

    if ( $segment['non_gapped'] === NULL ) {
      $suffix = "_" . $segment['coverage'] . "_2_2_2_2";
    } else {
      $suffix = "_" . $segment['coverage'] . "_" . $segment['non_gapped'] . "_" .
          $segment['exceeded_clique_limit'] . "_" . $segment['weakly_non_scrambled'] . "_" .
          $segment['strongly_non_scrambled'];
    }

    if ( $segment['is_fragment'] === "0" ) {

      // darker blue for preliminary matches, lighter blue for additional matches
      $color = $segment['is_preliminary'] === "1" ? "0,102,255" : "0,204,255";

      $prec_segments_row = array( $prec_id, $segment['prec_start'], $segment['prec_end'],
          $segment['prec_segment_alias'] . $suffix, "0", $segment['orientation'],
          $segment['prec_start'], $segment['prec_end'], $color );

/*      fwrite( $prec_segments_file, "\n" . implode( "\t", $prec_segments_row ) );*/

    } else if ( $OUTPUT_FRAGMENTS === true ) {

      // dark teal for index 0 fragments, green for regular fragments
      $color = $segment['index'] === "0" ? "0,153,153" : "0,204,102";

      $prec_fragments_row = array( $prec_id, $segment['prec_start'], $segment['prec_end'],
          $segment['prec_segment_alias'] . $suffix, "0", $segment['orientation'],
          $segment['prec_start'], $segment['prec_end'], $color );

/*      fwrite( $prec_fragments_file, "\n" . implode( "\t", $prec_fragments_row ) );*/

    }

    if ( $OUTPUT_GIVE_COMPLEMENT === true && $segment['is_fragment'] === "0" ) {

      // process previous precursor sequence and set up for next
      if ( $prec_id !== $old_prec_id ) {

        if ( $previous_segment_end + $OUTPUT_MIN_COMPLEMENT_LENGTH <= intval( $length ) ) {

          $prec_eliminated_sequences_row = array( $old_prec_id, $previous_segment_end + 1,
              $length, $previous_segment_alias . "_none_0", "0", "+",
              $previous_segment_end + 1, $length, "255,0,153" );

          fwrite( $prec_eliminated_sequences_file, "\n" .
              implode( "\t", $prec_eliminated_sequences_row ) );

        }

        $old_prec_id = $prec_id;
        $length = $prec_length[ $prec_id ];
        $previous_segment_end = 0;
        $previous_segment_alias = "none_0";

      }

      $next_segment_start = (int) $segment['prec_start'];
      $next_segment_alias_split = explode( "_", $segment['prec_segment_alias'] );
      $next_segment_alias = $next_segment_alias_split[2] . "_" . $next_segment_alias_split[5];

      if ( $previous_segment_end + $OUTPUT_MIN_COMPLEMENT_LENGTH < $next_segment_start ) {

        $prec_eliminated_sequences_row = array( $prec_id, $previous_segment_end + 1,
            $next_segment_start - 1, $previous_segment_alias . "_" . $next_segment_alias, "0", "+",
            $previous_segment_end + 1, $next_segment_start - 1, "255,0,153" );

        fwrite( $prec_eliminated_sequences_file, "\n" .
            implode( "\t", $prec_eliminated_sequences_row ) );

      }

      $previous_segment_end = (int) $segment['prec_end'];
      $previous_segment_alias = $next_segment_alias;

    }

  }
  mysqli_free_result( $prec_segments_table );
/*  fclose( $prec_segments_file );
  if ( $OUTPUT_FRAGMENTS === true ) { fclose( $prec_fragments_file ); }*/
  if ( $OUTPUT_GIVE_COMPLEMENT === true ) {

    // process last precursor sequence
    if ( $previous_segment_end + $OUTPUT_MIN_COMPLEMENT_LENGTH <= intval( $length ) ) {

      $prec_eliminated_sequences_row = array( $prec_id, $previous_segment_end + 1,
          $length, $previous_segment_alias . "_none_0", "0", "+",
          $previous_segment_end + 1, $length, "255,0,153" );

      fwrite( $prec_eliminated_sequences_file, "\n" .
          implode( "\t", $prec_eliminated_sequences_row ) );

    }

    fclose( $prec_eliminated_sequences_file );

  }


  return true;

}

?>