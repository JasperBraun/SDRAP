<?php

// blasts product sequences against precursor sequences
function blast_sequences( array $INPUT, array &$ERRORS, array $DIRECTORIES, $BLAST_PARAMETERS, array $BLAST_DATABASE_FILE_EXTENSIONS ) {

  // verify that precursor sequence files for blast exist
  if ( ! is_file($DIRECTORIES['BLAST_PREC_SEQUENCE_FILE']) ) {
    $ERRORS['other'][] = "Could not find file " . $DIRECTORIES['BLAST_PREC_SEQUENCE_FILE'] . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // verify that product sequence files for blast exist
  if ( ! is_file($DIRECTORIES['BLAST_PROD_SEQUENCE_FILE']) ) {
    $ERRORS['other'][] = "Could not find file " . $DIRECTORIES['BLAST_PROD_SEQUENCE_FILE'] . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // build blast database
  $command = "makeblastdb -in " . $DIRECTORIES['BLAST_PREC_SEQUENCE_FILE'] . " -parse_seqids -dbtype nucl 2>&1";
  $exec_result = exec ( $command, $blast_db_output, $blast_db_return_value );
  if ( $blast_db_return_value != 0 ) {
    $ERRORS['other'][] = "Failed to build blast database with precursor sequences with output " . $exec_result . "; " . json_encode( $blast_db_output ) . " to command " . $command . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // blast query against database
  exec ( "blastn -db " . $DIRECTORIES['BLAST_PREC_SEQUENCE_FILE'] . " -query " . $DIRECTORIES['BLAST_PROD_SEQUENCE_FILE'] . " -outfmt 10 -out " . $DIRECTORIES['BLAST_OUTPUT_FILE'] . " " . $BLAST_PARAMETERS, $blast_query_output, $blast_query_return_value );
  if ( $blast_query_return_value != 0 ) {
    $ERRORS['other'][] = "Failed to blast query product sequences against database of precursor sequences in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // remove blast files
  unlink( $DIRECTORIES['BLAST_PREC_SEQUENCE_FILE'] );
  foreach ( $BLAST_DATABASE_FILE_EXTENSIONS AS $ext ) {
    unlink( $DIRECTORIES['BLAST_PREC_SEQUENCE_FILE'] . $ext );
  }
  unlink( $DIRECTORIES['BLAST_PROD_SEQUENCE_FILE'] );

  return true;

}

?>