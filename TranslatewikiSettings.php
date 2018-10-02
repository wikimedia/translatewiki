<?php

/**
 * Performance etc.
 */
$wgMiserMode = true;
$wgShowIPinHeader = false;

$wgMainCacheType = CACHE_MEMCACHED;
$wgMessageCacheType = CACHE_ACCEL;
$wgMemCachedServers = [ "127.0.0.1:11211" ];
$wgMemCachedPersistent = true;

$wgEnableSidebarCache = true;
$wgAdaptiveMessageCache = true;

$wgJobRunRate = 0;
$wgJobTypeConf['default'] = [ 'class' => 'JobQueueDB', 'order' => 'random', 'claimTTL' => 60 ];

$wgLocalisationCacheConf['store'] = 'files';
$wgLocalisationCacheConf['manualRecache'] = true;

$wgInvalidateCacheOnLocalSettingsChange = false;

$wgResourceLoaderValidateJS = false;

$wgHooks['SpecialPage_initList'][] = function ( &$list ) {
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
$wgCommentTableSchemaMigrationStage = MIGRATION_WRITE_BOTH;

$wgDeprecationReleaseLimit = '1.26';

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

$wgLogo = "https://translatewiki.net/static/logo.png";
$wgLogoHD = [
	'svg' => 'https://translatewiki.net/static/logo.svg',
];

$wgGrammarForms['et']['genitive']['translatewiki.net'] = 'translatewiki.net-i';
$wgGrammarForms['et']['inessive']['translatewiki.net'] = 'translatewiki.net-is';
$wgGrammarForms['et']['elative']['translatewiki.net'] = 'translatewiki.net-ist';

$wgGrammarForms['fi']['genitive']['translatewiki.net'] = 'translatewiki.netin';
$wgGrammarForms['fi']['inessive']['translatewiki.net'] = 'translatewiki.netissä';
$wgGrammarForms['fi']['illative']['translatewiki.net'] = 'translatewiki.netiin';
$wgGrammarForms['fi']['elative']['translatewiki.net'] = 'translatewiki.netistä';
$wgGrammarForms['fi']['partitive']['translatewiki.net'] = 'translatewiki.netiä';

// For TwnMainPage
array_unshift( $wgFooterIcons['poweredby'], [
	'url' => 'http://www.netcup.de/',
	'alt' => 'Powered by netcup - netcup.de – Webhosting, vServer, Servermanagement',
] );

// For Vector skin which does not support imageless icons except in the deprecated way.
$wgFooterIcons['netcup'][] = "<div class='mw_poweredby'><a href=\"http://www.netcup.de/\" title=\"Powered by netcup - netcup.de – Webhosting, vServer, Servermanagement\" target=\"_blank\">Powered by netcup - netcup.de – Webhosting, vServer, Servermanagement</a></div>";

/**
 * Changes list
 */
$wgRCMaxAge = 5 * 365 * 24 * 3600; // 5 years
$wgShowUpdatedMarker = true;
$wgUseRCPatrol = false;
$wgUseNPPatrol = false;
$wgRCLinkLimits = [ 100, 500 ];
$wgRCLinkDays = [ 1, 7, 30 ];

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
$wgMinimalPasswordLength = 6;
$wgBlockAllowsUTEdit = true;
$wgAutoConfirmAge = 3600 * 24 * 14;
$wgAutoblockExpiry = 3600 * 24 * 14; // 2 weeks of rest from vandals reusing IPs

$wgEnableEmail = true;
$wgEnableUserEmail = true;
$wgPasswordResetRoutes['email'] = true;

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
$wgHiddenPrefs[] = 'cols';

$wgDefaultUserOptions['usenewrc'] = 1;
# Disabled 2012-08-20 / Nike / Too spammy/buggy.
# $wgDefaultUserOptions['lqtnotifytalk'] = true;
$wgDefaultUserOptions['watchcreations'] = true;

$wgCaptchaTriggers['createaccount'] = true; // Special:Userlogin&type=signup
$wgCaptchaRegexes[] = '/viagra|cialis/sDu';
$wgCaptchaTriggers['edit'] = true; // Would check on every edit
$wgCaptchaTriggers['create'] = true; // Check on page creation.
$wgCaptchaTriggers['addurl'] = true; // Check on edits that add URLs
$wgCaptchaTriggers['badlogin'] = true; // Special:Userlogin after failure

/**
 * Upload
 */
$wgEnableUploads = true;
$wgAllowCopyUploads = true;
$wgUseImageMagick = false;
$wgUseTeX = true;
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
	NS_IMAGE_TALK => true,
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

/**
 * Skins
 */
wfLoadSkins( [ 'Vector', 'MonoBook', 'Timeless' ] );

/**
 * Extensions
 */

// Extensions which we don't specify any configuration
wfLoadExtensions( [
	'AbuseFilter',
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
require_once "$EXT/AdminLinks/AdminLinks.php";

wfLoadExtension( 'CleanChanges' );
$wgCCUserFilter = true;
$wgCCTrailerFilter = true;
$wgStructuredChangeFiltersShowPreference = true;
$wgCCFiltersOnly = true;

$wgNamespacesToBeSearchedDefault[NS_MAIN] = true;
$wgNamespacesToBeSearchedDefault[NS_MEDIAWIKI] = true;
require_once "$EXT/CirrusSearch/CirrusSearch.php";
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

require_once "$EXT/Translate/Translate.php";
require_once __DIR__ . "/TranslateSettings.php";
require_once __DIR__ . "/LanguageSettings.php";

require_once __DIR__ . "/nikext.php";

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

$wgNoFollowDomainExceptions = [
	'laxstrom.name',
	'ultimategerardm.blogspot.com',
];

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

wfLoadExtension( 'CheckUser' );
$wgCheckUserLogLogins = true;

/**
 * Dynamic code starts here
 */
if ( $wgCanonicalServer !== "https://translatewiki.net" ) {
	$wgHooks['SiteNoticeAfter'][] = function ( &$siteNotice ) {
		$siteNotice = "
	<div style='text-align: center; font-size: larger' dir='ltr'><strong>This is not a production site!
	Go to <a href='https://translatewiki.net'>translatewiki.net</a>!</strong></div>";
		return true;
	};
}

$wgHooks['GetLocalURL'][] = function ( &$title, &$url, $query ) {
	if ( !$title->isExternal() && $query == '' && $title->isMainPage() ) {
		$url = '/';
	}
};

$wgHooks['TestCanonicalRedirect'][] = function ( $request ) {
	return $request->getRequestURL() !== '/';
};

$wgExtensionFunctions[] = function () {
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

$wgHooks['LanguageGetNamespaces'][] = function ( &$list ) {
	global $wgTranslateMessageNamespaces;
	$msgs = array_flip( $wgTranslateMessageNamespaces );
	natcasesort( $list );
	$basic = $extra = [];
	foreach ( $list as $key => $text ) {
		if ( !isset( $msgs[$key - $key%2] ) ) {
			$basic[$key] = $text;
		} else {
			$extra[$key] = $text;
		}
	}

	$list = $basic + $extra;
	return true;
};

$wgResourceModules['twn.jserrorlog'] = [
	'localBasePath' => __DIR__ . '/webfiles',
	'remoteBasePath' => "$wgScriptPath/webfiles",
	'scripts' => 'twn.jserrorlog.js',
];

$wgHooks['BeforePageDisplay'][] = function ( $out ) {
	$out->addModules( 'twn.jserrorlog' );
};
