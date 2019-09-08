<?php
/* part of SDRAP step; returns response message to AJAX form */

// return response message      
if ( $ERRORS === $NO_ERRORS ) { 
  $RESPONSE[ 'success' ] = true;
  $RESPONSE[ 'message' ] = array( 
    "hostname" => $INPUT['HOSTNAME'], 
    "username" => $INPUT['USERNAME'], 
    "password" => "XXXXXXXX",
    "database" => $INPUT['DATABASE'],
    "precursor_filename" => $INPUT['PRECURSOR_FILENAME'], 
    "product_filename" => $INPUT['PRODUCT_FILENAME'],
    "genus" => $INPUT['GENUS'],
    "species" => $INPUT['SPECIES'],
    "strain" => $INPUT['STRAIN'],
    "taxonomy_id" => $INPUT['TAXONOMY_ID'],
    "telo_pattern" => $INPUT['TELO_PATTERN'],
    "telo_error_limit" => $INPUT['TELO_ERROR_LIMIT'],
    "telo_buffer_limit" => $INPUT['TELO_BUFFER_LIMIT'],
    "telo_max_length" => $INPUT['TELO_MAX_LENGTH'],
    "telo_max_offset" => $INPUT['TELO_MAX_OFFSET'],
    "telo_min_length" => $INPUT['TELO_MIN_LENGTH'],
    "hsp_min_length" => $INPUT['HSP_MIN_LENGTH'],
    "pre_match_min_bitscore" => $INPUT['PRE_MATCH_MIN_BITSCORE'],
    "pre_match_min_pident" => $INPUT['PRE_MATCH_MIN_PIDENT'],
    "pre_match_min_coverage_addition" => $INPUT['PRE_MATCH_MIN_COVERAGE_ADDITION'],
    "merge_tolerance" => $INPUT['MERGE_TOLERANCE'],
    "merge_max_gap" => $INPUT['MERGE_MAX_GAP'],
    "gap_min_length" => $INPUT['GAP_MIN_LENGTH'],
    "pointer_min_length" => $INPUT['POINTER_MIN_LENGTH'],
    "add_match_min_bitscore" => $INPUT['ADD_MATCH_MIN_BITSCORE'],
    "add_match_min_pident" => $INPUT['ADD_MATCH_MIN_PIDENT'],
    "add_match_min_prod_segment_overlap" => $INPUT['ADD_MATCH_MIN_PROD_SEGMENT_OVERLAP'],
    "fragment_min_prod_segment_overlap" => $INPUT['FRAGMENT_MIN_PROD_SEGMENT_OVERLAP'],
    "property_min_coverage" => $INPUT['PROPERTY_MIN_COVERAGE'],
    "property_max_match_overlap" => $INPUT['PROPERTY_MAX_MATCH_OVERLAP'],
    "property_clique_limit" => $INPUT['PROPERTY_CLIQUE_LIMIT'],
    "scr_complete" => $INPUT['SCR_COMPLETE'],
    "scr_consecutive" => $INPUT['SCR_CONSECUTIVE'],
    "scr_ordered" => $INPUT['SCR_ORDERED'],
    "output_min_coverage" => $INPUT['OUTPUT_MIN_COVERAGE']
  );  
} else {
  $RESPONSE[ 'success' ] = false;
  $RESPONSE[ 'errors' ] = $ERRORS;
}

// close MySQL connection
if ( $LINK ) { mysqli_close ( $LINK ); }  

// send response
echo json_encode ( $RESPONSE );

?>
