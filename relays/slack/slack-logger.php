<?php
declare( strict_types = 1 );

$webhook = getenv( 'SLACK_WEBHOOK' );
$runtimeDir = getenv( 'RUNTIME_DIRECTORY' );
$pipePath = "$runtimeDir/pipe";

try {
	if ( !$webhook ) {
		throw new Exception( 'SLACK_WEBHOOK is not set' );
	}

	// Open the named pipe for reading in non-blocking mode
	$pipe = fopen( $pipePath, 'r+' ); // r+ prevents blocking when no writers
	if ( !$pipe ) {
		throw new RuntimeException( "Failed to open named pipe at $pipePath" );
	}
	stream_set_blocking( $pipe, false );

	$read = [ $pipe ];
	$write = $except = [];
	$waitTimeInSeconds = 5;

	while ( true ) {
		$incomingStream = stream_select( $read, $write, $except, $waitTimeInSeconds );

		if ( $incomingStream === false ) {
			// An error occurred, exit the loop
			throw new RuntimeException( 'Error while reading from named pipe' );
		}

		if ( $incomingStream > 0 ) {
			// There is data to read from the pipe
			$data = fgets( $pipe );

			if ( $data !== false ) {
				sendSlackMessage( $webhook, generateSlackMessage( $data ) );
			}
		}
		$read = [ $pipe ];

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
 * @throws Exception
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
