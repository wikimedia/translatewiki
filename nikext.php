<?php

$wgExtensionCredits['parserhook'][] = [
	'name' => 'Translatewiki.net Magic',
	'path' => __FILE__,
	'version' => '2018-06-15',
	'description' => 'Implements <nowiki>{{UILANGCODE}}</nowiki>',
	'author' => 'Niklas Laxström',
];

$wgExtensionMessagesFiles['translatewiki-magic'] = __DIR__ . '/nikext.i18n.magic.php';

$wgHooks['MagicWordwgVariableIDs'][] = function ( &$vars ) {
	$vars[] = 'MAG_UILANGCODEx';
};

$wgHooks['ParserGetVariableValueSwitch'][] = function ( $parser, &$varCache, $index, &$ret ) {
	if ( $index === 'MAG_UILANGCODEx' ) {
		$ret = $varCache[$index] = $parser->getOptions()->getUserLangObj()->getCode();
	}
};
