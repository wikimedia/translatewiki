<?php

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

require 'vendor/autoload.php';

$patterns = array_slice( $argv, 1 );
$files = getFiles( $patterns );
$exit = 0;

foreach ( $files as $file ) {
	echo $file;
	$contents = file_get_contents( $file );
	// This is more strict than the spec
	$documents = preg_split( "/^---$/m", $contents, -1, PREG_SPLIT_NO_EMPTY );

	$ok = 0;
	foreach ( $documents as $i => $document ) {
		try {
			$value = Yaml::parse( $document );
			echo ".";
		} catch ( ParseException $exception ) {
			echo " FAIL\n";
			printf( "Document #%d fails to parse: %s\n", $i + 1, $exception->getMessage() );
			$ok = $exit = 1;
			continue;
		}
	}
	if ( $ok === 0 ) {
		echo " OK\n";
	}
}

function getFiles( array $patterns ): iterable {
	foreach ( $patterns as $p ) {
		if ( is_file( $p ) ) {
			yield $p;
		} elseif ( is_dir( $p ) ) {
			$directory = new RecursiveDirectoryIterator( $p );
			$iterator = new RecursiveIteratorIterator( $directory );
			$filteredIterator = new RegexIterator( $iterator, '/\.ya?ml$/i', RecursiveRegexIterator::GET_MATCH );
			foreach ( $filteredIterator as $k => $v ) {
				yield $k;
			}
		} else {
			echo "$p is not a file or directory\n";
			exit( 2 );
		}
	}
}

exit( $exit );
