<?php

$conf = parse_ini_file( "config.ini", true );

$cmd = '';
if ( isset( $argv[1] ) ) {
	$cmd = str_replace( '-', '_', $argv[1] );
}

require( 'BundleCreater.php' );

$bundler = new BundleCreater( $conf, getcwd() );
if ( method_exists( $bundler, $cmd ) ) {
	$bundler->$cmd();
} else {
	echo "Available actions are:\n";
	$rObj = new ReflectionObject( $bundler );
	$rMethods = $rObj->getMethods( ReflectionMethod::IS_PUBLIC );
	foreach ( $rMethods as $rMethod ) {
		$name = str_replace( '_', '-', $rMethod->getName() );
		if ( $name === '--construct' ) {
			continue;
		}
		echo "- " . $name . "\n";
	}
}