<?php

wfLoadSkins( [ 'Vector', 'MonoBook', 'Timeless' ] );
// Extensions which we don't specify any configuration
wfLoadExtensions( [
	'AbuseFilter',
	'AdminLinks',
	'CharInsert',
	'cldr',
	'Elastica',
	'Gadgets',
	'I18nTags',
	'Interwiki',
	'LiquidThreads',
	'LoginNotify',
	'Nuke',
	'OATHAuth',
	'Renameuser',
	'ReplaceText',
	'SyntaxHighlight_GeSHi',
	'TemplateSandbox',
	'Thanks',
	'UniversalLanguageSelector',
	'UserMerge',
	'UserOptionStats',
	'WikiEditor',
] );

$EXT = "$IP/extensions";

wfLoadExtension( 'CleanChanges' );
$wgCCUserFilter = true;
$wgCCTrailerFilter = true;
$wgStructuredChangeFiltersShowPreference = true;
$wgCCFiltersOnly = true;

wfLoadExtension( 'CirrusSearch' );
$wgSearchType = 'CirrusSearch';
$wgAdvancedSearchHighlighting = true;
$wgCirrusSearchWikimediaExtraPlugin[ 'regex' ] = [
	'build',
	'use',
	'use_extra_timeout',
];
$wgCirrusSearchUseExperimentalHighlighter = true;
$wgCirrusSearchOptimizeIndexForExperimentalHighlighter = true;
$wgCirrusSearchElasticQuirks = [
	'query_string_max_determinized_states' => true,
];
$wgCirrusSearchServers = [ $privESService ];

wfLoadExtension( 'Translate' );
$wgTranslateDocumentationLanguageCode = 'qqq';
$wgTranslateGroupRoot = '/resources/projects';
$wgTranslateMessageIndex = [ 'CDBMessageIndex' ];
$wgTranslateDelayedMessageIndexRebuild = true;
$wgTranslateDisablePreSaveTransform = true;
$wgULSCompactLanguageLinksBetaFeature = false;
$wgPageTranslationLanguageList = 'sidebar-only';
$wgTranslatePermissionUrl = 'Special:MainPage';
$wgTranslateUseSandbox = true;
$wgTranslateSandboxPromotedGroup = 'translator';

$wgTranslateTranslationServices['TTMServer'] = [
	'type' => 'ttmserver',
	'class' => 'ElasticSearchTTMServer',
	'cutoff' => 0.75,
	'public' => true,
	'config' => $privESService,
	'timeout' => 8,
	'use_wikimedia_extra' => true,
	'shards' => 1,
	'replicas' => 0,
];

$wgTranslateTranslationServices['Apertium WMF'] = [
	'type' => 'cxserver',
	'host' => 'https://cxserver.wikimedia.org',
	'timeout' => 3,
];

wfLoadExtension( 'ParserFunctions' );
$wgPFEnableStringFunctions = true;

wfLoadExtension( 'NewUserMessage' );
$wgNewUserSuppressRC = true;
$wgNewUserMinorEdit = false;

require_once "$EXT/ContributionScores/ContributionScores.php";
$wgContribScoreIgnoreBots = true;

wfLoadExtension( 'WebChat' );
$wgWebChatChannel = '#mediawiki-i18n';
$wgWebChatClient = 'freenodeChat';

wfLoadExtension( 'Babel' );
$wgBabelCategoryNames = [
	'0' => 'User_%code%-0',
	'1' => 'User_%code%-1',
	'2' => 'User_%code%-2',
	'3' => 'User_%code%-3',
	'4' => 'User_%code%-4',
	'5' => 'User_%code%-5',
	'N' => 'User_%code%-N'
];
$wgBabelMainCategory = 'User_%code%';

# Semantic MediaWiki (installed using composer)
$smwgNamespaceIndex = 200; # Nike 2010-06-15
enableSemantics( 'translatewiki.net' );
$smwgNamespacesWithSemanticLinks[90/*NS_LQT_THREAD*/] = true;
$smwgNamespacesWithSemanticLinks[92/*NS_LQT_SUMMARY*/] = true;
$smwgEnabledEditPageHelp = false;
$smwgQMaxInlineLimit = 2000;
$smwgQUpperbound = 2000;

wfLoadExtension( 'PageForms' );
$sfgRedLinksCheckOnlyLocalProps = true;

wfLoadExtension( 'TwnMainPage' );
$wgMainPageImages[] = [
	'url' => 'https://translatewiki.net/static/mainpage/gorges-du-tarn.jpg',
	'attribution' => '<a href="https://commons.wikimedia.org/wiki/File:01_Gorges_du_Tarn_Roc_des_Hourtous.jpg">CC BY Myrabella</a>',
];

$wgMainPageImages[] = [
	'url' => 'https://translatewiki.net/static/mainpage/mabodalen.jpg',
	'attribution' => '<a href="https://commons.wikimedia.org/wiki/File:M%C3%A5b%C3%B8dalen,_2011_August.jpg">CC BY-SA Simo Räsänen</a>',
];

$wgMainPageImages[] = [
	'url' => 'https://translatewiki.net/static/mainpage/ferronor.jpg',
	'attribution' => '<a href="https://commons.wikimedia.org/wiki/File:Ferronor_GR12U_412_Montadon_-_Potrerillos.jpg">CC BY-SA Kabelleger / David Gubler</a>',
];

$wgMainPageImages[] = [
	'url' => 'https://translatewiki.net/static/mainpage/alpamayo.jpg',
	'attribution' => '<a href="https://commons.wikimedia.org/wiki/File:Alpamayo.jpg">CC-0</a>',
];

$wgMainPageImages[] = [
	'url' => 'https://translatewiki.net/static/mainpage/kasumi.jpg',
	'attribution' => '<a href="https://commons.wikimedia.org/wiki/File:Imagoura_Kasumi_Coast04bs4440.jpg">CC BY 663highland</a>',
];

$wgMainPageImages[] = [
	'url' => 'https://translatewiki.net/static/mainpage/aaretal.jpg',
	'attribution' => '<a href="https://commons.wikimedia.org/wiki/File:Fr%C3%BChlingslandschft_Aaretal_Schweiz.jpg">CC BY-SA Benjamin Gimmel</a>',
];

$wgMainPageImages[] = [
	'url' => 'https://translatewiki.net/static/mainpage/taburiente.jpg',
	'attribution' => '<a href="https://commons.wikimedia.org/wiki/File:Caldera_de_Taburiente_La_Palma.jpg">CC BY-SA Luc Viatour</a>',
];

$wgMainPageImages[] = [
	'url' => 'https://translatewiki.net/static/mainpage/concert-hall.jpg',
	'attribution' => '<a href="https://commons.wikimedia.org/wiki/File:Image-Disney_Concert_Hall_by_Carol_Highsmith_edit.jpg">CC-0 Carol M. Highsmith</a>',
];

$wgMainPageImages[] = [
	'url' => 'https://translatewiki.net/static/mainpage/golden-gate.jpg',
	'attribution' => '<a href="https://commons.wikimedia.org/wiki/File:GoldenGateBridge_BakerBeach_MC.jpg">CC BY Christian Mehlführer</a>',
];

$wgMainPageImages[] = [
	'url' => 'https://translatewiki.net/static/mainpage/ruhrtalbruecke.jpg',
	'attribution' => '<a href="https://commons.wikimedia.org/wiki/File:Ruhrtalbruecke-Sonnenuntergang.jpg">CC BY-SA Tuxyso</a>',
];

$wgMainPageImages[] = [
	'url' => 'https://translatewiki.net/static/mainpage/holzbrucke.jpg',
	'attribution' => '<a href="https://commons.wikimedia.org/wiki/File:Holzbr%C3%BCcke_2011-02-10_15-15-08.JPG">CC BY-SA Roland zh</a>',
];

$wgMainPageImages[] = [
	'url' => 'https://translatewiki.net/static/mainpage/acueducto.jpg',
	'attribution' => '<a href="https://commons.wikimedia.org/wiki/File:AcueductoSegovia04.JPG">CC BY-SA Manuel González Olaechea y Franco</a>',
];

$wgMainPageImages[] = [
	'url' => 'https://translatewiki.net/static/mainpage/turtle.jpg',
	'attribution' => '<a href="https://commons.wikimedia.org/wiki/File:El_Gouna_Turtle_House_R01.jpg">CC BY Marc Ryckaert</a>',
];

wfLoadExtension( 'InviteSignup' );
$wgISGroups = [ 'translator' ];

wfLoadExtension( 'Echo' );
$wgEchoBundleEmailInterval = 14400;

wfLoadExtension( 'OAuth' );
$wgMWOAuthSecureTokenTransfer = true;
$wgGroupPermissions['sysop']['mwoauthproposeconsumer'] = true;
$wgGroupPermissions['sysop']['mwoauthupdateownconsumer'] = true;
$wgGroupPermissions['staff']['mwoauthmanageconsumer'] = true;
$wgGroupPermissions['staff']['mwoauthsuppress'] = true;
$wgGroupPermissions['staff']['mwoauthviewsuppressed'] = true;
$wgGroupPermissions['staff']['mwoauthviewprivate'] = true;

wfLoadExtension( 'PoolCounter' );
$wgPoolCounterConf = [
	'TranslateFetchTranslators' => [
		'class' => 'PoolCounter_Client',
		'timeout' => 4,
		'workers' => 1, # How many workers per language
		'slots' => 4, # How many concurrent languages
		'maxqueue' => 4, # How many can queue per language
	],
];

wfLoadExtension( 'CheckUser' );
$wgCheckUserLogLogins = true;