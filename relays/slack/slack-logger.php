<?php
declare( strict_types = 1 );

$webhook = getenv( 'SLACK_LOG_WEBHOOK' );

try {
	if ( !$webhook ) {
		throw new Exception( 'SLACK_LOG_WEBHOOK is not set' );
	}
	$read = [ STDIN ];
	$write = $except = [];
	$waitTimeInSeconds = 5;

	stream_set_blocking( STDIN, false ); // Set STDIN to non-blocking mode
	while ( true ) {
		$incomingStream = stream_select( $read, $write, $except, $waitTimeInSeconds );

		if ( $incomingStream === false ) {
			// An error occurred, exit the loop
			throw new RuntimeException( "Error while reading STDIN" );
		}

		if ( $incomingStream > 0 ) {
			// There is data to read from STDIN
			$data = fgets( STDIN );

			if ( $data !== false ) {
				sendSlackMessage( $webhook, generateSlackMessage( $data ) );
			}
		}
		$read = [ STDIN ];

	}
} catch ( Exception $e ) {
	exit( "$e" );
}

/**
 * Generate a Slack message
 */
function generateSlackMessage( string $data ): string {
	$slackMessage = [
		'text' => "```$data```",
		'username' => 'Translatewiki Logger',
	];
	return json_encode( $slackMessage );
}

/**
 * Send a Slack message via curl
 */
function sendSlackMessage( string $webhookUrl, string $payload ): void {
	$c = curl_init( $webhookUrl );
	curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $c, CURLOPT_SSL_VERIFYPEER, true );
	curl_setopt( $c, CURLOPT_POST, true );
	curl_setopt( $c, CURLOPT_FAILONERROR, true );
	curl_setopt( $c, CURLOPT_POSTFIELDS, [ 'payload' => $payload ] );
	curl_exec( $c );

	if ( curl_errno( $c ) ) {
		$errorMessage = curl_error( $c );
	}

	if ( isset( $errorMessage ) ) {
		throw new Exception( $errorMessage );
	}
}
