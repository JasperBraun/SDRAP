<?php

// adds a vertex to the graph corresponding to the arrangement
function add_vertex( $v, array &$vert_to_match, array &$E, &$non_overlapping, &$non_repeating,
    $PROPERTY_MAX_MATCH_OVERLAP )
    {

  $new_adjacency_row = array();
  $neighbors = array();

  // compare vertex v to all previous vertices
  for ( $u = 0; $u < $v; $u++ ) {

    // compute overlap of corresponding matches on precursor
    $overlap = min( $vert_to_match[$u]['end'], $vert_to_match[$v]['end'] ) -
        max( $vert_to_match[$u]['start'], $vert_to_match[$v]['start'] ) + 1;

    if ( $vert_to_match[$u]['index'] === $vert_to_match[$v]['index'] ) {

      // do not add edge between u and v if they are repeats
      $E[$u][$v] = false;
      $new_adjacency_row[$u] = false;
      $non_repeating = false;

    } elseif ( $overlap > $PROPERTY_MAX_MATCH_OVERLAP ) {

      // do not add edge between u and v if they are repeats, or if they overlap too much
      $E[$u][$v] = false;
      $new_adjacency_row[$u] = false;
      $non_overlapping = false;

    } else {

      // add edge between u and v if they are not repeats and do not overlap too much
      $E[$u][$v] = true;
      $new_adjacency_row[$u] = true;

    }

  }

  // vertex is not adjacent to itself
  $new_adjacency_row[$v] = false;

  // add row to adjacency matrix and neighbors to edge list
  $E[$v] = $new_adjacency_row;

  return true;

}

?>