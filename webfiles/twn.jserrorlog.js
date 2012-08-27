window.onerror = function ( errorMsg, fileUrl, lineNumber ) {
	$.ajax({
		url: mw.config.get( 'wgScriptPath' ) + '/jserror' + mw.config.get( 'wgScriptExtension' ),
		type: 'POST',
		data: {
			errorMsg: errorMsg,
			fileUrl: fileUrl,
			lineNumber: lineNumber,
			windowLocation: window.location.href,
			errorStackTrace: new Error().stack // non-standard property, not in all browsers
		},
		success: function () {
			mw.log('window.onerror> Logged error successfully', arguments);
		},
		error: function ()  {
			mw.log('window.onerror> Logging error failed', arguments);
		}
	});
};
