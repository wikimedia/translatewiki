<?php

/*
 * To print stats:
 *  mlebstats /var/lib/awstats
 * To email stats:
 *  mlebstats /var/lib/awstats email1 email2...
 */

$awstatsDir = $argv[1];

$emails = array_slice( $argv, 2 );

function startsWith( $haystack, $needle ) {
	return substr( $haystack, 0, strlen( $needle ) ) === $needle;
}

function endsWith( $haystack, $needle ) {
	if ( $needle === '' ) {
		return;
	}
	return substr( $haystack, -strlen( $needle ) ) === $needle;
}

function getStatsFromFile( $path ) {
	$offsetToDownloads = 0;
	$stats = [
		// filename => downloads
	];

	$file = new SplFileObject( $path );
	$file->setFlags(
		  SplFileObject::READ_AHEAD
		| SplFileObject::DROP_NEW_LINE
		| SplFileObject::SKIP_EMPTY
	);

	while ( !$file->eof() ) {
		$line = $file->current();

		if ( startsWith( $line, 'POS_DOWNLOADS ' ) ) {
			$spaceInTheMidle = stripos( $line, ' ' );
			$offsetToDownloads = (int)substr( $line, $spaceInTheMidle + 1 );
			break;
		}

		if ( $line === 'END_MAP' ) {
			throw new Exception( "Unable to parse file $path" );
		}

		$file->next();
	}

	$file->fseek( $offsetToDownloads );

	while ( !$file->eof() ) {
		$line = $file->current();
		if ( startsWith( $line, '/mleb/' ) ) {
			[ $filename, $downloads, $hits, $size ] = explode( ' ', $line );
			if ( endsWith( $filename, '.tar.bz2' ) ) {
				$stats[$filename] = $downloads;
			}
		}

		if ( $line === 'END_DOWNLOADS' ) {
			break;
		}

		$file->next();
	}

	return $stats;
}

function getFiles( $dir ) {
	return glob( "$dir/awstats*.txt" );
}

function getCuteName( $filename ) {
	$matches = null;
	preg_match( '/MediaWikiLanguageExtensionBundle-(\d{4}.\d\d)/', $filename, $matches );
	return $matches[1];
}

function arrayToList( $array ) {
	array_walk( $array, static function ( &$value, $key ) {
		$value = [ $key, $value ];
	} );
	return array_values( $array );
}

function printAllTime( $stats ) {
	$combined = [];
	foreach ( $stats as $month ) {
		foreach ( $month as $file => $downloads ) {
			if ( !isset( $combined[$file] ) ) {
				$combined[$file] = 0;
			}
			$combined[$file] += $downloads;
		}
	}

	// Awful hack to able to iterate both arrays at once
	arsort( $combined );
	$popular = arrayToList( $combined );
	ksort( $combined );
	$sorted = arrayToList( $combined );

	printf( "%-13s   |   %-13s\n", 'Most popular', 'In order' );
	echo "---------------------------------\n";
	$len = count( $combined );
	for ( $i = 0; $i < $len; $i++ ) {
		[ $pk, $pv ] = $popular[$i];
		[ $sk, $sv ] = $sorted[$i];

		printf(
			"%7s %5d   |   %7s %5d \n",
			getCuteName( $pk ),
			$pv,
			getCuteName( $sk ),
			$sv
		);
	}
}

function printMonth( $stats ) {
	$total = 0;
	foreach ( $stats as $filename => $downloads ) {
		$total += $downloads;
		printf( "%7s %5d\n", getCuteName( $filename ), $downloads );
	}

	printf( "Total: %d downloads\n", $total );
}

ob_start();

$skipped = [];
$stats = [];

foreach ( getFiles( $awstatsDir ) as $path ) {
	$file = basename( $path );
	$year = (int)substr( $file, 9, 4 );
	$month = (int)substr( $file, 7, 2 );

	if ( $year < 2013 || ( $year === 2013 && $month < 10 ) ) {
		// No download stats in expected format available
		continue;
	}

	try {
		$stats["$year-$month"] = getStatsFromFile( $path );
	} catch ( Exception $e ) {
		$skipped[] = $file;
	}
}

$lastmonth = mktime( 0, 0, 0, date( 'm' ) - 1, 1, date( 'Y' ) );
$index = date( 'Y-n', $lastmonth );
$pretty = date( 'Y-m', $lastmonth );

if ( isset( $stats[$index] ) ) {
	echo "Stats for last month ($pretty)\n\n";
	printMonth( $stats[$index] );
	echo "\n\n";
} else {
	echo "Unable to print stats for $pretty\n\n";
}

echo "All time statistics since 2013-10\n\n";

printAllTime( $stats );

if ( $skipped ) {
	$skipped = implode( ', ', $skipped );
	echo "\n\nThe following files were skipped: $skipped\n";
}

$buffer = ob_get_contents();
ob_end_clean();

if ( $emails ) {
	mail( implode( ', ', $emails ), 'MLEB download statistics', $buffer );
} else {
	echo $buffer;
}
