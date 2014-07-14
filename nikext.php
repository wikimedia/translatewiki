<?php

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'Translatewiki.net Magic',
	'version' => '2014-07-14',
	'description' => 'Implements some translatewiki.net specific magic',
	'author' => 'Niklas Laxström',
);

global $wgHooks;
$wgHooks['MagicWordwgVariableIDs'][] = function ( &$vars ) {
	$vars[] = 'MAG_UILANGCODEx';
	return true;
};

$wgHooks['LanguageGetMagic'][] = function ( &$raw ) {
	$raw['MAG_UILANGCODEx'] = array( 1, 'UILANGCODE' );
	return true;
};

$wgHooks['ParserGetVariableValueSwitch'][] = function ( &$parser, &$varCache, &$index, &$ret ) {
	global $wgLang;
	if ( $index === 'MAG_UILANGCODEx' ) {
		$ret = $varCache[$index] = $wgLang->getCode();
	}
	return true;
};
