<?php

date_default_timezone_set( 'UTC' );

$stamp = strftime( '%FT%T%Z' );
$name = @$_REQUEST['fileUrl'];
$line = @$_REQUEST['lineNumber'];
$message = @$_REQUEST['errorMsg'];
$url = @$_REQUEST['windowLocation'];
$stack = @$_REQUEST['errorStackTrace'];
$ip = @$_SERVER['REMOTE_ADDR'];
$agent = @$_SERVER['HTTP_USER_AGENT'];
$host = $_SERVER['HTTP_HOST'];

$file = "/www/$host/logs/error_js";
if ( file_exists( $file ) ) {
	$file = fopen( $file, 'a' );
	fwrite( $file, "$stamp\t$message\t$name:$line\n>$ip\t$agent\n>URL: $url\n>STACK: $stack\n" );
	fclose( $file );
	echo "Thank you sir obvious.\n";
} else {
	echo "Bunny is sad.\n";
}
