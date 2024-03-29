<?php

// upload data into `feature` table
function upload_features( $LINK ) {

  $result = true;
  if ( mysqli_query ( $LINK, "INSERT INTO `feature` VALUES ( '1', 'prec', 'nucleotide' );" ) === false ) {
    $result = false;
  }
  if ( mysqli_query ( $LINK, "INSERT INTO `feature` VALUES ( '2', 'prod', 'nucleotide' );" ) === false ) {
    $result = false;
  }

  return $result;

}

// upload data into `organism` table
function upload_organism( $LINK, array $INPUT ) {

  $result = true;
  if ( mysqli_query ( $LINK,
    "INSERT INTO `organism` VALUES (
     '1',
     '{$INPUT['GENUS']}',
     '{$INPUT['SPECIES']}',
     '{$INPUT['STRAIN']}',
     '{$INPUT['TAXONOMY_ID']}'
    );"
  ) === false ) {
    $result = false;
  }

  return $result;

}

// upload data into `parameters` table
function upload_parameters( $LINK, array $INPUT ) {

  $result = true;

  $date = date("Y-m-d-H-i");
  $compute_pointers = (int) $INPUT['COMPUTE_POINTERS'];
  $scr_complete = (int) $INPUT['SCR_COMPLETE'];
  $scr_consecutive = (int) $INPUT['SCR_CONSECUTIVE'];
  $scr_ordered = (int) $INPUT['SCR_ORDERED'];
  $output_give_summary = (int) $INPUT['OUTPUT_GIVE_SUMMARY'];
  $output_use_alias = (int) $INPUT['OUTPUT_USE_ALIAS'];
  $output_gaps = (int) $INPUT['OUTPUT_GAPS'];
  $output_fragments = (int) $INPUT['OUTPUT_FRAGMENTS'];
  $output_give_complement = (int) $INPUT['OUTPUT_GIVE_COMPLEMENT'];
  $output_min_complement_length = (int) $INPUT['OUTPUT_MIN_COMPLEMENT_LENGTH'];
  if ( mysqli_query ( $LINK,
    "INSERT INTO `parameter` VALUES (
     '1',
     '{$date}',
     '{$INPUT['DATABASE']}',
     '{$INPUT['USERNAME']}',
     '{$INPUT['GENUS']}',
     '{$INPUT['SPECIES']}',
     '{$INPUT['STRAIN']}',
     '{$INPUT['TAXONOMY_ID']}',
     '{$INPUT['PRECURSOR_FILENAME']}',
     '{$INPUT['PRECURSOR_DELIMITER']}',
     '{$INPUT['PRODUCT_FILENAME']}',
     '{$INPUT['PRODUCT_DELIMITER']}',
     '{$INPUT['TELO_PATTERN']}',
     '{$INPUT['TELO_ERROR_LIMIT']}',
     '{$INPUT['TELO_BUFFER_LIMIT']}',
     '{$INPUT['TELO_MAX_LENGTH']}',
     '{$INPUT['TELO_MAX_OFFSET']}',
     '{$INPUT['TELO_MIN_LENGTH']}',
     '{$INPUT['HSP_MIN_LENGTH']}',
     '{$INPUT['PRE_MATCH_MIN_BITSCORE']}',
     '{$INPUT['PRE_MATCH_MIN_PIDENT']}',
     '{$INPUT['PRE_MATCH_MIN_COVERAGE_ADDITION']}',
     '{$INPUT['MERGE_TOLERANCE']}',
     '{$INPUT['MERGE_MAX_GAP']}',
     '{$INPUT['GAP_MIN_LENGTH']}',
     '{$compute_pointers}',
     '{$INPUT['POINTER_MIN_LENGTH']}',
     '{$INPUT['ADD_MATCH_MIN_BITSCORE']}',
     '{$INPUT['ADD_MATCH_MIN_PIDENT']}',
     '{$INPUT['ADD_MATCH_MIN_PROD_SEGMENT_OVERLAP']}',
     '{$INPUT['FRAGMENT_MIN_PROD_SEGMENT_OVERLAP']}',
     '{$INPUT['PROPERTY_MIN_COVERAGE']}',
     '{$INPUT['PROPERTY_MAX_MATCH_OVERLAP']}',
     '{$INPUT['PROPERTY_CLIQUE_LIMIT']}',
     '{$scr_complete}',
     '{$scr_consecutive}',
     '{$scr_ordered}',
     '{$INPUT['OUTPUT_MIN_COVERAGE']}',
     '{$output_give_summary}',
     '{$output_use_alias}',
     '{$output_gaps}',
     '{$output_fragments}',
     '{$output_give_complement}',
     '{$output_min_complement_length}'
    );"
  ) === false ) { $result = false; }

  return $result;

}

?>