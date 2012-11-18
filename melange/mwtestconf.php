<?php

ini_set( 'display_errors',          1);
ini_set( 'ignore_repeated_errors',  1);
error_reporting( E_ALL | E_STRICT );
date_default_timezone_set('UTC');

$wgIncludeLegacyJavaScript = false;
$wgLegacyJavaScriptGlobals = false;
$wgEnableJavaScriptTest    = true;

$wgShowExceptionDetails = true;
$wgShowSQLErrors        = true;
$wgDevelopmentWarnings  = true;

$EXT = "$IP/../extensions";
require( "$EXT/Translate/Translate.php" );
require( "$EXT/cldr/cldr.php" );
