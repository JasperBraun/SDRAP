<?php

// reads in nucleotide string of current sequence from fasta
// file and returns description of next sequence, if any
function read_sequence( $input_sequence_file, $delim ) {

  $result = array(
    "last" => false,
    "sequence" => "",
    "length" => 0,
    "next_header" => ""
  );

  while ( ( $seq_line = fgets($input_sequence_file) ) !== false ) {

    if ( substr($seq_line, 0, 1) !== '>' ) {
      $result['sequence'] .= strtoupper( chop($seq_line) );
    } else {
      $result['length'] = strlen($result['sequence']);
      if ( $delim === "" ) {
        $result['next_header'] = trim( substr($seq_line, 1) );
      } else {
        $result['next_header'] = array_map( 'trim', explode( $delim, substr($seq_line, 1) ) );
      }
      return $result;
    }

  }

  $result['last'] = true;
  $result['length'] = strlen($result['sequence']);
  $result['next_header'] = "";

  return $result;

}

?>