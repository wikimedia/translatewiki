( function () {
	const maxErrors = 10;
	const seenErrors = new Set();
	let errorCount = 0;

	function safeString( value ) {
		try {
			const json = JSON.stringify( value );

			if ( json !== undefined ) {
				return json;
			}
		} catch ( e ) {
			// Fall through for circular structures and unsupported values.
		}

		try {
			return String( value );
		} catch ( e ) {
			return '[unprintable value]';
		}
	}

	function logJavaScriptError( data ) {
		if ( errorCount >= maxErrors ) {
			return;
		}

		const key = [
			data.type,
			data.message,
			data.source,
			data.lineno,
			data.colno
		].join( '|' );

		if ( seenErrors.has( key ) ) {
			return;
		}

		seenErrors.add( key );
		errorCount++;

		$.ajax( {
			url: mw.config.get( 'wgScriptPath' ) + '/webfiles/jserror.php',
			type: 'POST',
			data: Object.assign( {
				windowLocation: location.href,
				wiki: mw.config.get( 'wgDBname' ),
				page: mw.config.get( 'wgPageName' ),
				action: mw.config.get( 'wgAction' ),
				skin: mw.config.get( 'skin' ),
				mwVersion: mw.config.get( 'wgVersion' )
			}, data )
		} ).then( null, () => {
			mw.log.warn( 'JavaScript error logging failed' );
		} );
	}

	addEventListener( 'error', ( event ) => {
		// Ignore failed resource loads such as <img> and <script>.
		if ( !( event instanceof ErrorEvent ) ) {
			return;
		}

		const error = event.error;

		logJavaScriptError( {
			type: 'error',
			message: event.message || ( error && error.message ) || '',
			source: event.filename || '',
			lineno: event.lineno || 0,
			colno: event.colno || 0,
			errorName: error && error.name || '',
			stack: error && error.stack || ''
		} );
	} );

	addEventListener( 'unhandledrejection', ( event ) => {
		const reason = event.reason;
		const isError = reason instanceof Error;

		logJavaScriptError( {
			type: 'unhandledrejection',
			message: isError ? reason.message : safeString( reason ),
			source: '',
			lineno: 0,
			colno: 0,
			errorName: isError ? reason.name : '',
			stack: isError && reason.stack || ''
		} );
	} );
}() );
