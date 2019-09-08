<?php

// determines whether match_a < match_b by series of sort keys
function match_order ( $match_a, $match_b ) {

  if ( $match_a['prec_nuc_id'] < $match_b['prec_nuc_id'] ) { return -1; }
  else if ( $match_a['prec_nuc_id'] > $match_b['prec_nuc_id'] ) { return 1; }
  else if ( $match_a['prod_start'] < $match_b['prod_start'] ) { return -1; }
  else if ( $match_a['prod_start'] > $match_b['prod_start'] ) { return 1; }
  else if ( $match_a['prod_end'] < $match_b['prod_end'] ) { return -1; }
  else if ( $match_a['prod_end'] > $match_b['prod_end'] ) { return 1; }
  else if ( $match_a['prec_start'] < $match_b['prec_start'] ) { return -1; }
  else if ( $match_a['prec_start'] > $match_b['prec_start'] ) { return 1; }
  else if ( $match_a['prec_end'] < $match_b['prec_end'] ) { return -1; }
  else if ( $match_a['prec_end'] > $match_b['prec_end'] ) { return 1; }
  else { return 0; }

}

?>