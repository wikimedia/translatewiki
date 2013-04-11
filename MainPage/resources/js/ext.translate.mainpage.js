/**
 * Dynamic behavior for the main page
 * @author Niklas Laxström
 * @license GPL2+
 */
( function ( $, mw ) {
	'use strict';

	$( document ).ready( function () {
		var $tiles, language;

		$tiles = $( '.project-tile' );

		$tiles.hover(
			function () { $( this ).find( '.project-actions' ).removeClass( 'hide' ); },
			function () { $( this ).find( '.project-actions' ).addClass( 'hide' ); }
		);

		if ( $tiles.length !== 8 ) {
			// We have less than 8 tiles, so all are shown
			return;
		}

		language = mw.config.get( 'wgUserLanguage' );

		// Take the last tile
		$tiles.eq( 7 )
			.empty()
			.addClass( 'more' )
			.text( 'Show more projects...' )
			.one( 'click', function () {
				$.when(
					mw.translate.loadMessageGroups()
				).then( function () {
					$tiles.trigger( 'dataready.translate' );
				} );
			} )
			.msggroupselector( {
				language: language,
				onSelect: mw.translate.changeGroup,
				position: {
					my: 'left bottom',
					at: 'right bottom+275'
				}
			} );
	} );
}( jQuery, mediaWiki ) );
