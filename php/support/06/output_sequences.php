<?php

// outputs nuceleotide sequences
function output_sequences( $OUTPUT_USE_ALIASES, array &$ERRORS, $LINK, array $DIRECTORIES ) {

  // get `nucleotide` table and output in FASTA format
  $nucleotide_table = mysqli_query( $LINK,
    "SELECT N.`nuc_id`, N.`sequence`, N.`feat_id`, A.`alias`
    FROM `nucleotide` AS N
    LEFT JOIN `alias` AS A
    ON N.`nuc_id` = A.`nuc_id`
    WHERE `is_primary` = 1;"
  );
  if ( $nucleotide_table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
        " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  $nucleotide_file = fopen( $DIRECTORIES['NUCLEOTIDE_FILE'], "w" );
  $prec_nucleotide_file = fopen( $DIRECTORIES['PREC_NUCLEOTIDE_FILE'], "w" );
  $prod_nucleotide_file = fopen( $DIRECTORIES['PROD_NUCLEOTIDE_FILE'], "w" );

  while ( $nucleotide_row = mysqli_fetch_assoc( $nucleotide_table ) ) {

    $id = $OUTPUT_USE_ALIASES === true ? $nucleotide_row['nuc_id'] : $nucleotide_row['alias'];

    fwrite( $nucleotide_file, ">" . $id . "\n" . chunk_split( $nucleotide_row['sequence'], 60,
        "\n" ) );

    if ( $nucleotide_row['feat_id'] == '1' ) {

      fwrite( $prec_nucleotide_file, ">" . $id . "\n" . chunk_split( $nucleotide_row['sequence'],
          60, "\n" ) );

    } else {

      fwrite( $prod_nucleotide_file, ">" . $id . "\n" . chunk_split( $nucleotide_row['sequence'],
          60, "\n" ) );

    }

  }

  mysqli_free_result( $nucleotide_table );
  fclose( $nucleotide_file );
  fclose( $prec_nucleotide_file );
  fclose( $prod_nucleotide_file );

  exec( "samtools faidx" . $DIRECTORIES['NUCLEOTIDE_FILE'] );
  exec( "samtools faidx" . $DIRECTORIES['PREC_NUCLEOTIDE_FILE'] );
  exec( "samtools faidx" . $DIRECTORIES['PROD_NUCLEOTIDE_FILE'] );

  return true;

}

?>