<?php

function makeRepoConfig( $type ) {
	$data = file_get_contents( __DIR__ . "/mediawiki-$type.txt" );
	$data = preg_replace( '/#.*\n/', '', $data );

	$sections = array_map(
		'trim',
		preg_split( '/\n{2,}/', $data, -1, PREG_SPLIT_NO_EMPTY )
	);

	$repos = [];
	foreach ( $sections as $section ) {
		$lines = preg_split( '/\n/', $section );
		$repo = str_replace( ' ', '', $lines[ 0 ] );
		foreach ( $lines as $line ) {
			$parts = preg_split( '/\s*=\s*/', $line, 2 );
			if ( count( $parts ) === 2 && $parts[ 0 ] === 'file' ) {
				$repo = explode( '/', $parts[ 1 ] )[0];
			}
		}

		$repos[] = $repo;
	}

	sort( $repos );

	$config = [];
	foreach ( $repos as $name ) {
		$config[ "mediawiki-$type/$name" ] = [
			'type' => 'wmgerrit',
			'url' => "https://gerrit.wikimedia.org/r/mediawiki/$type/$name",
			'url|export' => "ssh://l10n-bot@gerrit.wikimedia.org:29418/mediawiki/$type/$name",
		];
	}

	return $config;
}

$type = $argv[ 1 ];
if ( $type === 'extensions' || $type === 'skins' ) {
	$config = makeRepoConfig( $type );
	echo json_encode( $config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
} else {
	echo "Invalid type: $type\n";
	exit( 1 );
}
