function showErrors ( errors ) {

  // input errors
  $.each ( errors[ 'input' ], function ( i, e ) {
     $( '#errors' ).append ( "<li>" + e + "</li>" );
    $( '#' + i ).addClass ( "has-error" );
    $( '#' + i + '-glyph' ).addClass ( "glyphicon-remove" );
  } );

  // other errors
  $.each ( errors[ 'other' ], function ( i, e ) {
    $( '#errors' ).append ( "<li>" + e + "</li>" );
  } );

  // show errors
  $( '#alert-errors' ).removeClass ( "hide" );

  return true;
}


function ajaxSDRAP ( formData, stepJSON, state ) {

  if ( state < stepJSON.length ) {
    var stepName = stepJSON[ state ][ 'name' ],
        stepURL  = stepJSON[ state ][ 'url' ]; 

    // reset error status
    $( '#alert-errors' ) .addClass ( "hide" );
    $( '#errors' ) .empty ( );
    $( 'div' ).removeClass ( "has-error" );
    $( 'span' ).removeClass ( "glyphicon-remove" );

    // show progress list
    $( '#sdrap-' + stepName ).removeClass ( "hide" );
    $( '#sdrap-' + stepName + '-progress, #sdrap-' + stepName + '-progress div ' ).removeClass ( "hide" );

    return $.ajax ( {
        type        : 'POST',
        url         : 'php/sdrap/' + stepURL,
        data        : formData,
        dataType    : 'json',
        encode      : true,
        error: function (jqXHR, textStatus, errorThrown, exception) {
          console.log( "jqXHR: " );
          console.log( jqXHR );
        },
        timeout     : 100000000
      } ).done ( function ( data ) {

      // hide progress bar
      $( '#sdrap-' + stepName + '-progress' ).addClass ( "hide" );

      // validate response message
      if ( data[ 'success' ] ) {
        $( '#sdrap-' + stepName + '-good' ).removeClass ( "hide" );
        ajaxSDRAP ( formData, stepJSON, state + 1 );

      } else {
        $( '#sdrap-' + stepName + '-bad' ).removeClass ( "hide" );
        showErrors ( data[ 'errors' ] );
      }

      } );
  }

  return true;
}


$( document ).ready ( function ( ) { 

  // process the form
  $( '#mainForm' ).submit ( function ( event ) {

    // reset progress list-groups
    $( '#alert-errors' ).addClass ( "hide" );
    $( '#sdrap-progress a, #sdrap-progress a div' ).addClass ( "hide" );

    var inputs = {
            'hostname' : $( '#database-hostname' ).val(),
            'username' : $( '#database-username' ).val(),
            'password' : $( '#database-password' ).val(),
            'database' : $( '#database-database' ).val(),
            'precursor_filename' : $( '#sequences-precursor' ).val(),
            'precursor_delimiter' : $( '#precursor-delimiter' ).val(),
            'product_filename' : $( '#sequences-product' ).val(),
            'product_delimiter' : $( '#product-delimiter' ).val(),
            'genus' : $( '#organism-genus' ).val(),
            'species' : $( '#organism-species' ).val(),
            'strain' : $( '#organism-strain' ).val(),
            'taxonomy_id' : $( '#organism-taxonomy' ).val(),
            'telo_pattern' : $( '#organism-telomere' ).val(),
            'telo_error_limit' : $( '#telomere-errorLimit' ).val(),
            'telo_buffer_limit' : $( '#telomere-bufferLimit' ).val(),
            'telo_max_length' : $( '#telomere-maxLength' ).val(),
            'telo_max_offset' : $( '#telomere-maxOffset' ).val(),
            'telo_min_length' : $( '#telomere-minLength' ).val(),
            'hsp_min_length' : $( '#minimum-HSPlength' ).val(),
            'pre_match_min_bitscore' : $( '#minimum-bitscore' ).val(),
            'pre_match_min_pident' : $( '#minimum-pident' ).val(),
            'pre_match_min_coverage_addition' : $( '#minimum-coverageAddition' ).val(),
            'merge_tolerance' : $( '#merge-tolerance' ).val(),
            'merge_max_gap' : $( '#maximum-mergeGap' ).val(),
            'gap_min_length' : $( '#minimum-gap' ).val(),
            'compute_pointers' : $( '#compute-pointers' ).prop('checked'),
            'pointer_min_length' : $( '#minimum-pointerLength' ).val(),
            'add_match_min_bitscore' : $( '#minimum-matchBitscore' ).val(),
            'add_match_min_pident' : $( '#minimum-matchPident' ).val(),
            'add_match_min_prod_segment_overlap' : $( '#minimum-mdsOverlap' ).val(),
            'fragment_min_prod_segment_overlap' : $( '#minimum-fragmentOverlap' ).val(),
            'property_min_coverage' : $( '#minimum-coverage' ).val(),
            'property_max_match_overlap' : $( '#maximum-bpOverlap' ).val(),
            'property_clique_limit' : $( '#maximum-arrangementNumber' ).val(),
            'scr_complete' : $( '#scrambling-complete' ).prop('checked'),
            'scr_consecutive' : $( '#scrambling-consecutive' ).prop('checked'),
            'scr_ordered' : $( '#scrambling-ordered' ).prop('checked'),
            'output_min_coverage' : $( '#minimum-macCoverage' ).val(),
            'output_use_alias' : $( '#use-alias' ).prop('checked'),
            'output_gaps' : $( '#output-gaps' ).prop('checked'),
            'output_fragments' : $( '#output-fragments' ).prop('checked'),
            'output_give_complement' : $( '#give-complement' ).prop('checked'),
            'output_min_complement_length' : $( '#minimum-complement' ).val(),
            'output_give_summary' : $( '#giveSummary' ).prop('checked')
    };

    var sdrapSteps = [
          { 'name' : 'connect',  'url' : '01_create_database.php' },
          { 'name' : 'telomere', 'url' : '02_upload_datasets.php' },
          { 'name' : 'blast',    'url' : '03_blast_sequences.php' },
          { 'name' : 'match',      'url' : '04_compute_annotations.php' },
          { 'name' : 'properties',  'url' : '05_compute_properties.php' },
          { 'name' : 'output',      'url' : '06_output_annotation.php' }
        ];

    ajaxSDRAP( inputs, sdrapSteps, 0 );

    // prevent page refresh of form
    event.preventDefault ( );

  } );

} );
