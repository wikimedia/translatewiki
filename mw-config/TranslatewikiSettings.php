<?php

/**
 * Performance etc.
 */
$wgMiserMode = true;

$wgMainCacheType = CACHE_MEMCACHED;
$wgMessageCacheType = CACHE_MEMCACHED;
$wgMemCachedServers = [ "127.0.0.1:11211" ];
$wgMemCachedPersistent = true;

$wgEnableSidebarCache = true;
$wgAdaptiveMessageCache = true;

$wgJobRunRate = 0;
$wgJobTypeConf['default'] = [ 'class' => 'JobQueueDB', 'order' => 'random', 'claimTTL' => 60 ];

$wgLocalisationCacheConf['store'] = 'files';
$wgLocalisationCacheConf['manualRecache'] = true;
$wgLocalisationCacheConf['storeDirectory'] = "$IP/cache";

$wgInvalidateCacheOnLocalSettingsChange = false;

$wgResourceLoaderValidateJS = false;

$wgHooks['SpecialPage_initList'][] = static function ( &$list ) {
	unset( $list['Allmessages'] );
};

/**
 * Experimentalism
 */
// Migrate off $wgExperimentalHtmlIds
$wgFragmentMode = [ 'html5' ];
$wgAllUnicodeFixes = true;
$wgDevelopmentWarnings = true;
$wgPageLanguageUseDB = true;
$wgLegacyJavaScriptGlobals = false;

/**
 * Unsorted
 */
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "";
$wgRightsText = "";
$wgRightsIcon = "";

$wgMaxShellMemory = 1024 * 200;

/**
 * Names
 */
$wgSitename = 'translatewiki.net';
$wgEnableCanonicalServerLink = true;

$wgEmergencyContact = 'translatewiki@translatewiki.net';
$wgPasswordSender = 'noreply@translatewiki.net';

$wgLogos = [
	'1x' => 'https://translatewiki.net/static/logo-square.png',
	'svg' => 'https://translatewiki.net/static/logo-square.svg',
	'icon' => 'https://translatewiki.net/static/logo-square.svg',
];

$wgGrammarForms['et']['genitive']['translatewiki.net'] = 'translatewiki.net-i';
$wgGrammarForms['et']['inessive']['translatewiki.net'] = 'translatewiki.net-is';
$wgGrammarForms['et']['elative']['translatewiki.net'] = 'translatewiki.net-ist';

$wgGrammarForms['fi']['genitive']['translatewiki.net'] = 'translatewiki.netin';
$wgGrammarForms['fi']['inessive']['translatewiki.net'] = 'translatewiki.netissä';
$wgGrammarForms['fi']['illative']['translatewiki.net'] = 'translatewiki.netiin';
$wgGrammarForms['fi']['elative']['translatewiki.net'] = 'translatewiki.netistä';
$wgGrammarForms['fi']['partitive']['translatewiki.net'] = 'translatewiki.netiä';

array_unshift(
	$wgFooterIcons['poweredby'],
	[
		'url' => 'http://www.netcup.de/',
		'alt' => 'Powered by netcup - netcup.de – Webhosting, vServer, Servermanagement',
	],
	[
		'src' => 'https://translatewiki.net/static/spi.svg',
		'alt' => 'Software in the Public Interest associated project',
		'url' => 'https://www.spi-inc.org/projects/translatewiki.net/',
		'height' => '31',
		'width' => false,
	]
 );

// For Vector skin which does not support imageless icons except in the deprecated way.
$wgFooterIcons['netcup'][] = "<div class='mw_poweredby'><a href=\"http://www.netcup.de/\" title=\"Powered by netcup - netcup.de – Webhosting, vServer, Servermanagement\" target=\"_blank\">Powered by netcup - netcup.de – Webhosting, vServer, Servermanagement</a></div>";

/**
 * Changes list
 */
$wgRCMaxAge = 5 * 365 * 24 * 3600; // 5 years
$wgUseRCPatrol = false;
$wgUseNPPatrol = false;
$wgRCLinkLimits = [ 100, 500 ];
$wgRCLinkDays = [ 1, 7, 30 ];

$wgRCFeeds['irc'] = [
	'formatter' => 'IRCColourfulRCFeedFormatter',
	'uri' => $privIRCService,
	'add_interwiki_prefix' => false,
];

$wgCategoryPagingLimit = 500;
$wgMaximumMovedPages = 300;
$wgCategoryCollation = 'uca-default';

/**
 * Ajax spicy etc
 */
$wgUseAutomaticEditSummaries = false;
$wgUseInstantCommons = true;

/**
 * User (account) settings
 */
$wgAllowUserJs = true;
$wgAllowUserCss = true;
$wgPasswordPolicy['policies']['default']['MinimalPasswordLength'] = [
	'value' => 6,
	'suggestChangeOnLogin' => false
];
$wgAutoConfirmAge = 3600 * 24 * 14;
$wgAutoblockExpiry = 3600 * 24 * 14; // 2 weeks of rest from vandals reusing IPs

$wgEnotifUserTalk = true;
$wgEnotifWatchlist = true;
$wgEnotifMinorEdits = false;

$wgHiddenPrefs[] = 'stubthreshold';
$wgHiddenPrefs[] = 'userid';
$wgHiddenPrefs[] = 'math';
$wgHiddenPrefs[] = 'imagesize';
$wgHiddenPrefs[] = 'thumbsize';
$wgHiddenPrefs[] = 'nocache';
$wgHiddenPrefs[] = 'showtoc';
$wgHiddenPrefs[] = 'showjumplinks';
$wgHiddenPrefs[] = 'justify';
$wgHiddenPrefs[] = 'numberheadings';
$wgHiddenPrefs[] = 'livepreview';
$wgHiddenPrefs[] = 'watchmoves';
$wgHiddenPrefs[] = 'watchdeletion';
$wgHiddenPrefs[] = 'disablesuggest';
$wgHiddenPrefs[] = 'searchlimit';
$wgHiddenPrefs[] = 'contextlines';
$wgHiddenPrefs[] = 'contextchars';
$wgHiddenPrefs[] = 'diffonly';
$wgHiddenPrefs[] = 'norollbackdiff';

$wgDefaultUserOptions['usenewrc'] = 1;
# Disabled 2012-08-20 / Nike / Too spammy/buggy.
# $wgDefaultUserOptions['lqtnotifytalk'] = true;
$wgDefaultUserOptions['watchcreations'] = true;

/**
 * Upload
 */
$wgEnableUploads = true;
$wgAllowCopyUploads = true;
$wgUseImageMagick = false;
$wgFileExtensions = [ 'png', 'gif', 'jpg', 'jpeg', 'ogg', 'pdf', 'svg' ];

$wgSVGConverter = 'rsvg';
$wgSVGConverters['rsvg'] = '$path/rsvg-convert -w $width -h $height $input -o $output';

/**
 * Namespaces
 */
$wgNamespaceAliases['Betawiki'] = NS_PROJECT;
$wgNamespaceAliases['Betawiki_talk'] = NS_PROJECT_TALK;
$wgMetaNamespace = 'Project';

define( "NS_PORTAL", 100 );
define( "NS_PORTAL_TALK", 101 );
define( "NS_TRANSLATING", 1102 );
define( "NS_TRANSLATING_TALK", 1103 );

$wgNamespacesWithSubpages = [
	NS_MAIN => true,
	NS_TALK => true,
	NS_USER => true,
	NS_USER_TALK => true,
	NS_PROJECT => true,
	NS_PROJECT_TALK => true,
	NS_FILE_TALK => true,
	NS_MEDIAWIKI => true,
	NS_MEDIAWIKI_TALK => true,
	NS_TEMPLATE => true,
	NS_TEMPLATE_TALK => true,
	NS_HELP_TALK => true,
	NS_CATEGORY => true,
	NS_CATEGORY_TALK => true,
	NS_TRANSLATING => true,
	NS_TRANSLATING_TALK => true,
	NS_PORTAL => true,
];

$wgExtraNamespaces[NS_PORTAL] = 'Portal';
$wgExtraNamespaces[NS_PORTAL_TALK] = 'Portal_talk';
$wgExtraNamespaces[NS_TRANSLATING] = 'Translating';
$wgExtraNamespaces[NS_TRANSLATING_TALK] = 'Translating_talk';

$wgContentNamespaces[] = NS_MEDIAWIKI;
$wgContentNamespaces[] = NS_TRANSLATING;

$wgNamespacesToBeSearchedDefault[NS_MAIN] = true;
$wgNamespacesToBeSearchedDefault[NS_MEDIAWIKI] = true;

$wgNoFollowDomainExceptions = [
	'laxstrom.name',
	'ultimategerardm.blogspot.com',
];

require_once __DIR__ . '/ExtensionSettings.php';
require_once "$EXT/Translate/utils/lc.php";
require_once __DIR__ . '/TranslateSettings.php';
require_once __DIR__ . '/nikext.php';
require_once __DIR__ . '/NewUserMessageJob.php';

/**
 * Dynamic code starts here
 */
// Make it clear to see when canary is in use
$script = $_SERVER['SCRIPT_NAME'] ?? '';
if ( substr( $script, 0, 3 ) === '/x/' ) {
		$wgHooks['SiteNoticeAfter'][] = static function ( &$siteNotice ) {
				$siteNotice .= '<div dir="ltr">You are using canary!</div>';
		};

		$wgHooks['OutputPageBodyAttributes'][] = static function ( $out, $skin, &$attrs ) {
				$add = 'background: repeating-linear-gradient( -55deg, #f6ba52, #f6ba52 10px, #ffd180 10px, #ffd180 20px );';
				$attrs['style'] = ( $attrs['style'] ?? '' ) . $add;
		};
}

$wgMainPageIsDomainRoot = true;

$wgExtensionFunctions[] = static function () {
	global $wgRequest;
	try {
		$url = $wgRequest->getRequestURL();
		if ( strpos( $url, '&amp;' ) !== false ) {
			echo "&amp;amp; is disallowed in request urls";
			header( "HTTP/1.1 403 Forbidden" );
			exit();
		}
	} catch ( MWException $e ) {
	}
};

$wgHooks['LanguageGetNamespaces'][] = static function ( &$list ) {
	global $wgTranslateMessageNamespaces;
	$msgs = array_flip( $wgTranslateMessageNamespaces );
	natcasesort( $list );
	$basic = $extra = [];
	foreach ( $list as $key => $text ) {
		if ( !isset( $msgs[$key - $key % 2] ) ) {
			$basic[$key] = $text;
		} else {
			$extra[$key] = $text;
		}
	}

	$list = $basic + $extra;
	return true;
};

$wgHooks['GetPreferences'][] = static function ( User $user, array &$preferences ) {
	// 'translate-pref-nonewsletter' is used as opt-out for
	// users with a confirmed email address
	$preferences['translate-nonewsletter'] = [
		'type' => 'toggle',
		'section' => 'personal/email',
		'label-message' => 'translate-pref-nonewsletter',
	];
};

$wgResourceModules['twn.jserrorlog'] = [
	'localBasePath' => "$IP/webfiles",
	'remoteBasePath' => "$wgScriptPath/webfiles",
	'scripts' => 'twn.jserrorlog.js',
];

$wgHooks['BeforePageDisplay'][] = static function ( $out ) {
	$out->addModules( 'twn.jserrorlog' );
};

$wgJobClasses['NewUserMessageJob'] = NewUserMessageJob::class;
$wgHooks['Translate:TranslatorSandbox:UserPromoted'][] = static function ( User $user ) {
	\MediaWiki\MediaWikiServices::getInstance()
		->getJobQueueGroup()->push( new NewUserMessageJob( [ 'userId' => $user->getId() ] ) );
};
