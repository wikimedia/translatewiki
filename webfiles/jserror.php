<?php
declare( strict_types = 1 );

// Standalone endpoint outside MediaWiki; direct request access is intentional.
// phpcs:disable MediaWiki.Usage.SuperGlobalsUsage.SuperGlobals

date_default_timezone_set( 'UTC' );

const MAX_SHORT = 2048;
const MAX_URL = 8192;
const MAX_STACK = 16384;

function inputString( array $input, string $key, int $maxLength ): string {
	$value = $input[$key] ?? '';

	if ( !is_string( $value ) ) {
		return '';
	}

	if ( strlen( $value ) > $maxLength ) {
		$value = substr( $value, 0, $maxLength );
	}

	return $value;
}

if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
	http_response_code( 405 );
	header( 'Allow: POST' );
	exit( "Method not allowed\n" );
}

$host = $_SERVER['SERVER_NAME'] ?? '';

if (
	!is_string( $host ) ||
	!preg_match( '/\A[a-zA-Z0-9.-]+\z/', $host )
) {
	http_response_code( 500 );
	exit( "Invalid server configuration\n" );
}

$file = "/www/$host/logs/error_js";

if ( !is_file( $file ) || !is_writable( $file ) ) {
	http_response_code( 500 );
	exit( "Log unavailable\n" );
}

$data = [
	'timestamp' => gmdate( 'Y-m-d\TH:i:s\Z' ),

	'type' => inputString( $_POST, 'type', MAX_SHORT ),
	'name' => inputString( $_POST, 'errorName', MAX_SHORT ),
	'message' => inputString( $_POST, 'message', MAX_SHORT ),

	'source' => inputString( $_POST, 'source', MAX_URL ),
	'line' => (int)( $_POST['lineno'] ?? 0 ),
	'column' => (int)( $_POST['colno'] ?? 0 ),
	'stack' => inputString( $_POST, 'stack', MAX_STACK ),

	'url' => inputString( $_POST, 'windowLocation', MAX_URL ),

	'wiki' => inputString( $_POST, 'wiki', MAX_SHORT ),
	'page' => inputString( $_POST, 'page', MAX_SHORT ),
	'action' => inputString( $_POST, 'action', MAX_SHORT ),
	'skin' => inputString( $_POST, 'skin', MAX_SHORT ),
	'mwVersion' => inputString( $_POST, 'mwVersion', MAX_SHORT ),

	'userAgent' => inputString( $_SERVER, 'HTTP_USER_AGENT', MAX_SHORT ),
];

$json = json_encode(
	$data,
	JSON_UNESCAPED_SLASHES |
	JSON_UNESCAPED_UNICODE |
	JSON_INVALID_UTF8_SUBSTITUTE
);

if ( $json === false ) {
	http_response_code( 500 );
	exit( "Encoding failed\n" );
}

if (
	file_put_contents(
		$file,
		$json . "\n",
		FILE_APPEND | LOCK_EX
	) === false
) {
	http_response_code( 500 );
	exit( "Logging failed\n" );
}

header( 'Content-Type: text/plain; charset=utf-8' );
echo "OK\n";
