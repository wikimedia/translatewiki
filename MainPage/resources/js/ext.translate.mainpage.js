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
			.text( mw.msg( 'twnmp-show-more-projects' ) )
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

// Sign up form
( function ( $, mw ) {
	'use strict';

	$( document ).ready( function () {
		var $form = $( '.login-widget' );

		$form.on( 'submit', function ( e ) {
			var options, req,
				api = new mw.Api(),
				username = $form.find( 'input[name="wpName"]' ).val(),
				email = $form.find( 'input[name="wpEmail"]' ).val(),
				password = $form.find( 'input[name="wpPassword"]' ).val();

			e.preventDefault();

			options = {
				action: 'translatesandbox',
				'do': 'create',
				username: username,
				email: email,
				password: password,
				token: $form.find( 'input[name="wpSandboxToken"]' ).val(),
			};

			req = api.post( options );
			req.fail( function () { window.alert( 'Failure' ); } )
			req.done( function () {
				var options, req,
					api = new mw.Api();

				options = {
					action: 'login',
					lgname: username,
					lgpassword: password
				}

				req = api.post( options );
				req.fail( function () { window.alert( 'Failure2' ); } )
				req.done( function ( data ) {
					var req,
						api = new mw.Api();

					req = api.post( $.extend( {}, { lgtoken: data.login.token }, options ) );
					req.done( function ( ) {
						window.location.reload();
					} );
				} );
			} );
		} );
	} );
}( jQuery, mediaWiki ) );
