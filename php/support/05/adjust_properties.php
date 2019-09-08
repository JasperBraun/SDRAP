<?php

// determines scrambling and adjusts properties
function adjust_properties( array &$properties, array $clique_properties, array $NON_SCRAMBLED ) {
  
  $non_scrambled = true;

  if ( $clique_properties['complete'] ) {
    $properties['weakly_complete'] = true;
  } else {
    $properties['strongly_complete'] = false;

    if ( $NON_SCRAMBLED['COMPLETE'] ) {
      $non_scrambled = false;
    }

  }

  if ( $clique_properties['consecutive'] ) {
    $properties['weakly_consecutive'] = true;
  } else {
    $properties['strongly_consecutive'] = false;

    if ( $NON_SCRAMBLED['CONSECUTIVE'] ) {
      $non_scrambled = false;
    }

  }

  if ( $clique_properties['ordered'] ) {
    $properties['weakly_ordered'] = true;
  } else {
    $properties['strongly_ordered'] = false;

    if ( $NON_SCRAMBLED['ORDERED'] ) {
      $non_scrambled = false;
    }

  }

  if ( $non_scrambled ) {
    $properties['weakly_non_scrambled'] = true;
  } else {
    $properties['strongly_non_scrambled'] = false;
  }

  return true;

}

?>