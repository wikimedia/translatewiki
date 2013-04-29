<?php

require_once( __DIR__ . '/FallbackSettings.php' );

$GROUPS = __DIR__ . '/groups';

$wgSpecialPages['Magic'] = 'SpecialMagic';
$wgTranslateNewsletterPreference = true;
$wgTranslateYamlLibrary = 'syck-pecl';
$wgTranslateCacheDirectory = "/www/translatewiki.net/w/cache";
$wgTranslateEC = array();
$wgTranslateFuzzyBotName = 'FuzzyBot';
$wgTranslateDocumentationLanguageCode = 'qqq';
$wgTranslatePHPlot = '/home/betawiki/software/phplot/phplot.php';
$wgTranslateGroupRoot = '/resources/projects';
$wgEnablePageTranslation = true;
$wgTranslateMessageIndex = array( 'CDBMessageIndex' );
$wgTranslateDelayedMessageIndexRebuild = true;
$wgTranslateDisablePreSaveTransform = true;
$wgTranslateCheckBlacklist = "$GROUPS/check-blacklist.php";

$wgTranslatePermissionUrl = 'Special:FirstSteps';
$wgTranslateSupportUrl = array(
	'page' => 'Support',
	'params' => array(
		'lqt_method' => 'talkpage_new_thread',
		'lqt_subject_field' => 'About [[%MESSAGE%]]',
	)
);

$wgTranslateStaticTags = array(
	"tp:mark" => 3,
	"tp:tag" => 4,
	"tp:transver" => 5
);

# Make this appear last in the list
unset( $wgTranslateTranslationServices['TTMServer'] );
$wgTranslateTranslationServices['TTMServer'] = array(
	'database' => false, // Passed to wfGetDB
	'cutoff' => 0.75,
	'type' => 'ttmserver',
	'public' => true,
);

require( "$IP/extensions/Solarium/Solarium.php" );
$wgTranslateTranslationServices['TTMServer'] = array(
	'type' => 'ttmserver',
	'class' => 'SolrTTMServer',
	'cutoff' => 0.75,
	'public' => true,
	'config' => array(
		'adapteroptions' => array(
			'host' => 'localhost',
			'timeout' => 10,
			'core' => 'production',
		),
		'adapter' => 'Solarium_Client_Adapter_Curl'
	),
);

$wgHooks['Translate:GettextFFS:headerFields'][] = 'efHT';
function efHT( $specs, $group, $code ) {
	global $wgSitename, $wgCanonicalServer;
	$specs['Project-Id-Version'] = $group->getLabel();
	$specs['Report-Msgid-Bugs-To'] = $wgSitename;
	$server = $wgCanonicalServer;
	$specs['X-Translation-Project'] = "$wgSitename <$server>";
	return true;
}

$wgTranslateExtensionDirectory = '/resources/projects/mediawiki-extensions/extensions';

$wgTranslateCC['wiki-betawiki'] = 'customMessageGroups';
function customMessageGroups( $id ) {
	$mg = new WikiMessageGroup( 'wiki-betawiki', 'betawiki-messages' );
	$mg->setLabel( 'Translatewiki.net' );
	$mg->setDescription( '{{int:bw-desc-translatewiki-messages}}' );
	return $mg;
}

$wgTranslateCC['wiki-twn-mainpage'] = 'customMessageGroupTwnMainpage';
function customMessageGroupTwnMainpage( $id ) {
	$mg = new WikiMessageGroup( 'wiki-twn-mainpage', 'twn-mainpage' );
	$mg->setLabel( 'Translatewiki.net main page' );
	$mg->setDescription( '{{int:twn-mainpage-desc}}' );
	return $mg;
}

$wgHooks['TranslatePostInitGroups'][] = array( 'setupMediaWiki' );
function setupMediaWiki( &$cc ) {
	global $wgTranslateGroupRoot, $GROUPS;

	$id = "core";
	$mg = CoreMessageGroup::factory( "MediaWiki (latest)", $id );
	$releaseDir = 'master';
	$mg->setPrefix( "$wgTranslateGroupRoot/mediawiki/$releaseDir/languages/messages" );
	$mg->setMetaDataPrefix( "$wgTranslateGroupRoot/mediawiki/$releaseDir/maintenance/language/" );
	$mg->setIcon( 'wiki://Mediawiki-logo.png' );
	$cc[$id] = $mg;

	$id = "core-0-mostused";
	$mg = new CoreMostUsedMessageGroup;
	$releaseDir = 'master';
	$mg->setPrefix( "$wgTranslateGroupRoot/mediawiki/$releaseDir/languages/messages" );
	$mg->setMetaDataPrefix( "$wgTranslateGroupRoot/mediawiki/$releaseDir/maintenance/language/" );
	$mg->setIcon( 'wiki://Mediawiki-logo.png' );
	$mg->setListFile( "$GROUPS/MediaWiki/wikimedia-mostused-2011.txt" );
	$cc[$id] = $mg;

	$changed = array(
		'1.21' => array(
			'passwordreset-emailerror-capture',
		), // Checked up to bda51d3c6e7235d810f7a9d8f47f330ae91a3260 / 2013-04-29
		'1.20' => array(
			'cannotundelete', 'logouttext', 'enotif_body', 'createaccountmail',
			'username', 'uid', 'prefs-memberingroups', 'linksearch-text',
			'contributions', 'passwordreset-emailerror-capture',
			'logentry-delete-delete', 'logentry-delete-event', 'logentry-delete-event-legacy', 'logentry-delete-restore',
			'logentry-delete-revision', 'logentry-delete-revision-legacy', 'logentry-move-move', 'logentry-move-move-noredirect',
			'logentry-move-move_redir', 'logentry-move-move_redir-noredirect', 'logentry-newusers-autocreate', 'logentry-newusers-create',
			'logentry-newusers-create2', 'logentry-newusers-newusers', 'logentry-patrol-patrol', 'logentry-patrol-patrol-auto',
			'logentry-suppress-delete', 'logentry-suppress-event', 'logentry-suppress-event-legacy', 'logentry-suppress-revision',
			'logentry-suppress-revision-legacy',
		), // Checked up to bda51d3c6e7235d810f7a9d8f47f330ae91a3260 / 2013-04-29
		'1.19' => array(
			'version-poweredby-others',
			'recentchangestext', 'feedback-bugornote', 'noarticletext-nopermission', 'pageinfo-edits',
			// From 1.20.
			'cannotundelete', 'logouttext', 'enotif_body', 'createaccountmail',
			'username', 'uid', 'prefs-memberingroups', 'linksearch-text',
			'contributions', 'passwordreset-emailerror-capture',
			'logentry-delete-delete', 'logentry-delete-event', 'logentry-delete-event-legacy', 'logentry-delete-restore',
			'logentry-delete-revision', 'logentry-delete-revision-legacy', 'logentry-move-move', 'logentry-move-move-noredirect',
			'logentry-move-move_redir', 'logentry-move-move_redir-noredirect', 'logentry-newusers-autocreate', 'logentry-newusers-create',
			'logentry-newusers-create2', 'logentry-newusers-newusers', 'logentry-patrol-patrol', 'logentry-patrol-patrol-auto',
			'logentry-suppress-delete', 'logentry-suppress-event', 'logentry-suppress-event-legacy', 'logentry-suppress-revision',
			'logentry-suppress-revision-legacy',
		), // Checked up to bda51d3c6e7235d810f7a9d8f47f330ae91a3260 / 2013-04-29
	);

	$branches = array_keys( $changed );

	foreach ( $branches as $branch ) {
		$id = "core-$branch";
		$mangle = new StringMatcher( "mw-core-$branch-", $changed[$branch] );
		$mg = CoreMessageGroup::factory( "MediaWiki $branch", $id );
		$mg->setDescription( "{{int:translate-group-desc-mediawiki-core-branch}}" );
		$mg->setMangler( $mangle );
		$releaseDir = 'REL' . str_replace( '.', '_', $branch );
		$mg->setPrefix( "$wgTranslateGroupRoot/mediawiki/$releaseDir/languages/messages" );
		$mg->setMetaDataPrefix( "$wgTranslateGroupRoot/mediawiki/$releaseDir/maintenance/language/" );
		$mg->setIcon( 'wiki://Mediawiki-logo.png' );
		$mg->setMeta( true );
		$mg->parentId = 'core';
		$cc[$id] = $mg;
	}

	return true;
}

# Add aggregate message groups for MediaWiki extensions.
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/MediaWiki.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/CollectionAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/FlaggedRevsAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/ReaderFeedbackAgg.yaml";
# Disabled by Siebrand / 2011-10-23. Maintainer is not willing to stick to conventions.
#$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/SocialProfileAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/TranslateAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/WikimediaMainAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/WikimediaAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/ExtensionsAgg.yaml";

$wgTranslateGroupFiles[] = "$GROUPS/PageTranslationAgg.yaml";

$wgHooks['TranslatePostInitGroups'][] = array( 'setupMediaWikiExtensions' );
function setupMediaWikiExtensions( &$list, &$deps, &$autoload ) {
	global $GROUPS;

	$def = "$GROUPS/MediaWiki/mediawiki-defines.txt";
	$path = '%GROUPROOT%/mediawiki-extensions/extensions/';

	$foo = new PremadeMediawikiExtensionGroups( $def, $path );
	$foo->register( $list, $deps, $autoload );

	return true;
}

$wgHooks['TranslatePostInitGroups'][] = array( 'setupWikia' );
function setupWikia( &$list, &$deps, &$autoload ) {
	global $GROUPS;

	$def = "$GROUPS/Wikia/extensions.txt";
	$path = '%GROUPROOT%/wikia/extensions/wikia/';

	$foo = new PremadeMediawikiExtensionGroups( $def, $path );
	$foo->setNamespace( NS_WIKIA );
	$foo->setGroupPrefix( 'wikia-' );
	//$mg->setIcon( 'wiki://Wikia_Logo.png' );
	$foo->setUseConfigure( false );
	$foo->register( $list, $deps, $autoload );

	return true;
}

$wgHooks['TranslatePostInitGroups'][] = array( 'setupToolserver' );
function setupToolserver( &$list, &$deps, &$autoload ) {
	global $GROUPS;

	$def = "$GROUPS/Toolserver/toolserver-textdomains.txt";
	$path = '%GROUPROOT%/toolserver/language/messages/';

	$foo = new PremadeToolserverTextdomains( $def, $path );
	$foo->register( $list, $deps, $autoload );

	return true;
}

$wgTranslateAuthorBlacklist[] = array( 'black', '/^.*;.*;(Andre Engels|Gangleri|Jon Harald Søby|IAlex|M.M.S.|BotMultichill|Nike|Piivaat|Raymond|RobertL|SieBot|Siebrand|SPQRobin|Suradnik13|Verdy p)$/Ui' );
$wgTranslateAuthorBlacklist[] = array( 'black', '/^.*;da;(Wegge|Morten)$/Ui' ); # are both credited under other names
$wgTranslateAuthorBlacklist[] = array( 'black', '/^out-mantis.*;nl;Siebrand$/Ui' ); # credited under other name

$wgTranslateAuthorBlacklist[] = array( 'white', '/^.*;(qqq|fr);IAlex$/Ui' );
$wgTranslateAuthorBlacklist[] = array( 'white', '/^.*;(qqq|sma|sv);M.M.S.$/Ui' );
$wgTranslateAuthorBlacklist[] = array( 'white', '/^.*;(qqq|fi);Nike$/Ui' );
$wgTranslateAuthorBlacklist[] = array( 'white', '/^.*;.*;Paucabot$/Ui' );
$wgTranslateAuthorBlacklist[] = array( 'white', '/^.*;qqq;Raymond$/Ui' );
$wgTranslateAuthorBlacklist[] = array( 'white', '/^out-osm.*;(qqq|de);Raymond$/Ui' );
$wgTranslateAuthorBlacklist[] = array( 'white', '/^.*;qqq;RobertL$/Ui' );
$wgTranslateAuthorBlacklist[] = array( 'white', '/^.*;(qqq|nl|nl-informal);Siebrand$/Ui' );
$wgTranslateAuthorBlacklist[] = array( 'white', '/^.*;(qqq|nl|nl-informal|af|la|grc);SPQRobin$/Ui' );
$wgTranslateAuthorBlacklist[] = array( 'white', '/^.*;(qqq|no|nn|da|sv|en-gb);Jon Harald Søby$/Ui' );
$wgTranslateAuthorBlacklist[] = array( 'white', '/^.*;(qqq|fr);Verdy p$/Ui' );

$wgTranslateBlacklist = array(
'*' => array(
	'en' => 'English is the source language. Suggest improvements at [[Support]].',
	'ady' => 'This language code should remain unused. Localise in ady-cyrl please.',
	'als' => 'This language code should remain unused. Localise in gsw please.',
	'bat-smg' => 'This code is for compatibility purposes only. Localise in \'sgs\'',
	'bbc' => 'This language code should remain unused. Localise in bbc-latn please.',
	'bh' => 'This code is for compatibility purposes only. Localise in \'bho\'',
	'be-x-old' => 'This code is for compatibility purposes only. Localise in \'be-tarask\'',
	'crh' => 'This language code should remain unused. Localise in crh-latn or crh-cyrl please.',
	'dk' => 'This language code should remain unused. Localise in da please.',
	'fiu-vro' => 'This language code should remain unused. Localise in vro please.',
	'gan' => 'This language code should remain unused. Localise in gan-hant or gan-hans please.',
	'gom' => 'This language code should remain unused. Localise in gom-deva please.',
	#'got' => 'This is an [http://www.sil.org/iso639-3/documentation.asp?id=got ancient language] without enough information to create a localisation. It cannot be localised in translatewiki.net.',
	'hif' => 'This language code should remain unused. Localise in hif-latn please.',
	'ike' => 'This language code should remain unused. Localise in ike-cans or ike-latn please.',
	'iu' => 'This language code should remain unused. Localise in ike-cans or ike-latn please.',
	'kbd' => 'This language code should remain unused. Localise in kbd-cyrl please.',
	'kk' => 'This language code should remain unused. Localise in kk-cyrl please.',
	'kk-cn' => 'This language code should remain unused. Localise in kk-arab please.',
	'kk-kz' => 'This language code should remain unused. Localise in kk-cyrl please.',
	'kk-tr' => 'This language code should remain unused. Localise in kk-latn please.',
	'ks' => 'This language code should remain unused. Localise in ks-arab (Arabic script) or ks-deva (Devanagari script) please.',
	'ku' => 'This code is for compatibility purposes only. Localise in \'ku-latn\'',
	'no' => 'This language code should remain unused. Use \'nb\'',
	'oge' => 'This is a [http://www.sil.org/iso639-3/documentation.asp?id=oge historical language]. It cannot be localised in translatewiki.net.',
	'roa-rup' => 'This language code should remain unused. Localise in rup nplease.',
	'ruq' => 'This language code should remain unused. Localise in ruq-latn please.',
	'simple' => 'This language code should remain unused.',
	'sr' => 'This language code should remain unused. Localise in sr-ec please.',
	'tg' => 'This language code should remain unused. Localise in tg-cyrl please.',
	'tp' => 'This language cannot be localised in translatewiki.net. It is not a valid language code.',
	'tokipona' => 'This language cannot be localised in translatewiki.net. It is not a valid language code.',
	'tt' => 'This language code should remain unused. Localise in tt-cyrl please.',
	'ug' => 'This language code should remain unused. Localise in ug-arab please.',
	'zh' => 'This language code should remain unused. Localise in zh-hans or zh-hant please.',
	'zh-classical' => 'This language code should remain unused. Localise in lzh please.',
	'zh-cn' => 'This language code should remain unused. Localise in zh-hans please.',
	'zh-tw' => 'This language code should remain unused. Localise in zh-hant please.',
	'zh-min-nan' => 'This language code should remain unused. Localise in nan please.',
	'zh-mo' => 'This language code should remain unused. Localise in zh-hk please.',
	'zh-my' => 'This language code should remain unused. Localise in zh-sg please.',
	'zh-sg' => 'This language code should remain unused. Localise in zh-hans please.',
	'zh-yue' => 'This language code should remain unused. Localise in yue please.',
),
'core' => array(
	'es-419' => 'This code is only supported for Shapado. Use \'es\'.',
	'nl-be' => 'This code is not used in MediaWiki. Use \'nl\' or \'vls\'.',
),
'ext' => array(
	'es-419' => 'This code is only supported for Shapado. Use \'es\'.',
	'nl-be' => 'This code is not used in MediaWiki. Use \'nl\' or \'vls\'.',
),
'out' => array(
	'es-419' => 'This code is not available for this software.',
	'roa-tara' => 'This code is not available for this software.',
),
'wikia' => array(
	'es-419' => 'This code is not available for this software.',
	'nl-be' => 'This code is not used in MediaWiki. Use \'nl\' or \'vls\'.',
),
);

# Namespace 8
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/jquery.i18n.yaml";

# No longer in use.
wfAddNamespace( 1200, 'Voctrain' );

wfAddNamespace( 1202, 'FreeCol' );
$wgTranslateGroupFiles[] = "$GROUPS/FreeCol/FreeCol.yaml";

# No longer in use.
wfAddNamespace( 1204, 'Nocc' );

wfAddNamespace( 1206, 'Wikimedia' );
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/jquery.uls.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikiBlame.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikimediaMobile.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikimediaMobile-android.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikimediaMobile-ios.yaml";

# No longer in use.
wfAddNamespace( 1208, 'StatusNet' );

wfAddNamespace( 1210, 'Mantis' );
$wgTranslateGroupFiles[] = "$GROUPS/MantisBT/MantisBT.yaml";

wfAddNamespace( 1212, 'Mwlib' );
$wgTranslateGroupFiles[] = "$GROUPS/Mwlib/Mwlibrl.yaml";

# No longer in use.
wfAddNamespace( 1214, 'Commonist' );

# No longer in use.
wfAddNamespace( 1216, 'OpenLayers' );

wfAddNamespace( 1218, 'FUDforum' );
$wgTranslateGroupFiles[] = "$GROUPS/FUDforum/FUDforum.yaml";

wfAddNamespace( 1220, 'Okawix' );
$wgTranslateGroupFiles[] = "$GROUPS/Okawix/Okawix.yaml";

wfAddNamespace( 1222, 'Osm' );
$wgTranslateGroupFiles[] = "$GROUPS/OpenStreetMap/OpenStreetMap.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/OpenStreetMap/Potlatch2.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/OpenStreetMap/WaymarkedTrails.yaml";

wfAddNamespace( 1224, 'WikiReader' );
$wgTranslateGroupFiles[] = "$GROUPS/WikiReader/WikiReader.yaml";

wfAddNamespace( 1226, 'Shapado' );
$wgTranslateGroupFiles[] = "$GROUPS/Shapado/Shapado.yaml";

wfAddNamespace( 1228, 'iHRIS' );
$wgTranslateGroupFiles[] = "$GROUPS/IHRIS/IHRISCommon.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/IHRIS/IHRISI2ce.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/IHRIS/IHRISManage.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/IHRIS/IHRISQualify.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/IHRIS/IHRIS.yaml";

wfAddNamespace( 1230, 'Mifos' );
$wgTranslateGroupFiles[] = "$GROUPS/Mifos/Mifos.yaml";

wfAddNamespace( 1232, 'Wikia' );
$wgTranslateGroupFiles[] = "$GROUPS/Wikia/WikiaAgg.yaml";

wfAddNamespace( 1234, 'OpenImages' );
$wgTranslateGroupFiles[] = "$GROUPS/OpenImages/OpenImages.yaml";

# No longer in use.
wfAddNamespace( 1236, 'Europeana' );

wfAddNamespace( 1238, 'Pywikipedia' );
$wgTranslateGroupFiles[] = "$GROUPS/Pywikipedia/Pywikipedia.yaml";

wfAddNamespace( 1240, 'Toolserver' );
$wgTranslateGroupFiles[] = "$GROUPS/Toolserver/ToolserverAgg.yaml";

wfAddNamespace( 1242, 'EOL' );
$wgTranslateGroupFiles[] = "$GROUPS/EOL/EOL.yaml";

wfAddNamespace( 1244, 'Kiwix' );
$wgTranslateGroupFiles[] = "$GROUPS/Kiwix/Kiwix.yaml";

wfAddNamespace( 1246, 'Mozilla' );
$wgTranslateGroupFiles[] = "$GROUPS/Mozilla/MozillaJava.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Mozilla/MozillaDtd.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Mozilla/Mozilla.yaml";

# Not yet in use.
wfAddNamespace( 1248, 'FrontlineSMS' );
#$wgTranslateGroupFiles[] = "$GROUPS/FrontlineSMS/FrontlineSMS.yaml";

wfAddNamespace( 1250, 'EtherpadLite' );
$wgTranslateGroupFiles[] = "$GROUPS/EtherpadLite/EtherpadLite.yaml";

# Not yet in use.
wfAddNamespace( 1252, 'Vicuna' );
$wgTranslateGroupFiles[] = "$GROUPS/Vicuna/Vicuna.yaml";

wfAddNamespace( 1254, 'FUEL' );
$wgTranslateGroupFiles[] = "$GROUPS/FUEL/FUEL.yaml";

wfAddNamespace( 1256, 'Blockly' );
$wgTranslateGroupFiles[] = "$GROUPS/Blockly/Blockly.yaml";
