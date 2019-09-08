<?php

// load helper functions
require_once( __DIR__ . "/adjust_properties.php" );

// determines arrangement properties and computes pointer sequences and assembly words
function process_cliques ( array &$maximal_cliques, $max_index, array &$vert_to_match,
    array &$connected_components, array &$properties, array $NON_SCRAMBLED ) {

  foreach ( $maximal_cliques as $clique ) {

    // initialize variables
    $comp_key = $clique['comp_key'];

    $clique_properties = array(
        "complete" => true,
        "consecutive" => true,
        "ordered" => true,
        "non_scrambled" => true
    );

    $occurrences = array_fill( 1, $max_index, false );
    $clique_min_index = $max_index + 1;
    $last_index = NULL;

    // check the subarrangement corresponding to the clique
    foreach ( $clique['vertices'] as $v ) {

      $match = $vert_to_match[ $connected_components[$comp_key][$v] ];
      $index = $match['index'];

      // check for orderedness
      if ( $last_index === NULL ) {
        $orientation = $match['orientation']; // first match of the subarrangement
      } else if ( $orientation !== $match['orientation'] ) {
        $clique_properties['ordered'] = false; // must all have same orientation to be ordered
      } else if ( $orientation === "+" && $last_index > $index ) {
        $clique_properties['ordered'] = false; // positively oriented but decreasing
      } else if ( $orientation === "-" && $last_index < $index ) {
        $clique_properties['ordered'] = false; // negatively oriented but increasing
      }

      $last_index = $index;

      // for checking completeness and consecutivity
      $occurrences[ $index ] = true;
      if ( $index < $clique_min_index ) {
        $clique_min_index = $index;
      }

    }

    // check for consecutivity
    $end_consec = false;
    for ( $i = $clique_min_index; $i <= $max_index; $i++ ) {

      if ( !$occurrences[$i] ) { // consecutive chunk of indices ended
        $end_consec = true;
      } else if ( $occurrences[$i] && $end_consec ) { // a second consecutive chunk started
        $clique_properties['consecutive'] = false;
        break;
      }

    }

    // check for completeness
    if ( $clique_min_index > 1 || $end_consec ) { // incompleteness is equivalent to this condition
      $clique_properties['complete'] = false;
    }

    // determine scrambling and adjust properties
    adjust_properties( $properties, $clique_properties, $NON_SCRAMBLED );

  }

  return true;

}

?>