<?php

//load helper functions
require_once( __DIR__ . "/get_maximal_cliques.php" );
require_once( __DIR__ . "/process_cliques.php" );
require_once( __DIR__ . "/output_properties_row.php" );

// compute and output row of `properties` table for the two sequences
function process_components( array &$connected_components, array $pair, $n, array &$E,
    array &$vert_to_match, $non_overlapping, $non_repeating, array &$output_aux,
    array $NON_SCRAMBLED, $PROPERTY_CLIQUE_LIMIT, array &$ERRORS, array $DIRECTORIES,
    $MAX_NUM_TABLE_FILE_ROWS ) {

  // initialize variables
  $properties = array(
      "weakly_complete" => false, "strongly_complete" => true,
      "weakly_consecutive" => false, "strongly_consecutive" => true,
      "weakly_ordered" => false, "strongly_ordered" => true,
      "weakly_non_scrambled" => false, "strongly_non_scrambled" => true
  );
  $num_cliques = 0;
  $exceeded_clique_limit = false;
  $maximal_cliques = array();

  // find maximal cliques
  foreach ( $connected_components as $comp_key => $component ) {

    // if clique limit reached and still components left, limit will be exceeded
    if ( $num_cliques === $PROPERTY_CLIQUE_LIMIT ) {
      $exceeded_clique_limit = true;
      break;
    }

    // find the maximal cliques in the component
    $exceeded_clique_limit = get_maximal_cliques( $maximal_cliques, $comp_key, $component, $E,
        $num_cliques, $PROPERTY_CLIQUE_LIMIT );

    if ( $exceeded_clique_limit ) { break; }

  }

  // determines arrangement properties
  process_cliques( $maximal_cliques, intval( $pair['max_index'] ), $vert_to_match,
      $connected_components, $properties, $NON_SCRAMBLED );

  // output data for `properties` table
  output_properties_row( $properties, $pair, $n, $non_overlapping, $non_repeating,
      $exceeded_clique_limit, $output_aux, $DIRECTORIES['PROPERTIES_FILE_PATH'],
      $MAX_NUM_TABLE_FILE_ROWS );

  return true;

}

?>