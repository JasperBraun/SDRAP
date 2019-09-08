<?php

// load helper functions
require_once( __DIR__ . "/add_vertex.php" );
require_once( __DIR__ . "/get_connected_components.php" );
require_once( __DIR__ . "/process_components.php" );

// computes and outputs contents of `properties` table for given sequence pair
function process_sequence_pair( array $pair, array &$output_aux, array $INPUT, array &$ERRORS,
    $LINK, array $DIRECTORIES, $MAX_NUM_TABLE_FILE_ROWS ) {

  // get matches of the product sequence on the precursor sequence
  $match_table = mysqli_query( $LINK,
      "SELECT `prec_start`, `prec_end`, `index`
      FROM `match`
      WHERE `prec_nuc_id` = {$pair['prec_nuc_id']}
      AND `prod_nuc_id` = {$pair['prod_nuc_id']}
      AND `is_fragment` = 0
      ORDER BY `prec_start` ASC;"
  );
  if ( $match_table === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() .
        " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    return false;
  }

  // form arrangement graph
  $E = array(); // lower triangle of adjacency matrix
  $v = -1; // current vertex
  $vert_to_match = array(); // matches with corresponding vertex as index
  $non_overlapping = true;
  $non_repeating = true;

  // add vertices to graph one-by-one
  while ( $match = mysqli_fetch_assoc( $match_table ) ) {

    // keep track of correspondence between vertex and matches
    $v += 1;
    $vert_to_match[$v] = array(
        "start" => (int) $match['prec_start'],
        "end" => (int) $match['prec_end'],
        "index" => abs( (int) $match['index'] ),
        "orientation" => substr( $match['index'], 0, 1 ) === "-" ? "-" : "+"
    );

    // add a vertex to the graph corresponding to the arrangement
    add_vertex( $v, $vert_to_match, $E, $non_overlapping, $non_repeating,
        $INPUT['PROPERTY_MAX_MATCH_OVERLAP'] );

  }
  mysqli_free_result( $match_table );

  // get number of vertices
  $n = sizeof( $vert_to_match );

  // compute connected components of the graph
  $connected_components = get_connected_components( $n, $E );
  $NON_SCRAMBLED = array(
      "COMPLETE" => $INPUT['SCR_COMPLETE'],
      "CONSECUTIVE" => $INPUT['SCR_CONSECUTIVE'],
      "ORDERED" => $INPUT['SCR_ORDERED']
  );
  // compute and output row of `properties` table for the arrangement between the two sequences
  $process_components_result = process_components( $connected_components, $pair, $n, $E,
      $vert_to_match, $non_overlapping, $non_repeating, $output_aux, $NON_SCRAMBLED, $INPUT['PROPERTY_CLIQUE_LIMIT'], $ERRORS, $DIRECTORIES, $MAX_NUM_TABLE_FILE_ROWS );
  if ( $process_components_result === false ) {
    $ERRORS['other'][] = "Error while computing properties of precursor sequnce " .
        json_encode($pair['prec_nuc_id']) . ", and product sequence " .
        json_encode($pair['prod_nuc_id']) . " in " . basename(__FILE__,".php") .
        " near line " . __LINE__ . ".";
    return false;
  }

  return true;

}

?>