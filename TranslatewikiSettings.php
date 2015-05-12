<?php

###
# Performance etc.
###
$wgMainCacheType = CACHE_MEMCACHED;
$wgMemCachedServers = array( "127.0.0.1:11211" );
$wgMemCachedTimeout = 150000;
$wgEnableSidebarCache = true;
$wgSessionsInMemcached = true;
$wgShowIPinHeader = false;
$wgAdaptiveMessageCache = true;
$wgJobRunRate = 0;

###
# Experimentalism
###
$wgWellFormedXml = false;
$wgExperimentalHtmlIds = true;
$wgAllUnicodeFixes = true;
$wgDevelopmentWarnings = true;

# Temporary till enabled by default in core, bug 9360
$wgPageLanguageUseDB = true;

$wgResourceLoaderValidateJS = false;
#$wgIncludeLegacyJavaScript = false;
$wgLegacyJavaScriptGlobals = false;

$wgDeprecationReleaseLimit = '1.21';


###
# Unsorted
###
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "";
$wgRightsText = "";
$wgRightsIcon = "";

$wgUseTidy = false;
$wgMaxShellMemory = 1024 * 200;

###
# Names
###
$wgSitename = 'translatewiki.net';
$wgEnableCanonicalServerLink = true;

$wgLogo = "//translatewiki.net/static/logo.png";

$wgGrammarForms['fi']['genitive']['translatewiki.net'] = 'translatewiki.netin';
$wgGrammarForms['fi']['inessive']['translatewiki.net'] = 'translatewiki.netissä';
$wgGrammarForms['fi']['illative']['translatewiki.net'] = 'translatewiki.netiin';
$wgGrammarForms['fi']['elative']['translatewiki.net'] = 'translatewiki.netistä';
$wgGrammarForms['fi']['partitive']['translatewiki.net'] = 'translatewiki.netiä';

$wgFooterIcons['poweredby']['netcup'] = "<div class='mw_poweredby'><a href=\"http://www.netcup.de/\" title=\"Powered by netcup - netcup.de – Webhosting, vServer, Servermanagement\" target=\"_blank\">Powered by netcup - netcup.de – Webhosting, vServer, Servermanagement</a></div>";

###
# Changes list
###
$wgRCMaxAge = 5 * 365 * 24 * 3600; // 5 years
$wgShowUpdatedMarker = true;
$wgUseRCPatrol = false;
$wgUseNPPatrol = false;
$wgRCLinkLimits = array( 100, 500 );
$wgRCLinkDays = array( 1, 7, 30 );

$wgCategoryPagingLimit = 500;
$wgMaximumMovedPages = 300;
$wgCategoryCollation = 'uca-default';

###
# Ajax spicy etc
###
$wgUseAutomaticEditSummaries = false;
$wgUseInstantCommons = true;

###
# User (account) settings
###
$wgAllowUserJs = true;
$wgAllowUserCss = true;
$wgMinimalPasswordLength = 6;
$wgBlockAllowsUTEdit = true;
$wgAutoConfirmAge = 3600 * 24 * 14;
$wgAccountCreationThrottle = 1;
$wgAutoblockExpiry = 3600 * 24 * 14; // 2 weeks of rest from vandals reusing IPs

$wgEnableEmail = true;
$wgEnableUserEmail = true;

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
#$wgDefaultUserOptions['lqtnotifytalk'] = true;
$wgDefaultUserOptions['watchcreations'] = true;

$wgCaptchaTriggers['createaccount'] = true; // Special:Userlogin&type=signup
$wgCaptchaRegexes[] = '/viagra|cialis/sDu';
$wgCaptchaTriggers['edit'] = true; // Would check on every edit
$wgCaptchaTriggers['create'] = true; // Check on page creation.
$wgCaptchaTriggers['addurl'] = true; // Check on edits that add URLs
$wgCaptchaTriggers['badlogin'] = true; // Special:Userlogin after failure

###
# Upload
###
$wgEnableUploads = true;
$wgAllowCopyUploads = true;
$wgUseImageMagick = false;
$wgUseTeX = true;
$wgFileExtensions = array( 'png', 'gif', 'jpg', 'jpeg', 'ogg', 'pdf', 'svg' );

$wgSVGConverter = 'rsvg';
$wgSVGConverters['rsvg'] = '$path/rsvg-convert -w $width -h $height $input -o $output';

###
# Namespaces
###
$wgNamespaceAliases['Betawiki'] = NS_PROJECT;
$wgNamespaceAliases['Betawiki_talk'] = NS_PROJECT_TALK;
$wgMetaNamespace = 'Project';

define( "NS_PORTAL", 100 );
define( "NS_PORTAL_TALK", 101 );
define( "NS_TRANSLATING", 1102 );
define( "NS_TRANSLATING_TALK", 1103 );

$wgNamespacesWithSubpages = array(
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
);

$wgExtraNamespaces[NS_PORTAL] = 'Portal';
$wgExtraNamespaces[NS_PORTAL_TALK] = 'Portal_talk';
$wgExtraNamespaces[NS_TRANSLATING] = 'Translating';
$wgExtraNamespaces[NS_TRANSLATING_TALK] = 'Translating_talk';

$wgContentNamespaces[] = NS_MEDIAWIKI;
$wgContentNamespaces[] = NS_TRANSLATING;

###
# Skins
###
require_once "$IP/skins/Vector/Vector.php";
require_once "$IP/skins/MonoBook/MonoBook.php";

###
# Extensions
###
wfLoadExtensions( array(
	'ApiSandbox',
	'BetaFeatures',
	'CharInsert',
	'Elastica',
	#'Gadgets',
	'Interwiki',
	'Nuke',
	'ParserFunctions',
	'Renameuser',
	'WikiEditor',
) );

$EXT = "$IP/extensions";

require_once "$EXT/cldr/cldr.php";
require_once "$EXT/CleanChanges/CleanChanges.php";
$wgCCUserFilter = true;
$wgCCTrailerFilter = true;
require_once "$EXT/UserDailyContribs/UserDailyContribs.php";

$wgNamespacesToBeSearchedDefault[NS_MAIN] = true;
$wgNamespacesToBeSearchedDefault[NS_MEDIAWIKI] = true;
require_once "$EXT/CirrusSearch/CirrusSearch.php";
$wgSearchType = 'CirrusSearch';
$wgAdvancedSearchHighlighting = true;

require_once "$EXT/I18nTags/I18nTags.php";
require_once "$EXT/Translate/Translate.php";
require_once __DIR__ . "/TranslateSettings.php";
require_once __DIR__ . "/LanguageSettings.php";

require_once __DIR__ . "/nikext.php";

$wgPFEnableStringFunctions = true;

require_once "$EXT/NewUserMessage/NewUserMessage.php";
$wgNewUserSuppressRC = true;
$wgNewUserMinorEdit = false;

require_once "$EXT/CharInsert/CharInsert.php";
require_once "$EXT/LiquidThreads/LiquidThreads.php";
require_once "$EXT/ContributionScores/ContributionScores.php";
$wgContribScoreIgnoreBots = true;

require_once "$EXT/UserMerge/UserMerge.php";
require_once "$EXT/WebChat/WebChat.php";
$wgWebChatChannel = '#mediawiki-i18n';
$wgWebChatClient = 'freenodeChat';

require_once "$EXT/Babel/Babel.php";
$wgBabelCategoryNames = array(
	'0' => 'User_%code%-0',
	'1' => 'User_%code%-1',
	'2' => 'User_%code%-2',
	'3' => 'User_%code%-3',
	'4' => 'User_%code%-4',
	'5' => 'User_%code%-5',
	'N' => 'User_%code%-N'
);
$wgBabelMainCategory = 'User_%code%';

require_once "$EXT/SyntaxHighlight_GeSHi/SyntaxHighlight_GeSHi.php";
require_once "$EXT/ReplaceText/ReplaceText.php";

# Semantic MediaWiki (installed using composer)
$smwgNamespaceIndex = 200; # Nike 2010-06-15
enableSemantics( 'translatewiki.net' );
$smwgNamespacesWithSemanticLinks[NS_LQT_THREAD] = true;
$smwgNamespacesWithSemanticLinks[NS_LQT_SUMMARY] = true;

require_once "$EXT/SemanticForms/SemanticForms.php";
$sfgRedLinksCheckOnlyLocalProps = true;

require_once "$EXT/TitleBlacklist/TitleBlacklist.php";
$wgTitleBlacklistSources = array(
	array(
		'type' => TBLSRC_LOCALPAGE,
		'src' => 'MediaWiki:Titleblacklist'
	)
);
require_once "$EXT/SpamBlacklist/SpamBlacklist.php";
$wgSpamBlacklistFiles = array(
	"http://meta.wikimedia.org/w/index.php?title=Spam_blacklist&action=raw&sb_ver=1",
);
$wgLogSpamBlacklistHits = true;
$wgNoFollowDomainExceptions = array(
	'laxstrom.name',
	'ultimategerardm.blogspot.com',
);

require_once "$EXT/AdminLinks/AdminLinks.php";
require_once "$EXT/UserOptionStats/UserOptionStats.php";
require_once "$EXT/EventLogging/EventLogging.php";
$wgEventLoggingBaseUri = 'http://bits.wikimedia.org/event.gif';

require_once "$EXT/UniversalLanguageSelector/UniversalLanguageSelector.php";
$wgULSEventLogging = true;

require_once "$EXT/TwnMainPage/MainPage.php";
$wgMainPageImages[] = array(
	'url' => '//translatewiki.net/static/mainpage/gorges-du-tarn.jpg',
	'attribution' => '<a href="//commons.wikimedia.org/wiki/File:01_Gorges_du_Tarn_Roc_des_Hourtous.jpg">CC BY Myrabella</a>',
);

$wgMainPageImages[] = array(
	'url' => '//translatewiki.net/static/mainpage/mabodalen.jpg',
	'attribution' => '<a href="//commons.wikimedia.org/wiki/File:M%C3%A5b%C3%B8dalen,_2011_August.jpg">CC BY-SA Simo Räsänen</a>',
);

$wgMainPageImages[] = array(
	'url' => '//translatewiki.net/static/mainpage/ferronor.jpg',
	'attribution' => '<a href="//commons.wikimedia.org/wiki/File:Ferronor_GR12U_412_Montadon_-_Potrerillos.jpg">CC BY-SA Kabelleger / David Gubler</a>',
);

$wgMainPageImages[] = array(
	'url' => '//translatewiki.net/static/mainpage/alpamayo.jpg',
	'attribution' => '<a href="//commons.wikimedia.org/wiki/File:Alpamayo.jpg">CC-0</a>',
);

$wgMainPageImages[] = array(
	'url' => '//translatewiki.net/static/mainpage/kasumi.jpg',
	'attribution' => '<a href="//commons.wikimedia.org/wiki/File:Imagoura_Kasumi_Coast04bs4440.jpg">CC BY 663highland</a>',
);

$wgMainPageImages[] = array(
	'url' => '//translatewiki.net/static/mainpage/aaretal.jpg',
	'attribution' => '<a href="//commons.wikimedia.org/wiki/File:Fr%C3%BChlingslandschft_Aaretal_Schweiz.jpg">CC BY-SA Benjamin Gimmel</a>',
);

$wgMainPageImages[] = array(
	'url' => '//translatewiki.net/static/mainpage/taburiente.jpg',
	'attribution' => '<a href="//commons.wikimedia.org/wiki/File:Caldera_de_Taburiente_La_Palma.jpg">CC BY-SA Luc Viatour</a>',
);

$wgMainPageImages[] = array(
	'url' => '//translatewiki.net/static/mainpage/concert-hall.jpg',
	'attribution' => '<a href="//commons.wikimedia.org/wiki/File:Image-Disney_Concert_Hall_by_Carol_Highsmith_edit.jpg">CC-0 Carol M. Highsmith</a>',
);

$wgMainPageImages[] = array(
	'url' => '//translatewiki.net/static/mainpage/golden-gate.jpg',
	'attribution' => '<a href="//commons.wikimedia.org/wiki/File:GoldenGateBridge_BakerBeach_MC.jpg">CC BY Christian Mehlführer</a>',
);

$wgMainPageImages[] = array(
	'url' => '//translatewiki.net/static/mainpage/ruhrtalbruecke.jpg',
	'attribution' => '<a href="//commons.wikimedia.org/wiki/File:Ruhrtalbruecke-Sonnenuntergang.jpg">CC BY-SA Tuxyso</a>',
);

$wgMainPageImages[] = array(
	'url' => '//translatewiki.net/static/mainpage/holzbrucke.jpg',
	'attribution' => '<a href="//commons.wikimedia.org/wiki/File:Holzbr%C3%BCcke_2011-02-10_15-15-08.JPG">CC BY-SA Roland zh</a>',
);

$wgMainPageImages[] = array(
	'url' => '//translatewiki.net/static/mainpage/acueducto.jpg',
	'attribution' => '<a href="//commons.wikimedia.org/wiki/File:AcueductoSegovia04.JPG">CC BY-SA Manuel González Olaechea y Franco</a>',
);

$wgMainPageImages[] = array(
	'url' => '//translatewiki.net/static/mainpage/turtle.jpg',
	'attribution' => '<a href="//commons.wikimedia.org/wiki/File:El_Gouna_Turtle_House_R01.jpg">CC BY Marc Ryckaert</a>',
);

require_once "$EXT/InviteSignup/InviteSignup.php";
$wgISGroups = array( 'translator' );

###
# Dynamic code starts here
###
if ( $wgCanonicalServer !== "https://translatewiki.net" ) {
	$wgHooks['SiteNoticeAfter'][] = function ( &$siteNotice ) {
		$siteNotice = "
	<big align=\"center\" dir='ltr'><strong>This is not a production site!
	Go to <a href='https://translatewiki.net'>translatewiki.net</a>!</strong></big>";
		return true;
	};
}

$wgHooks['GetLocalURL'][] = function ( &$title, &$url, $query ) {
	if ( !$title->isExternal() && $query == '' ) {
		$dbkey = wfUrlencode( $title->getPrefixedDBkey() );
		if ( strpos( $dbkey, '%3F' ) !== false || strpos( $dbkey, '%26' ) !== false || strpos( $dbkey, '//' ) !== false ) {
			global $wgScript;
			$url = "$wgScript?title=$dbkey";
		}
	}
	return true;
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
	} catch ( MWException $e ) {}
};

$wgHooks['LanguageGetNamespaces'][] = function ( &$list ) {
	// help
	unset( $list[12] );
	unset( $list[13] );

	global $wgTranslateMessageNamespaces;
	$msgs = array_flip( $wgTranslateMessageNamespaces );
	natcasesort( $list );
	$basic = $extra = array();
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

$wgResourceModules['twn.jserrorlog'] = array(
	'localBasePath' => __DIR__ . '/webfiles',
	'remoteBasePath' => "$wgScriptPath/webfiles",
	'scripts' => 'twn.jserrorlog.js',
);

$wgHooks['BeforePageDisplay'][] = function ( $out ) {
	$out->addModules( 'twn.jserrorlog' );
};
