#!/usr/bin/env php
<?php

$scripts = glob( __DIR__ . '/bin/*' );

/**
 * bash -n: Read commands but do not execute them.
 *
 * @return True || False
 */
function bashread( $file ) {
	$out = '';
	$exitCode = 0;
	exec(
		'/usr/bin/env bash -n ' . escapeshellarg( $file ),
		$out, $exitCode
	);
	return $exitCode;
}

$exit = 0;
foreach ( $scripts as $script ) {
	if ( is_dir( $script ) || $script === __FILE__ ) {
		continue;
	}
	$exit |= bashread( $script );
}

if ( $exit === 0 ) {
	print "All bin scripts pass `bash -n`.\n";
	exit( 0 );
} else {
	print "Some bin scripts have errors.\n";
	exit( $exit );
}
