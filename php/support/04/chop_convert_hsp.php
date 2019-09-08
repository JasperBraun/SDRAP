<?php

// chops off telomeric portions of hsp, if any,
// and converts to correct data types
function chop_convert_hsp( array $full_hsp, array $inter_tel_interval ) {

  $hsp = $full_hsp;
  unset( $hsp['bitscore'] );
  unset( $hsp['pident'] );

  $prod_left_chop = max( 0, $inter_tel_interval['start'] - intval( $full_hsp['prod_start'] ) );
  $prod_right_chop = max( 0, intval( $full_hsp['prod_end'] ) - $inter_tel_interval['end'] );

  if ( $full_hsp['orientation'] === '+' ) {
    $prec_left_chop = $prod_left_chop;
    $prec_right_chop = $prod_right_chop;
  } else {
    $prec_left_chop = $prod_right_chop;
    $prec_right_chop = $prod_left_chop;
  }

  $hsp['hsp_id'] = array( $full_hsp['hsp_id'] );
  $hsp['prec_nuc_id'] = $full_hsp['prec_nuc_id'];
  $hsp['prec_start'] = intval( $full_hsp['prec_start'] ) + $prec_left_chop;
  $hsp['prec_end'] = intval( $full_hsp['prec_end'] ) - $prec_right_chop;
  $hsp['prod_start'] = intval( $full_hsp['prod_start'] ) + $prod_left_chop;
  $hsp['prod_end'] = intval( $full_hsp['prod_end'] ) - $prod_right_chop;
  $hsp['orientation'] = $full_hsp['orientation'];
  $hsp['length'] = intval( $full_hsp['length'] ) - $prod_left_chop - $prod_right_chop;

  return $hsp;

}

?>