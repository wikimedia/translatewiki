<?php

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'Translatewiki.net Magic',
	'version' => '2008-02-01',
	'description' => 'Implements some translatewiki.net specific magic',
	'author' => 'Niklas Laxström',
);

global $wgHooks;
$wgHooks['MagicWordwgVariableIDs'][] = 'uiExtraMagicHookVariables';
$wgHooks['LanguageGetMagic'][] = 'uiExtraMagicHookRaw';
$wgHooks['ParserGetVariableValueSwitch'][] = 'uiExtraMagicHookSwitch';

function uiExtraMagicHookVariables( &$vars ) {
	$vars[] = 'MAG_UILANGCODEx';
	$vars[] = 'MAG_UILANGx';
	return true;
}

function uiExtraMagicHookRaw( &$raw ) {
	$raw['MAG_UILANGCODEx'] = array( 1, 'UILANGCODE' );
	$raw['MAG_UILANGx'] = array( 1, 'UILANG' );
	return true;
}

function uiExtraMagicHookSwitch( &$parser, &$varCache, &$index, &$ret ) {
	global $wgLang;
	if ( $index === 'MAG_UILANGCODEx' ) {
		$ret = $varCache[$index] = $wgLang->getCode();
	} elseif ( $index === 'MAG_UILANGx' ) {
		$ret = $varCache[$index] = 'puga';
	}
	return true;
}
