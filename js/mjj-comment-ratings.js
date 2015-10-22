/*!  - v1.0.0 - 2015-10-21
 * 
 * Copyright (c) 2015; * Licensed GPLv2+ */
 jQuery('document').ready( function ($){
// the jquery bit, jQuery(\'document\').ready( function ($){...}, is loaded through grunt concat

$( '.choose-rating' ).on( 'mouseenter', '.choose', function(){
	$( this ).prevAll().andSelf().removeClass( 'grey-star' ).addClass( 'colour-star' );
});

$( '.choose-rating' ).on( 'mouseleave', '.choose', function(){
	$( this ).prevAll().andSelf().removeClass( 'colour-star' ).addClass( 'grey-star' );
});

$( '.choose-rating' ).on( 'click', '.star-div', function( e ){
	// stop the hover effect
	$( '.choose' ).removeClass( 'choose' ).addClass( 'set' );

	// if you click it again, update it
	$( this ).prevAll().andSelf().removeClass( 'grey-star' ).addClass( 'colour-star' );
	$( this ).nextAll().removeClass( 'colour-star' ).addClass( 'grey-star' );

	var rating = $( this ).attr( 'data-rating' );
	$( '#review-rating' ).attr( 'value', rating );
});

$( '.clear-rating' ).on( 'click', function ( e ){
	$( '.choose-rating .set' ).removeClass( 'set' ).addClass( 'choose' );
	$( '.choose-rating .colour-star' ).removeClass( 'colour-star' ).addClass( 'grey-star' );
	$( '#review-rating' ).attr( 'value', 0 );
});

});