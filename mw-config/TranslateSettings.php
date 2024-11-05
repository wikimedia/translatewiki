<?php

require_once __DIR__ . '/FallbackSettings.php';
require_once __DIR__ . '/LanguageSettings.php';

$GROUPS = '/home/betawiki/config/groups';

// 2015-05-18 Really broken now. Disabled.
// $wgSpecialPages['Magic'] = 'SpecialMagic';
$wgTranslateNewsletterPreference = true;

// Enable message bundle integration. See: T286547
$wgTranslateEnableMessageBundleIntegration = true;

// Enable message group subscriptions. See: T348501
$wgTranslateEnableMessageGroupSubscription = true;

// Enable message bundle Lua library. See: T369049
$wgTranslateEnableLuaIntegration = true;

// Display page translation setting banner for certain namespaces. See: T360409
// For testing. Eventually, enable for namespaces where translatable content is more likely
$wgTranslatePageTranslationBannerNamespaces = [ NS_USER ];

$wgTranslateCacheDirectory = "/resources/caches/translatewiki.net";
$wgTranslateValidationExclusionFile = __DIR__ . "/validation-exclusion-list.php";

$talkParams = [
	'action' => 'edit',
	'section' => 'new',
	'preloadtitle' => 'About [[%MESSAGE%]]',
];
$phabParams = [
	'title' => '[[%MESSAGE%]] translation issue',
	'description' => "\n\n----\n\n**Message URL**: %MESSAGE_URL%",
];
$githubParams = [
	'title' => '[[%MESSAGE%]] i18n issue',
	'body' => "\nMessage URL: %MESSAGE_URL%",
];
$gitlabParams = [
	'issue[title]' => '[[%MESSAGE%]] i18n issue',
	'issue[description]' => "\nMessage URL: %MESSAGE_URL%",
];

$phabUrl = 'https://phabricator.wikimedia.org/maniphest/task/edit/form/1/';
$wgTranslateSupportUrl = [
	'page' => 'Support',
	'params' => $talkParams,
];

$wgHooks['Translate:GettextFormat:headerFields'][] = static function ( &$specs, $group, $code ) {
	global $wgSitename, $wgCanonicalServer;
	$specs['Project-Id-Version'] = $group->getLabel();
	$specs['Report-Msgid-Bugs-To'] = $wgSitename;
	$server = $wgCanonicalServer;
	$specs['X-Translation-Project'] = "$wgSitename <$server>";
	return true;
};

$wgTranslateExtensionDirectory = '/resources/projects/mediawiki-extensions/extensions';

$wgHooks['TranslatePostInitGroups'][] = static function ( &$list, &$deps, &$autoload ) {
	$mg = new WikiMessageGroup( 'wiki-betawiki', 'betawiki-messages' );
	$mg->setLabel( 'Translatewiki.net' );
	$mg->setDescription( '{{int:bw-desc-translatewiki-messages}}' );
	$list[ 'wiki-betawiki' ] = $mg;
};

$wgHooks['TranslatePostInitGroups'][] = static function ( &$list, &$deps, &$autoload ) {
	$mg = new WikiMessageGroup( 'wiki-gadget-term', 'gadget-term-messages' );
	$mg->setLabel( 'Terminology gadget' );
	$mg->setDescription( '{{int:bw-desc-gadget-term}}' );
	$list[ 'wiki-gadget-term' ] = $mg;
};

# Add aggregate message groups for MediaWiki extensions.
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/mwbitbucket.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/mwgerrit.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/mwgithub.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/mwgitlab.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/MediaWiki.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/MirahezeAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/WikimediaMainAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/WikimediaAdvancedAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/WikimediaFundraisingAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/WikimediaLegacyAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/WikimediaMediaAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/WikimediaTechnicalAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/WikimediaUpcomingAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/WikimediaWikivoyageAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/WikimediaAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/ExtensionsAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/SocialProfileAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/SocialToolsAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MediaWiki/SkinsAgg.yaml";
$wgTranslateGroupAliases['ext-cirrussearch-0-all'] = 'ext-cirrussearch';
$wgTranslateGroupAliases['ext-contenttranslation-0-all'] = 'ext-contenttranslation';
$wgTranslateGroupAliases['ext-flaggedrevs-0-all'] = 'ext-flaggedrevs';
$wgTranslateGroupAliases['ext-flow-0-all'] = 'ext-flow';
$wgTranslateGroupAliases['ext-installer'] = 'mediawiki-installer';
$wgTranslateGroupAliases['ext-semantic-0-all'] = 'ext-semantic';
$wgTranslateGroupAliases['ext-semanticdrilldown'] = 'mwgithub-semanticdrilldown';
$wgTranslateGroupAliases['ext-semantictasks'] = 'mwgithub-semantictasks';
$wgTranslateGroupAliases['ext-translate-0-all'] = 'ext-translate';
$wgTranslateGroupAliases['ext-universallanguageselector-0-all'] = 'ext-universallanguageselector';
$wgTranslateGroupAliases['ext-visualeditor-0-all'] = 'ext-visualeditor';
$wgTranslateGroupAliases['ext-0-wikimedia-main'] = 'wikimedia-main';

$wgTranslateGroupFiles[] = "$GROUPS/PageTranslationAgg.yaml";

$wgHooks['TranslatePostInitGroups'][] = static function ( &$list, &$deps, &$autoload ) use ( $GROUPS ) {
	# TODO: rename when possible
	$id = 'core-0-mostused';
	$subsetKeysFile = "$GROUPS/MediaWiki/wikimedia-mostused-2015.txt";
	$subsetKeys = explode( "\n", trim( file_get_contents( $subsetKeysFile ) ) );

	$list[$id] = new SubsetMessageGroup(
		$id,
		'MediaWiki (most important messages)',
		'mediawiki',
		$subsetKeys
	);
	$deps[] = new FileDependency( realpath( $subsetKeysFile ) );
};

$wgHooks['TranslatePostInitGroups'][] = static function ( &$list, &$deps, &$autoload ) use ( $GROUPS ) {
	$def = "$GROUPS/MediaWiki/mediawiki-extensions.txt";
	$path = '%GROUPROOT%/mediawiki-extensions/';

	$foo = new PremadeMediaWikiExtensionGroups( $def, $path );
	$foo->register( $list, $deps, $autoload );
};

$wgHooks['TranslatePostInitGroups'][] = static function ( &$list, &$deps, &$autoload ) use ( $GROUPS ) {
	$def = "$GROUPS/MediaWiki/mediawiki-skins.txt";
	$path = '%GROUPROOT%/mediawiki-skins/';

	$foo = new PremadeMediaWikiExtensionGroups( $def, $path );
	$foo->setGroupPrefix( 'mediawiki-skin-' );
	$foo->register( $list, $deps, $autoload );
};

$wgHooks['TranslatePostInitGroups'][] = static function ( &$list, &$deps, &$autoload ) use ( $GROUPS ) {
	$def = "$GROUPS/Intuition/intuition-textdomains.txt";
	$path = '%GROUPROOT%/intuition/language/messages/';

	$foo = new PremadeIntuitionTextdomains( $def, $path );
	$foo->register( $list, $deps, $autoload );
};

$wgTranslateAuthorExclusionList = [];
$wgTranslateAuthorExclusionList[] = [ 'exclude', '/^.*;.*;.*Bot$/Ui' ];
$wgTranslateAuthorExclusionList[] = [ 'exclude', '/^.*;.*;(Abijeet Patro|Amire80|Andre Engels|Gangleri|Jon Harald Søby|IAlex|M.M.S.|BotMultichill|Nike|Piivaat|Raymond|RobertL|SieBot|Siebrand|SPQRobin|Suradnik13|Verdy p)$/Ui' ];
$wgTranslateAuthorExclusionList[] = [ 'exclude', '/^.*;da;(Wegge|Morten)$/Ui' ]; # are both credited under other names
$wgTranslateAuthorExclusionList[] = [ 'exclude', '/^out-mantis.*;nl;Siebrand$/Ui' ]; # credited under other name

$wgTranslateAuthorExclusionList[] = [ 'include', '/^.*;(qqq|en-gb|hi);Abijeet Patro$/Ui' ];
$wgTranslateAuthorExclusionList[] = [ 'include', '/^.*;(qqq|ca|he|ht|ru);Amire80$/Ui' ];
$wgTranslateAuthorExclusionList[] = [ 'include', '/^.*;(qqq|fr);IAlex$/Ui' ];
$wgTranslateAuthorExclusionList[] = [ 'include', '/^.*;(qqq|sma|sv);M.M.S.$/Ui' ];
$wgTranslateAuthorExclusionList[] = [ 'include', '/^.*;(qqq|fi);Nike$/Ui' ];
$wgTranslateAuthorExclusionList[] = [ 'include', '/^.*;.*;Paucabot$/Ui' ];
$wgTranslateAuthorExclusionList[] = [ 'include', '/^.*;qqq;Raymond$/Ui' ];
$wgTranslateAuthorExclusionList[] = [ 'include', '/^out-osm.*;(qqq|de);Raymond$/Ui' ];
$wgTranslateAuthorExclusionList[] = [ 'include', '/^.*;qqq;RobertL$/Ui' ];
$wgTranslateAuthorExclusionList[] = [ 'include', '/^.*;(qqq|nl|nl-informal);Siebrand$/Ui' ];
$wgTranslateAuthorExclusionList[] = [ 'include', '/^.*;(qqq|nl|nl-informal|af|la|grc);SPQRobin$/Ui' ];
$wgTranslateAuthorExclusionList[] = [ 'include', '/^.*;(qqq|nb|no|nn|da|se|sv|sw|en-gb);Jon Harald Søby$/Ui' ];
$wgTranslateAuthorExclusionList[] = [ 'include', '/^.*;(qqq|fr);Verdy p$/Ui' ];

$wgTranslateDisabledTargetLanguages = [
	'*' => [
		'ady' => 'This language code should remain unused. Localise in ady-cyrl please.',
		'aeb' => 'This language code should remain unused. Localise in aeb-arab please.',
		'ais' => 'This language code sholud remain unused. Localise in szy please.',
		'ak' => 'This language code should remain unused. Localise in fat or tw please.',
		'als' => 'This language code should remain unused. Localise in gsw please.',
		'bat-smg' => 'This code is for compatibility purposes only. Localise in \'sgs\'',
		'bbc' => 'This language code should remain unused. Localise in bbc-latn please.',
		'bh' => 'This code is for compatibility purposes only. Localise in \'bho\'',
		'be-x-old' => 'This code is for compatibility purposes only. Localise in \'be-tarask\'',
		'cdo-hani' => 'This language code should remain unused. Localise in cdo-hant or cdo-hans please.',
		'cjy' => 'This language code should remain unused. Localise in cjy-hant please.',
		'cpx' => 'This language code should remain unused. Localise in cpx-hans, cpx-hant or cpx-latn please.',
		'crh' => 'This language code should remain unused. Localise in crh-latn or crh-cyrl please.',
		'dk' => 'This language code should remain unused. Localise in da please.',
		'es-419' => 'This language code should remain unused.',
		'fiu-vro' => 'This language code should remain unused. Localise in vro please.',
		'gan' => 'This language code should remain unused. Localise in gan-hant or gan-hans please.',
		'gom' => 'This language code should remain unused. Localise in gom-deva please.',
		# 'got' => 'This is an [http://www.sil.org/iso639-3/documentation.asp?id=got ancient language] without enough information to create a localisation. It cannot be localised in translatewiki.net.',
		'hak' => 'This language code should remain unused. Localise in hak-hans, hak-hant or hak-latn please.',
		'hif' => 'This language code should remain unused. Localise in hif-latn please.',
		'ike' => 'This language code should remain unused. Localise in ike-cans or ike-latn please.',
		'isv' => 'This language code should remain unused. Localise in isv-cyrl or isv-latn please.',
		'iu' => 'This language code should remain unused. Localise in ike-cans or ike-latn please.',
		'kbd' => 'This language code should remain unused. Localise in kbd-cyrl please.',
		'kk' => 'This language code should remain unused. Localise in kk-cyrl please.',
		'kk-cn' => 'This language code should remain unused. Localise in kk-arab please.',
		'kk-kz' => 'This language code should remain unused. Localise in kk-cyrl please.',
		'kk-tr' => 'This language code should remain unused. Localise in kk-latn please.',
		'kr' => 'This language code should remain unused. Localise in knc please.',
		'ks' => 'This language code should remain unused. Localise in ks-arab (Arabic script) or ks-deva (Devanagari script) please.',
		'ku' => 'This code is for compatibility purposes only. Localise in \'ku-latn\'',
		'mnc-latn' => 'This language code should remain unused. Localise in mnc (Latin script) or mnc-mong (Mongolian script) please.',
		'nan-hani' => 'This language code should remain unused. Localise in nan-hans or nan-hant please.',
		'nan-latn' => 'This language code should remain unused. Localise in nan-latn-pehoeji or nan-latn-tailo please.',
		'no' => 'This language code should remain unused. Use \'nb\'',
		'oge' => 'This is a [http://www.sil.org/iso639-3/documentation.asp?id=oge historical language]. It cannot be localised in translatewiki.net.',
		'roa-rup' => 'This language code should remain unused. Localise in rup nplease.',
		'ruq' => 'This language code should remain unused. Localise in ruq-latn please.',
		'se-fi' => 'This language code should remain unused. Localise in se please.',
		'se-no' => 'This language code should remain unused. Localise in se please.',
		'se-se' => 'This language code should remain unused. Localise in se please.',
		'simple' => 'This language code should remain unused.',
		'skr' => 'This language code should remain unused. Localise in skr-arab please.',
		'sr' => 'This language code should remain unused. Localise in sr-ec please.',
		'tg' => 'This language code should remain unused. Localise in tg-cyrl please.',
		'tt' => 'This language code should remain unused. Localise in tt-cyrl please.',
		'ug' => 'This language code should remain unused. Localise in ug-arab please.',
		'wuu' => 'This language code should remain unused. Localise in wuu-hans or wuu-hant please.',
		'yue' => 'This language code should remain unused. Localise in yue-hans or yue-hant please.',
		'zh' => 'This language code should remain unused. Localise in zh-hans or zh-hant please.',
		'zh-classical' => 'This language code should remain unused. Localise in lzh please.',
		'zh-cn' => 'This language code should remain unused. Localise in zh-hans please.',
		'zh-tw' => 'This language code should remain unused. Localise in zh-hant please.',
		'zh-min-nan' => 'This language code should remain unused. Localise in nan please.',
		'zh-mo' => 'This language code should remain unused. Localise in zh-hk please.',
		'zh-my' => 'This language code should remain unused. Localise in zh-sg please.',
		'zh-sg' => 'This language code should remain unused. Localise in zh-hans please.',
		'zh-yue' => 'This language code should remain unused. Localise in yue please.',
	],
	'core' => [
		'es-mx' => 'This code is not used in MediaWiki. Use \'es\'.',
	],
	'ext' => [
		'es-mx' => 'This code is not used in MediaWiki. Use \'es\'.',
	],
	'out' => [
		'roa-tara' => 'This code is not available for this software.',
	],
];

$wgTranslateGroupSynchronizationCache = true;

# Namespace 8
$wgTranslateMessageNamespaces[] = NS_MEDIAWIKI;
$wgMessagesDirs['MediaWikiInstaller'] = "$IP/includes/installer/i18n";
$wgTranslateSupportUrlNamespace[NS_MEDIAWIKI] = [
	'url' => "$phabUrl?projects=i18n",
	'params' => $phabParams,
];

# No longer in use.
wfAddNamespace( 1200, 'Voctrain' );

wfAddNamespace( 1202, 'FreeCol' );
$wgTranslateGroupFiles[] = "$GROUPS/FreeCol/FreeCol.yaml";
$wgTranslateSupportUrlNamespace[NS_FREECOL] = [
	'page' => 'Translating_talk:FreeCol',
	'params' => $talkParams,
];

wfAddNamespace( 1204, 'Nocc' );
$wgTranslateGroupFiles[] = "$GROUPS/Nocc/Nocc.yaml";

wfAddNamespace( 1206, 'Wikimedia' );
$wgTranslateGroupFiles[] = "$GROUPS/wikidata/wikidata.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/Adiutor.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/Anvesha.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/CapacityExchange.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/CeJS.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/Cita.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/CitationHunt.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/CommonsAndroid.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/ConvenientDiscussions.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/CopyPatrol.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/DiscordWikiBot.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/global-search.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/GrantMetrics.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/jquery.uls.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/ia-upload.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/InteractionTimeline.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/InternetArchiveBot.yaml";
$wgTranslateGroupAliases['out-jquery-uls'] = 'jquery-uls';
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/MiniEdit.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/PageContentService.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/Pageviews.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/ProveIt.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/QRmedia.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/SignIt.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/svgtranslate.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/SWViewer.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/Toolhub.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/Twinkle.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/video2commons.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WhoWroteThat.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/wiki-ai.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikiBlame.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WmczTracker.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WmczWeb.yaml";
$wgTranslateGroupAliases['out-wikiblame'] = 'wikiblame';
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikidataImagePositions.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikidataLexemeForms.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikidataMismatchFinder.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikiEduDashboard.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikimediaDeveloperPortal.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikimediaMailmanTemplates.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikimediaMobile-android.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikimediaMobile-ios.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikimediaMobile-kaios.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikimediaMobile.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikimediaOCR.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikimediaPortals.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikimediaTools.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikiLearn.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikipediaLibrary.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikipediaPreview.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WikiScore.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/Wikistats.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WPCleaner.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/Wscd.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/wscontest.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/WSExport.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Wikimedia/XTools.yaml";
$wgTranslateGroupAliases['out-pageviews'] = 'pageviews';
$wgTranslateGroupAliases['wikinity'] = 'wikimedia-tools-wikinity';
$wgTranslateGroupAliases['int-heritage'] = 'wikimedia-tools-heritage';
$wgTranslateGroupAliases['int-guc'] = 'wikimedia-tools-guc';
$wgTranslateGroupAliases['mwgitlabskin-liberty'] = 'mwgithubskin-liberty';
$wgTranslateGroupAliases['wdlocator'] = 'wikimedia-tools-wdlocator';

$wgTranslateSupportUrlNamespace[NS_WIKIMEDIA] = [
	'url' => "$phabUrl?projects=i18n,Wikimedia-General-or-Unknown",
	'params' => $phabParams,
];

# No longer in use.
wfAddNamespace( 1208, 'StatusNet' );

wfAddNamespace( 1210, 'Mantis' );
$wgTranslateGroupFiles[] = "$GROUPS/MantisBT/MantisBT.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/MantisBT/CodevTT.yaml";
$wgTranslateSupportUrlNamespace[NS_MANTIS] = [
	'page' => 'Translating_talk:MantisBT',
	'params' => $talkParams,
];

# No longer in use.
wfAddNamespace( 1212, 'Mwlib' );

# No longer in use.
wfAddNamespace( 1214, 'Commonist' );

# No longer in use.
wfAddNamespace( 1216, 'OpenLayers' );

wfAddNamespace( 1218, 'FUDforum' );
$wgTranslateGroupFiles[] = "$GROUPS/FUDforum/FUDforum.yaml";

# No longer in use.
wfAddNamespace( 1220, 'Okawix' );

wfAddNamespace( 1222, 'Osm' );
$wgTranslateGroupFiles[] = "$GROUPS/OpenStreetMap/OpenStreetMap.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/OpenStreetMap/WaymarkedTrails.yaml";
$wgTranslateGroupAliases['out-osm-potlatch2-main'] = 'osm-potlatch2-main';
$wgTranslateGroupAliases['out-osm-potlatch2-help'] = 'osm-potlatch2-help';
$wgTranslateSupportUrlNamespace[NS_OSM] = [
	'page' => 'Translating_talk:OpenStreetMap',
	'params' => $talkParams,
];

# No longer in use.
wfAddNamespace( 1224, 'WikiReader' );

# No longer in use.
wfAddNamespace( 1226, 'Shapado' );

# No longer in use.
wfAddNamespace( 1228, 'iHRIS' );

# No longer in use.
wfAddNamespace( 1230, 'Mifos' );

# No longer in use.
wfAddNamespace( 1232, 'Wikia' );

# No longer in use.
wfAddNamespace( 1234, 'OpenImages' );

# No longer in use.
wfAddNamespace( 1236, 'Europeana' );

wfAddNamespace( 1238, 'Pywikibot' );
$wgTranslateGroupFiles[] = "$GROUPS/Pywikibot/Pywikibot.yaml";
$wgNamespaceAliases['Pywikipedia'] = 1238;
$wgNamespaceAliases['Pywikipedia_talk'] = 1238;
$wgTranslateSupportUrlNamespace[NS_PYWIKIBOT] = [
	'page' => 'Translating_talk:Pywikibot',
	'params' => $talkParams,
];

wfAddNamespace( 1240, 'Intuition' );
$wgTranslateGroupFiles[] = "$GROUPS/Intuition/IntuitionAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Intuition/dcatap.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Intuition/orphantalk.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Intuition/raun.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Intuition/refill.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Intuition/web.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Intuition/commtech-commons.yaml";
$wgNamespaceAliases['Toolserver'] = 1240;
$wgNamespaceAliases['Toolserver_talk'] = 1240;
$wgTranslateSupportUrlNamespace[NS_INTUITION] = [
	'url' => "$phabUrl?projects=i18n,Tool-Labs-tools-Other",
	'params' => $phabParams,
];

wfAddNamespace( 1242, 'EOL' );
$wgTranslateGroupFiles[] = "$GROUPS/EOL/EOL.yaml";
$wgTranslateGroupAliases['out-eol-0-all'] = 'eol';
$wgTranslateSupportUrlNamespace[NS_EOL] = [
	'page' => 'Translating_talk:Encyclopedia_of_Life',
	'params' => $talkParams,
];

wfAddNamespace( 1244, 'Kiwix' );
$wgTranslateGroupFiles[] = "$GROUPS/Kiwix/KiwixAndroid.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Kiwix/KiwixDesktop.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Kiwix/KiwixMWoffliner.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Kiwix/KiwixServe.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Kiwix/KiwixPhET.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Kiwix/KiwixApple.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Kiwix/KiwixAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Kiwix/KiwixZimitFrontend.yaml";
$wgTranslateGroupAliases['out-kiwix'] = 'kiwix';
$wgTranslateSupportUrlNamespace[NS_KIWIX] = [
	'page' => 'Translating_talk:Kiwix',
	'params' => $talkParams,
];

# No longer in use.
wfAddNamespace( 1246, 'Mozilla' );

wfAddNamespace( 1248, 'Huggle' );
$wgTranslateGroupFiles[] = "$GROUPS/Huggle/Huggle.yaml";
$wgTranslateGroupAliases['out-huggle'] = 'huggle';
$wgTranslateSupportUrlNamespace[NS_HUGGLE] = [
	'url' => "$phabUrl?projects=i18n,Huggle",
	'params' => $phabParams,
];

wfAddNamespace( 1250, 'EtherpadLite' );
$wgTranslateGroupFiles[] = "$GROUPS/EtherpadLite/EtherpadLite.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/EtherpadLite/Plugins.yaml";
$wgTranslateGroupAliases['out-etherpad-lite'] = 'etherpad-lite';

wfAddNamespace( 1252, 'Vicuna' );
$wgTranslateGroupFiles[] = "$GROUPS/Vicuna/Vicuna.yaml";
$wgTranslateSupportUrlNamespace[NS_VICUNA] = [
	'page' => 'Translating_talk:Vicuña_Uploader',
	'params' => $talkParams,
];

# No longer in use.
wfAddNamespace( 1254, 'FUEL' );

wfAddNamespace( 1256, 'Blockly' );
$wgTranslateGroupFiles[] = "$GROUPS/Blockly/Blockly.yaml";

wfAddNamespace( 1258, 'MathJax' );
$wgTranslateGroupFiles[] = "$GROUPS/MathJax/MathJax.yaml";
$wgTranslateSupportUrlNamespace[NS_MATHJAX] = [
	'page' => 'Translating_talk:MathJax',
	'params' => $talkParams,
];

wfAddNamespace( 1260, 'NFCRingControl' );
$wgTranslateGroupFiles[] = "$GROUPS/NFCRingControl/NFCRingControl.yaml";
$wgTranslateGroupAliases['out-nfcring-control'] = 'nfcring-control';

wfAddNamespace( 1262, 'iNaturalist' );

# No longer in use
wfAddNamespace( 1264, 'EntryScape' );
$wgCapitalLinkOverrides[NS_ENTRYSCAPE] = false;
$wgCapitalLinkOverrides[NS_ENTRYSCAPE_TALK] = false;

wfAddNamespace( 1266, 'Hivejs' );
$wgCapitalLinkOverrides[NS_HIVEJS] = false;
$wgCapitalLinkOverrides[NS_HIVEJS_TALK] = false;

wfAddNamespace( 1268, 'lib.reviews', 'NS_LIBREVIEWS' );
$wgCapitalLinkOverrides[NS_LIBREVIEWS] = false;
$wgCapitalLinkOverrides[NS_LIBREVIEWS_TALK] = false;
$wgTranslateGroupFiles[] = "$GROUPS/lib.reviews/lib.reviews.yaml";
$wgTranslateSupportUrlNamespace[NS_LIBREVIEWS] = [
	'page' => 'Translating_talk:lib.reviews',
	'params' => $talkParams,
];

wfAddNamespace( 1270, 'Oppia' );
$wgCapitalLinkOverrides[NS_OPPIA] = false;
$wgCapitalLinkOverrides[NS_OPPIA_TALK] = false;
$wgTranslateGroupFiles[] = "$GROUPS/Oppia/Oppia.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Oppia/OppiaAndroid.yaml";
$wgTranslateSupportUrlNamespace[NS_OPPIA] = [
	'page' => 'Translating_talk:Oppia',
	'params' => $talkParams,
];

wfAddNamespace( 1272, 'Dissemin' );
$wgCapitalLinkOverrides[NS_DISSEMIN] = false;
$wgCapitalLinkOverrides[NS_DISSEMIN_TALK] = false;

wfAddNamespace( 1274, 'Phabricator' );
$wgCapitalLinkOverrides[NS_PHABRICATOR] = false;
$wgCapitalLinkOverrides[NS_PHABRICATOR_TALK] = false;
$wgTranslateGroupFiles[] = "$GROUPS/Phabricator/PhabricatorAgg.yaml";
$wgTranslateGroupFiles[] = "$GROUPS/Phabricator/Phabricator.yaml";

wfAddNamespace( 1276, 'Ajapaik' );
$wgCapitalLinkOverrides[NS_AJAPAIK] = false;
$wgCapitalLinkOverrides[NS_AJAPAIK] = false;
$wgTranslateGroupFiles[] = "$GROUPS/Ajapaik/Ajapaik.yaml";

wfAddNamespace( 1278, 'LibreMesh' );
$wgCapitalLinkOverrides[NS_LIBREMESH] = false;
$wgCapitalLinkOverrides[NS_LIBREMESH_TALK] = false;
$wgTranslateGroupFiles[] = "$GROUPS/LibreMesh/LibreMesh.yaml";

# No longer in use
wfAddNamespace( 1280, 'MAZI' );
$wgCapitalLinkOverrides[NS_MAZI] = false;
$wgCapitalLinkOverrides[NS_MAZI_TALK] = false;

wfAddNamespace( 1282, 'Wikidocumentaries' );
$wgCapitalLinkOverrides[NS_WIKIDOCUMENTARIES] = false;
$wgCapitalLinkOverrides[NS_WIKIDOCUMENTARIES_TALK] = false;
$wgTranslateGroupFiles[] = "$GROUPS/Wikidocumentaries/Wikidocumentaries.yaml";
$wgTranslateSupportUrlNamespace[NS_WIKIDOCUMENTARIES] = [
	'url' => 'https://github.com/Wikidocumentaries/wikidocumentaries-ui/issues/new',
	'params' => $githubParams,
];

# No longer in use
wfAddNamespace( 1284, 'BitmarkInc' );

# No longer in use
wfAddNamespace( 1286, 'CovidRatio' );

wfAddNamespace( 1288, 'Projects' );
$wgTranslateGroupFiles[] = "$GROUPS/Projects/OpenHistoricalMap.yaml";
