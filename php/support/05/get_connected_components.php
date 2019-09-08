<?php

// recursive dfs finding the vertices of component containing the given vertex
function recursive_dfs( $v, array &$component, array &$visited, &$num_visited, $n, array &$E ) {

  // add vertex to component
  $component[] = $v;
  $visited[$v] = true;
  $num_visited += 1;

  for ( $u = $v + 1; $u < $n; $u++ ) {
    if ( $E[$u][$v] && ! isset($visited[$u]) ) {
      recursive_dfs( $u, $component, $visited, $num_visited, $n, $E );
    }
  }

  return true;

}

// computes connected components of the graph
function get_connected_components( $n, array &$E ) {

  // initialize variables
  $result = array();
  $visited = array();
  $num_visited = 0;

  // one vertex at a time, perform dfs and add theconnected component containing the vertex to the
  // result
  for ( $v = 0; $v < $n; $v++ ) {

    // skip vertex if its component has been computed already
    if ( ! isset($visited[$v]) ) {

      // compute component containing vertex via recursive dfs
      $component = array();
      recursive_dfs( $v, $component, $visited, $num_visited, $n, $E );
      $result[] = $component;

      // stop when all vertices have been visited
      if ( $num_visited === $n ) { break; }

    }

  }

  return $result;

}

?>