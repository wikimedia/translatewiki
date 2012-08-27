<?php

$stamp = strftime( '%FT%T%Z' );
$name = @$_REQUEST['fileUrl'];
$line = @$_REQUEST['lineNumber'];
$message = @$_REQUEST['errorMsg'];
$url = @$_REQUEST['windowLocation'];
$stack = @$_REQUEST['errorStackTrace'];
$ip = @$_SERVER['REMOTE_ADDR'];
$agent = @$_SERVER['HTTP_USER_AGENT'];

$file = fopen( '../logs/error_js', 'a' );
fwrite( $file, "$stamp\t$message\t$name:$line\n>$ip\t$agent\n>URL: $url\n>STACK: $stack\n" );
fclose( $file );
echo "Thank you sir obvious.\n";
