<?php

// returns reindexed array containing values from $a not in $b
function my_array_diff ( array &$a, array &$b ) {

  $map = array();
  $out = array();

  foreach ( $a as $val ) { $map[$val] = 1; }
  foreach ( $b as $val ) {
    if ( isset( $map[$val] ) ) { $map[$val] = 0; }
  }
  foreach ( $map as $val => $ok ) {
    if ( $ok ) { $out[] = $val; }
  }
  return $out;

}

// returns reindexed array containing values from the intersection of $a, $b, $c and $d
function my_array_intersect( array &$a, array &$b, array &$c, array &$d ) {

  $result = array();
  $b_flipped = array_flip( $b );
  $c_flipped = array_flip( $c );
  $d_flipped = array_flip( $d );

  foreach ( $a as $val ) {
    if ( isset( $b_flipped[$val] ) && isset( $c_flipped[$val] ) && isset( $d_flipped[$val] ) ) {
      $result[] = $val;
    }
  }

  return $result;

}

// returns dot product of two binary arrays $a and $b of same size
function my_inner_prod ( &$a, &$b, $n ) {

  $sum = 0;
  $i = 0;
  while ( $i < $n ) {
    $sum += $a[$i] * $b[$i];
    $i += 1;
  }
  return $sum;

}

// returns lexicographically maximum maximal clique containing $K
function generate_max_maxl_clique( $K, $n, &$E ) {

  $result = array();
  $K_flipped = array_flip( $K );
  $added = array();
  $j = 0;
  while ( $j < $n ) {

    if ( isset( $K_flipped[$j] ) ) { // $j is in $K

      $result[] = $j;

    } else { // $j is not in $K

      $all_adjacent = true;
      foreach ( $K as $k ) { // check if $j is adjacent to verts in $K

        if ( !$E[$j][$k] ) { // $j is not adjacent to $k
          $all_adjacent = false;
          break;
        }

      }
      if ( $all_adjacent ) {
        foreach ( $added as $k ) { // check if $j is adjacent to already added verts

          if ( !$E[$j][$k] ) { // $j is not adjacent to $k
            $all_adjacent = false;
            break;
          }

        }

        if ( $all_adjacent ) { // $k is adjacent to verts in $K and already added verts
          $result[] = $j;
          $added[] = $j;
        }
      }

    }

    $j += 1;

  }

  return $result;

}

// returns lexicographically maximum maximal clique containing $i and those
// vertices of $K less than $i, which are adjacent to $i
function get_child ( $K, $i, &$E, $n ) {

  // collect $i and those vertices of $K less than $i, which are adjacent to $i
  $clique = array();
  foreach ( $K as $k ) {

    if ( $k > $i ) { // all following vertices of $K will be > $i
      break;
    } else if ( $E[$k][$i] ) {
      $clique[] = $k;
    }

  }
  $clique[] = $i;

  // return lexicographically maximum maximal clique containing $clique
  return generate_max_maxl_clique( $clique, $n, $E );

}

// returns the maximum vertex $i, such that $K \cap {0, 1, ..., $i-1} generates
// a lexicographically maximum maximal clique different from $K
function min_prefix ( $K, &$E ) {

  $max_prefix = -1; // max vertex, such that prefix up to (and including) that vertex
                    // of K generates lexicog. largest maximal clique different from K
  $K_flipped = array_flip( $K );
  $j = 0;
  $max_k = end($K);
  reset($K);
  while ( $j < $max_k ) {

    if ( ! isset( $K_flipped[$j] ) ) {

      $tmp_max_prefix = -1;

      foreach ( $K as $key => $k ) {

        if ( $E[$j][$k] ) {

          $tmp_max_prefix = $K[$key + 1] - 1;

        } else {

          break;

        }

      }

      if ( $tmp_max_prefix > $max_prefix ) {

        $max_prefix = $tmp_max_prefix;

      }

    }

    $j += 1;

  }

  if ( $max_prefix < 0 ) {
    return $max_prefix;
  } else {
    return $max_prefix + 1;
  }

}

// returns all vertices i satisfying (c) from doi mentioned below
function get_c ( $K, $n, &$E ) {

  $C = array();
  $K_flipped = array_flip( $K );
  $i = 0;

  while ( $i < $n ) { // check all vertices $i

    $satisfies_c = true; // unless there is a $j, satisfying (c-1), (c-2) and (c-3)

    $j = 0;
    while ( $j < $i ) { // check all $j satisfying (c-1)

      if ( $E[$i][$j] && ! isset( $K_flipped[$j] ) ) { // (c-2) -- (c-3) require_onces {i,j} in E

        $all_adjacent = true;
        foreach ( $K as $k ) {

          if ( $k >= $i ) {
            break;
          } else if ( $E[$i][$k] && ! $E[$j][$k] ) {
            $all_adjacent = false;
            break;
          }

        }
        if ( $all_adjacent ) { // (c-3) -- since already {i,j} in E
          $satisfies_c = false;
          break;
        }

      }

      $j += 1;

    }

    if ( $satisfies_c ) {
      $C[] = $i;
    }

    $i += 1;

  }

  return $C;

}

// returns all vertices i satisfying (d) from doi mentioned below
function get_d ( $K, $n, &$E ) {

  $D = array();
  $K_flipped = array_flip($K);
  $i = 0;

  while ( $i < $n ) { // check all indices

    $satisfies_d = true; // unless there is a $j, satisfying (d-1), (d-2), (d-3) and (d-4)

    $j = 0;
    while ( $j < $i ) { // check all $j satisfying (d-1)

      if ( ! isset( $K_flipped[$j] ) ) { // (d-2)

        $all_adjacent = true;
        foreach ( $K as $k ) {

          if ( $k > $i ) {
            break;
          } else if ( $k <= $j && ! $E[$j][$k] ) { // for (d-3)
            $all_adjacent = false;
            break;
          } else if ( $E[$i][$k] && ! $E[$j][$k] ) { // for (d-4)
            $all_adjacent = false;
            break;
          }

        }
        if ( $all_adjacent ) { // (d-3) and (d-4)
          $satisfies_d = false;
          break;
        }

      }

      $j += 1;

    }

    if ( $satisfies_d ) {
      $D[] = $i;
    }

    $i += 1;

  }

  return $D;

}

?>