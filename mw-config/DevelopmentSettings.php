<?php

// phpcs:ignore MediaWiki.NamingConventions.ValidGlobalName.allowedPrefix
global $DOMAIN, $IP;
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

$wgSitename = 'translatewiki.net development environment';
$wgServer = $wgCanonicalServer = "https://$DOMAIN";

$wgArticlePath = "/wiki/$1";
$wgScriptPath = "/w";

$wgDBname = 'wiki';
$wgDBuser = 'wikiuser';
$wgDBpassword = '';
$wgSQLMode = 'TRADITIONAL,ONLY_FULL_GROUP_BY';

$wgLogos = [
	'1x' => 'https://translatewiki.net/static/logo-square.png',
	'svg' => 'https://translatewiki.net/static/logo-square.svg',
	'icon' => 'https://translatewiki.net/static/logo-square.svg',
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

$wgRCMaxAge = 5 * 365 * 24 * 3600; // 5 years
$wgCategoryCollation = 'uca-default';
$wgUseAutomaticEditSummaries = false;
$wgUseInstantCommons = true;
$wgAllowUserJs = true;
$wgAllowUserCss = true;
$wgPageLanguageUseDB = true;

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
$privVERPsecret = 'dummyvalue';

require_once __DIR__ . '/PermissionSettings.php';
require_once __DIR__ . '/ExtensionSettings.php';

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

$wgHooks['Translate:GettextFormat:headerFields'][] = static function ( &$specs, $group, $code ) {
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
