<?php

wfLoadSkins( [ 'Vector', 'MonoBook', 'Timeless' ] );
$wgDefaultSkin = 'vector-2022';

// Extensions which we don't specify any configuration
wfLoadExtensions( [
	'AbuseFilter',
	'CharInsert',
	'cldr',
	'Elastica',
	'Gadgets',
	'I18nTags',
	'Interwiki',
	'LoginNotify',
	'Nuke',
	'OATHAuth',
	'ReplaceText',
	'Scribunto',
	'SyntaxHighlight_GeSHi',
	'TemplateSandbox',
	'TemplateStyles',
	'Thanks',
	'UniversalLanguageSelector',
	'UserMerge',
	'WebAuthn',
	'WikiEditor',
] );

$EXT = "$IP/extensions";

wfLoadExtension( 'CleanChanges' );
$wgCCUserFilter = true;
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
$wgPageTranslationLanguageList = 'sidebar-only';
$wgTranslatePermissionUrl = 'Special:MainPage';
$wgTranslateUseSandbox = true;
$wgTranslateSandboxPromotedGroup = 'translator';
$wgTranslateRecentChangesLanguageFilter = true;

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

$wgTranslateTranslationServices['MinT'] = [
	'type' => 'mint',
	'host' => 'https://cxserver.wikimedia.org',
	'timeout' => 3,
];

wfLoadExtension( 'ParserFunctions' );
$wgPFEnableStringFunctions = true;

wfLoadExtension( 'WebChat' );
$wgWebChatClient = 'LiberaChat';
$wgWebChatChannel = '#translatewiki';

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
$wgEchoNotificationIcons['site']['url'] = '/static/logo-square.svg';

wfLoadExtension( 'OAuth' );
$wgMWOAuthSecureTokenTransfer = true;
$wgGroupPermissions['user']['mwoauthproposeconsumer'] = true; // T330500
$wgGroupPermissions['user']['mwoauthupdateownconsumer'] = true; // T330500
$wgGroupPermissions['staff']['mwoauthmanageconsumer'] = true;
$wgGroupPermissions['staff']['mwoauthsuppress'] = true;
$wgGroupPermissions['staff']['mwoauthviewsuppressed'] = true;
$wgGroupPermissions['staff']['mwoauthviewprivate'] = true;

$wgPoolCounterConf = [
	'TranslateFetchTranslators' => [
		'class' => MediaWiki\PoolCounter\PoolCounterClient::class,
		'timeout' => 4,
		'workers' => 1, # How many workers per language
		'slots' => 4, # How many concurrent languages
		'maxqueue' => 4, # How many can queue per language
	],
];

wfLoadExtension( 'CheckUser' );
$wgCheckUserLogLogins = true;

wfLoadExtension( 'Maps' );

wfLoadExtension( 'CodeEditor' );
$wgDefaultUserOptions['usebetatoolbar'] = 1; // user option provided by WikiEditor extension

wfLoadExtension( 'CodeMirror' );

wfLoadExtensions( [ 'VisualEditor', 'Linter', 'DiscussionTools', 'LiquidThreads' ] );
// LiquidThreads is enabled by default on all talk pages. This hook handler disables it on pages without any threads.
// This makes manual opt-in/out unnecessary (and impossible), but adds a few additional database queries.
$wgHooks['LiquidThreadsIsLqtPage'][] = static function ( Title $title, bool &$isTalkPage ) {
	if ( !$title->exists() ) {
		$isTalkPage = false;
	} else {
		$db = \MediaWiki\MediaWikiServices::getInstance()->getDBLoadBalancer()->getConnection( DB_REPLICA );
		$query = $db->newSelectQueryBuilder();
		$isTalkPage = (bool)$query
			->from( 'thread' )
			->where( [
				'thread_article_namespace' => $title->getNamespace(),
				'thread_article_title' => $title->getDBkey(),

			] )
			->limit( 1 )
			->caller( __FILE__ )
			->fetchRowCount();
	}
};

// Linter extensions requires this to produce results.
$wgParsoidSettings[ 'linting' ] = true;

wfLoadExtension( 'BounceHandler' );
$wgVERPsecret = $privVERPsecret;
$wgBounceHandlerUnconfirmUsers = true;
$wgBounceRecordLimit = 5;
