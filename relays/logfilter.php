<?php
/**
 * A script to forward error log messages with some rate limiting.
 *
 * @author Niklas Laxstrom
 * @license GPL-2.0-or-later
 * @file
 */

$file = $_SERVER['argv'][1] ?? '/www/translatewiki.net/logs/error_php';

if ( !is_readable( $file ) ) {
	exit( "OMG\n" );
}

$handle = fopen( $file, "rt" );
fseek( $handle, 0, SEEK_END );
while ( true ) {
	$count = 0;
	$line = false;
	while ( !feof( $handle ) ) {
		$count++;
		$input = fgets( $handle );
		if ( $input !== false && $line === false ) {
			$line = $input;
		}
	}

	// I don't know why this is needed
	fseek( $handle, 0, SEEK_END );

	if ( $line !== false ) {
		// Remove trailing newlines
		$line = rtrim( $line );
		$note = '';
		if ( $count > 2 ) {
			$count -= 2;
			$note = "($count lines skipped) ";
		}
		if ( mb_strlen( $line ) > 400 ) {
			$line = mb_substr( $line, 0, 400 ) . '...';
		}
		echo "$line $note\n";
	}

	sleep( $count ? 30 : 1 );
}
