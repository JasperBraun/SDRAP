<?php

// validates presence of user inputs
function validate( array $raw_input, array $INPUT, array &$ERRORS, $BASE_DIRECTORY ) {

  $result = true;

  if ( empty( $raw_input['hostname'] ) || empty( $INPUT["HOSTNAME"] ) ) {
    $ERRORS['input']['host'] = "Valid hostname is required";
    $result = false;
  }

  if ( empty( $raw_input['username'] ) || empty( $INPUT["USERNAME"] ) ) {
    $ERRORS['input']['user'] = "Valid username is required";
    $result = false;
  }

  if ( empty( $INPUT["PASSWORD"] ) ) {
    $ERRORS['input']['pass'] = "Valid password is required";
    $result = false;
  }

  if ( empty( $raw_input['database'] ) || empty( $INPUT["DATABASE"] ) ) {
    $ERRORS['input']['data'] = "Valid database name is required";
    $result = false;
  }

  // validate file inputs
  if ( empty( $raw_input['precursor_filename'] ) || empty( $INPUT["PRECURSOR_FILENAME"] ) ) {
    $ERRORS['input']['prec'] = "Valid precursor sequence file is required";
    $result = false;
  }  elseif ( ! is_file ( $BASE_DIRECTORY . "input/precursor/{$INPUT['PRECURSOR_FILENAME']}" ) ) {
    $ERRORS['other'][] = "Could not find precursor sequence file " . $INPUT['PRECURSOR_FILENAME'] . " in the directory '" . $BASE_DIRECTORY . "input/precursor/' in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    $result = false;
  }

  if ( empty( $raw_input['product_filename'] ) || empty( $INPUT["PRODUCT_FILENAME"] ) ) {
    $ERRORS['input']['prod'] = "Valid product sequence file is required";
    $result = false;
  }  elseif ( ! is_file ( $BASE_DIRECTORY . "input/product/{$INPUT['PRODUCT_FILENAME']}" ) ) {
    $ERRORS['other'][] = "Could not find product sequence file " . $INPUT['PRODUCT_FILENAME'] . " in the directory '" . $BASE_DIRECTORY . "input/product/' in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
    $result = false;
  }

  // validate organism inputs 
  if ( empty( $raw_input['genus'] ) || empty( $INPUT["GENUS"] ) ) {
    $ERRORS['input']['genu'] = "Valid genus name is required";
    $result = false;
  }

  if ( empty( $raw_input['species'] ) || empty( $INPUT["SPECIES"] ) ) {
    $ERRORS['input']['spcs'] = "Valid species name is required";
    $result = false;
  }

  if ( empty( $raw_input['strain'] ) || empty( $INPUT["STRAIN"] ) ) {
    $ERRORS['input']['strn'] = "Valid strain name is required";
    $result = false;
  }

  if ( empty( $raw_input['taxonomy_id'] ) || empty( $INPUT["TAXONOMY_ID"] ) ) {
    $ERRORS['input']['taxo'] = "Valid taxonomy id is required";
    $result = false;
  }

  return $result;

}

?>