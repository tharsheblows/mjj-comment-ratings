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

});

$( '.clear-rating' ).on( 'click', function ( e ){
	$( this ).closest( '.choose-rating' ).find( '.set' ).removeClass( 'set' ).addClass( 'choose' );
	$( this ).closest( '.choose-rating' ).find( '.colour-star' ).removeClass( 'colour-star' ).addClass( 'grey-star' );
});

$( '#initial-rating .choose-rating' ).on( 'click', '.star-div', function( e ){
	var rating = $( this ).attr( 'data-rating' );
	$( '#review-rating' ).attr( 'value', rating );
});

$( '#initial-rating .clear-rating' ).on( 'click', function ( e ){
	$( '#review-rating' ).attr( 'value', 0 );
});

