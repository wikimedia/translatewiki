<?php
declare( strict_types = 1 );

$data = stream_get_contents( STDIN );
$webhook = getenv( 'SLACK_LOG_WEBHOOK' );

try {
	if ( !$webhook ) {
		throw new Exception( 'SLACK_LOG_WEBHOOK is not set' );
	}
	$c = curl_init( $webhook );
	curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $c, CURLOPT_SSL_VERIFYPEER, true );
	curl_setopt( $c, CURLOPT_POST, true );
	curl_setopt( $c, CURLOPT_FAILONERROR, true );
	curl_setopt( $c, CURLOPT_POSTFIELDS, [ 'payload' => generateSlackMessage( $data ) ] );
	curl_exec( $c );

	if ( curl_errno( $c ) ) {
		$errorMessage = curl_error( $c );
	}
	curl_close( $c );

	if ( isset( $errorMessage ) ) {
		throw new Exception( $errorMessage );
	}
} catch ( Exception $e ) {
	exit( "$e" );
}

/**
 * Generate a Slack message
 */
function generateSlackMessage( string $data ): string {
	$slackMessage = [
		'text' => $data,
		'username' => 'TWN Logger',
	];
	return json_encode( $slackMessage );
}
