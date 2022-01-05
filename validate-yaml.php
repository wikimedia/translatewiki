<?php

require 'vendor/autoload.php';

$patterns = array_slice( $argv, 1 );
$files = getFiles( $patterns );
$exit = 0;

foreach ( $files as $file ) {
	echo ".";
	// Emit a PHP warning with more details in case of a failure
	$value = yaml_parse_file( $file, -1 );
	if ( $value === false ) {
		echo "File $file does not pass syntax validation\n";
		$exit = 1;
	}
}

echo "\n";

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
