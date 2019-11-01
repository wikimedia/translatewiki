window.onerror = function ( errorMsg, fileUrl, lineNumber ) {
	$.ajax( {
		url: mw.config.get( 'wgScriptPath' ) + '/webfiles/jserror.php',
		type: 'POST',
		data: {
			errorMsg: errorMsg,
			fileUrl: fileUrl,
			lineNumber: lineNumber,
			windowLocation: location.href,
			errorStackTrace: new Error().stack // non-standard property, not in all browsers
		}
	} ).done( function () {
		mw.log( 'window.onerror> Logged error successfully', arguments );
	} ).fail( function () {
		mw.log( 'window.onerror> Logging error failed', arguments );
	} );
};
