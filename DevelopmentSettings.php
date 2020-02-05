<?php

// phpcs:ignore MediaWiki.NamingConventions.ValidGlobalName.allowedPrefix
global $CONF, $DOMAIN, $IP;
$LOGS = "/www/$DOMAIN/logs";

# Debug settings

date_default_timezone_set( 'UTC' );
error_reporting( E_ALL | E_STRICT );
ini_set( 'display_errors', 1 );
ini_set( 'error_log', "$LOGS/error_php" );
$wgShowExceptionDetails = true;
$wgDebugComments        = true;
$wgDebugDumpSql         = true;
$wgDevelopmentWarnings  = true;
$wgDebugTimestamps = true;
$wgDebugPrintHttpHeaders = true;
$wgDebugToolbar = true;
$wgInvalidateCacheOnLocalSettingsChange = true;
$wgDeprecationReleaseLimit = false;
$wgAPICacheHelpTimeout = 0;

$wgMWLoggerDefaultSpi = [
	'class' => MediaWiki\Logger\MonologSpi::class,
	'args' => [ [
		'loggers' => [
			'@default' => [
				'processors' => [ 'psr' ],
				'handlers' => [ 'default' ],
			],
			'error' => [
				'processors' => [ 'psr' ],
				'handlers' => [ 'mediawiki-error' ]
			],
			'translation-exports' => [
				'processors' => [ 'psr' ],
				'handlers' => [ 'mediawiki' ]
			],
			'Translate.Jobs' => [
				'processors' => [ 'psr' ],
				'handlers' => [ 'mediawiki' ]
			],
			'TranslationNotifications.Jobs' => [
				'processors' => [ 'psr' ],
				'handlers' => [ 'mediawiki' ]
			],
		],
		'processors' => [
			'psr' => [ 'class' => Monolog\Processor\PsrLogMessageProcessor::class ],
		],
		'handlers' => [
			'mediawiki-error' => [
				'class' => Monolog\Handler\StreamHandler::class,
				'args' => [ "$LOGS/mediawiki-error" ],
				'formatter' => 'line-stack'
			],
			'mediawiki' => [
				'class' => Monolog\Handler\StreamHandler::class,
				'args' => [ "$LOGS/mediawiki" ],
				'formatter' => 'line'
			],
			'default' => [
				'class' => Monolog\Handler\StreamHandler::class,
				'args' => [ "$LOGS/default" ],
				'formatter' => 'line'
			],
			'blackhole' => [
				'class' => Monolog\Handler\NullHandler::class,
			],
		],
		'formatters' => [
			'line' => [
				'class' => Monolog\Formatter\LineFormatter::class
			],
			'line-stack' => [
				'class' => Monolog\Formatter\LineFormatter::class,
				'calls' => [ 'includeStacktraces' => [ true ] ]
			],
		]
	] ]
];

$wgSitename = 'translatewiki.net development environment';
$wgServer = $wgCanonicalServer = "https://$DOMAIN";

$wgArticlePath = "/wiki/$1";
$wgScriptPath = "/w";

$wgDBname = 'wiki';
$wgDBuser = 'wikiuser';
$wgDBpassword = '';
$wgSQLMode = 'TRADITIONAL';

$wgLogos = [
	'1x' => 'https://translatewiki.net/static/logo.png',
	'svg' => 'https://translatewiki.net/static/logo.svg',
];

$wgEnableUploads = true;
$wgUploadDirectory = "/www/$DOMAIN/docroot/images";
$wgUploadPath = "$wgCanonicalServer/images";

$wgMainCacheType    = CACHE_MEMCACHED;
$wgMemCachedServers = [ '127.0.0.1:11211' ];
$wgMessageCacheType = CACHE_DB;
$wgParserCacheType  = CACHE_DB;
$wgEnableSidebarCache = true;
$wgAdaptiveMessageCache = true;
$wgLocalisationCacheConf['store'] = 'files';
$wgMaxShellMemory = 1024 * 200;
$wgJobRunRate = 0;
$wgJobTypeConf['default'] = [ 'class' => 'JobQueueDB', 'order' => 'random', 'claimTTL' => 60 ];
$wgMiserMode = true;
$wgShowIPinHeader = false;

$wgRCMaxAge = 5 * 365 * 24 * 3600; // 5 years
$wgCategoryCollation = 'uca-default';
$wgUseAutomaticEditSummaries = false;
$wgUseInstantCommons = true;
$wgAllowUserJs = true;
$wgAllowUserCss = true;

$wgMetaNamespace = 'Project';
define( 'NS_PORTAL', 100 );
define( 'NS_PORTAL_TALK', 101 );
define( 'NS_TRANSLATING', 1102 );
define( 'NS_TRANSLATING_TALK', 1103 );
$wgExtraNamespaces[NS_PORTAL] = 'Portal';
$wgExtraNamespaces[NS_PORTAL_TALK] = 'Portal_talk';
$wgExtraNamespaces[NS_TRANSLATING] = 'Translating';
$wgExtraNamespaces[NS_TRANSLATING_TALK] = 'Translating_talk';
$wgContentNamespaces[] = NS_MEDIAWIKI;
$wgContentNamespaces[] = NS_TRANSLATING;

$privESService = [ 'localhost' ];

require_once "$CONF/PermissionSettings.php";
require_once "$CONF/ExtensionSettings.php";

$wgCacheDirectory = "/resources/caches/$DOMAIN/general";
$wgTranslateCacheDirectory = "/resources/caches/$DOMAIN/groups";
$wgTranslateWorkflowStates = [
	'progress' => [ 'color' => 'E00' ],
	'proofreading' => [ 'color' => 'FFBF00' ],
	'ready' => [ 'color' => 'FF0' ],
	'published' => [ 'color' => 'AEA', 'right' => 'translate-manage' ],
	'state conditions' => [
		[ 'ready', [ 'PROOFREAD' => 'MAX' ] ],
		[ 'proofreading', [ 'TRANSLATED' => 'MAX' ] ],
		[ 'progress', [ 'UNTRANSLATED' => 'NONZERO' ] ],
		[ 'unset', [ 'UNTRANSLATED' => 'MAX', 'OUTDATED' => 'ZERO', 'TRANSLATED' => 'ZERO' ] ],
	],
];

$wgHooks['Translate:GettextFFS:headerFields'][] = function ( $specs, $group, $code ) {
	global $wgSitename, $wgCanonicalServer;
	$specs['Project-Id-Version'] = $group->getLabel();
	$specs['Report-Msgid-Bugs-To'] = $wgSitename;
	$server = $wgCanonicalServer;
	$specs['X-Translation-Project'] = "$wgSitename <$server>";
};

$lqtParams = [
	'lqt_method' => 'talkpage_new_thread',
	'lqt_subject_field' => 'About [[%MESSAGE%]]',
];
$wgTranslateSupportUrl = [
	'page' => 'Support',
	'params' => $lqtParams,
];
