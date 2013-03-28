( function ( $, mw ) {
	'use strict';

	$( document ).ready( function () {
		$( '.twn-mainpage-search button' ).on( 'click', function () {
			window.location.href = new mw.Uri( mw.util.wikiGetlink( 'Special:SearchTranslations' ) )
				.extend( { query: $( 'input.searchbox' ).val() } );
		} );
	} );
}( jQuery, mediaWiki ) );
