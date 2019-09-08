<?php

// load helper functions
require_once( __DIR__ . "/maximal_cliques_helper.php" );

// algorithm comes from: doi:10.1007/978-3-540-27810-8_23;
// properties (a), (b), (c), (d), (c-1), (c-2), (c-3), (d-1),
// (d-2), (d-3) and (d-4) refer to Lemmas 2, 3 and 4
// notation and terminology in the comments may also come from
// this source
function recursive_get_maximal_cliques ( $n, &$E, &$maximal_cliques, &$num_cliques, $parent_clique_idx, $PROPERTY_CLIQUE_LIMIT, $comp_key ) {

  $K = $maximal_cliques[ $parent_clique_idx ]['vertices'];
  $min_prefix = min_prefix( $K, $E );

  // compute all i satisfying (a), (b), (c) and (d)
  $A = my_array_diff( range( 0, $n-1 ), $K );
  $B = range( $min_prefix + 1, $n-1 );
  $C = get_c( $K, $n, $E );
  $D = get_d( $K, $n, $E );

  $children_indices = my_array_intersect( $A, $B, $C, $D );

  // for each child index, compute child and call recursion
  if ( $children_indices === NULL ) {
    return false;
  } else {
    foreach ( $children_indices as $i ) {

      if ( $num_cliques == $PROPERTY_CLIQUE_LIMIT ) {
        return true;
      } else {
        $child = get_child( $K, $i, $E, $n );
        $maximal_cliques[] = array( "vertices" => $child, "comp_key" => $comp_key );
        end( $maximal_cliques );
        $new_parent_clique_idx = key( $maximal_cliques );
        reset( $maximal_cliques );
        $num_cliques += 1;

        $recursion = recursive_get_maximal_cliques( $n, $E, $maximal_cliques, $num_cliques, $new_parent_clique_idx, $PROPERTY_CLIQUE_LIMIT, $comp_key );
        if ( $recursion ) {
          return true;
        }

      }

    }

  }

  return false;

}

// finds the maximal cliques in the component
function get_maximal_cliques( array &$cliques, $comp_key, array &$component, array &$E, &$num_cliques,
    $PROPERTY_CLIQUE_LIMIT ) {

  // get reindexed edges of component
  $E_component = array();
  for ( $i = 0; $i < sizeof($component); $i++ ) {
    $row = array();
    for ( $j = 0; $j < $i; $j++ ) {
      $row[$j] = $E[ $component[$i] ][ $component[$j] ];
      $E_component[$j][$i] = $E[ $component[$j] ][ $component[$i] ];
    }
    $row[$i] = false;
    $E_component[$i] = $row;
  }

  // get lexicographic maximum among all maximal cliques in component
  $n = sizeof($component);
  $K_0 = generate_max_maxl_clique( array(0), $n, $E_component );
  $cliques[] = array( "vertices" => $K_0, "comp_key" => $comp_key);
  $parent_clique_idx = $num_cliques;
  $num_cliques += 1;

  // recursively compute maximal cliques
  $exceeded_clique_limit = recursive_get_maximal_cliques( $n, $E_component, $cliques, $num_cliques,
      $parent_clique_idx, $PROPERTY_CLIQUE_LIMIT, $comp_key );

  return $exceeded_clique_limit;


}

?>