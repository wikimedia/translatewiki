( function ( $, mw ) {
	'use strict';

	function search () {
		window.location.href = new mw.Uri( mw.util.wikiGetlink( 'Special:SearchTranslations' ) )
			.extend( { query: $( 'input.searchbox' ).val() } );
	}
	$( document ).ready( function () {
		$( '.twn-mainpage-search button' ).on( 'click', search );
		$( '.twn-mainpage-search input' ).on( 'keyup', function( e ) {
			if ( e.which === 13 ) { // Enter key
				search();
			}
		} );
	} );
}( jQuery, mediaWiki ) );
