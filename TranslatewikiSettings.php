<?php

$wgEnableCanonicalServerLink = true;

$wgEnableEmail = true;
$wgEnableUserEmail = true;

$wgEnotifUserTalk = true; # UPO
$wgEnotifWatchlist = true; # UPO
$wgEnotifMinorEdits = false;

$wgMainCacheType = CACHE_MEMCACHED;
$wgMemCachedServers = array( "127.0.0.1:11211" );
$wgMemCachedTimeout = 150000; // Value moved from a live hack to this settings. Raymond 2010-01-21
$wgEnableSidebarCache = true;
$wgSessionsInMemcached = true;
$wgDisableCounters = true;
$wgWellFormedXml = false; # Nike 2009-09-18
$wgExperimentalHtmlIds = true; # Nike 2010-01-30
$wgAllUnicodeFixes = true;
$wgAdaptiveMessageCache = true; # Nike 2010-08-08
$wgDevelopmentWarnings = true;

if ( !defined( 'HHVM_VERSION' ) ) {
	$wgExternalDiffEngine = 'wikidiff2';
}

$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "";
$wgRightsText = "";
$wgRightsIcon = "";

$wgUseTidy = false;
$wgSVGConverter = 'rsvg';
$wgSVGConverters['rsvg'] = '$path/rsvg-convert -w $width -h $height $input -o $output';
$wgMaxShellMemory = 1024 * 200;

###
# Names
###
$wgSitename = 'translatewiki.net';
$wgLogo = "//translatewiki.net/static/logo.png";

$wgGrammarForms['fi']['genitive']['translatewiki.net'] = 'translatewiki.netin';
$wgGrammarForms['fi']['inessive']['translatewiki.net'] = 'translatewiki.netissä';
$wgGrammarForms['fi']['illative']['translatewiki.net'] = 'translatewiki.netiin';
$wgGrammarForms['fi']['elative']['translatewiki.net'] = 'translatewiki.netistä';
$wgGrammarForms['fi']['partitive']['translatewiki.net'] = 'translatewiki.netiä';

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
$wgShowIPinHeader = false;
$wgUseAutomaticEditSummaries = false;
$wgUseInstantCommons = true;

###
# Jobs
###
$wgJobRunRate = 0;

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

###
# Upload
###
$wgEnableUploads = true;
$wgUseImageResize = true;
$wgUseImageMagick = false;
$wgUseTeX = true;
$wgFileExtensions = array( 'png', 'gif', 'jpg', 'jpeg', 'ogg', 'pdf', 'svg' );
$wgStrictFileExtensions = true;

# Temporary till enabled by default in core, bug 9360
$wgPageLanguageUseDB = true;

###
# Extensions
###

include( "$IP/extensions/cldr/cldr.php" );
include( "$IP/extensions/CleanChanges/CleanChanges.php" );
$wgCCUserFilter = true;
$wgCCTrailerFilter = true;
include( "$IP/extensions/UserDailyContribs/UserDailyContribs.php" );

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

// Skins
require_once "$IP/skins/Vector/Vector.php";
require_once "$IP/skins/MonoBook/MonoBook.php";

###
# Search
###
$wgNamespacesToBeSearchedDefault[NS_MAIN] = true;
$wgNamespacesToBeSearchedDefault[NS_MEDIAWIKI] = true;
require_once "$IP/extensions/Elastica/Elastica.php";
require_once "$IP/extensions/CirrusSearch/CirrusSearch.php";
$wgSearchType = 'CirrusSearch';
$wgAdvancedSearchHighlighting = true;

include( "$IP/extensions/I18nTags/I18nTags.php" );
include( "$IP/extensions/Translate/Translate.php" );
require( __DIR__ . "/TranslateSettings.php" );

include( __DIR__ . "/nikext.php" );
include( "$IP/extensions/Renameuser/Renameuser.php" );
include( "$IP/extensions/ParserFunctions/ParserFunctions.php" );
$wgMaxIfExistCount = 300;
$wgPFEnableStringFunctions = true;

include( "$IP/extensions/NewUserMessage/NewUserMessage.php" );
$wgNewUserSuppressRC = true;
$wgNewUserMinorEdit = false;

$wgCaptchaRegexes[] = '/viagra|cialis/sDu';
$wgCaptchaTriggers['edit'] = true; // Would check on every edit
$wgCaptchaTriggers['create'] = true; // Check on page creation.
$wgCaptchaTriggers['addurl'] = true; // Check on edits that add URLs
$wgCaptchaTriggers['createaccount'] = true; // Special:Userlogin&type=signup
$wgCaptchaTriggers['badlogin'] = true; // Special:Userlogin after failure

include( "$IP/extensions/CharInsert/CharInsert.php" );

# LiquidThreads - Siebrand / 2009-11-01
require( "$IP/extensions/LiquidThreads/LiquidThreads.php" );

# Just for fun
include( "$IP/extensions/ContributionScores/ContributionScores.php" );
$wgContribScoreIgnoreBots = true;

include( "$IP/extensions/Gadgets/Gadgets.php" );
include( "$IP/extensions/UserMerge/UserMerge.php" );

require( "$IP/extensions/WebChat/WebChat.php" );
$wgWebChatChannel = '#mediawiki-i18n';
$wgWebChatClient = 'freenodeChat';

require( "$IP/extensions/Babel/Babel.php" );
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
include( "$IP/extensions/SyntaxHighlight_GeSHi/SyntaxHighlight_GeSHi.php" );
include( "$IP/extensions/Interwiki/Interwiki.php" ); # Added by Raymond 2009-01-07
include( "$IP/extensions/Nuke/Nuke.php" ); # Nike 2009-01-12
include( "$IP/extensions/ReplaceText/ReplaceText.php" ); # Added: Siebrand 2009-04-25.
include( "$IP/extensions/ApiSandbox/ApiSandbox.php" ); # Added: Siebrand 2012-11-05.

# Semantic MediaWiki (installed using composer)
$smwgNamespaceIndex = 200; # Nike 2010-06-15
enableSemantics( 'translatewiki.net' );
$smwgNamespacesWithSemanticLinks[NS_LQT_THREAD] = true;
$smwgNamespacesWithSemanticLinks[NS_LQT_SUMMARY] = true;

# Semantic Forms (installed using composer)
$sfgRedLinksCheckOnlyLocalProps = true;

# Niklas 2011-11-12
require( "$IP/extensions/TitleBlacklist/TitleBlacklist.php" );
$wgTitleBlacklistSources = array(
 array(
 'type' => TBLSRC_LOCALPAGE,
 'src' => 'MediaWiki:Titleblacklist'
 )
);
require( "$IP/extensions/SpamBlacklist/SpamBlacklist.php" );
$wgSpamBlacklistFiles = array(
	"http://meta.wikimedia.org/w/index.php?title=Spam_blacklist&action=raw&sb_ver=1",
);
$wgLogSpamBlacklistHits = true;

include( "$IP/extensions/AdminLinks/AdminLinks.php" ); # Added by Siebrand 2012-02-06


#$wgExtraLanguageNames = array();
$wgExtraLanguageNames['sxu'] = 'Säggssch'; # Upper Saxon
$wgExtraLanguageNames['rtm'] = 'Faeag Rotuma'; # Rotuman
$wgExtraLanguageNames['wls'] = 'Faka\'uvea'; # Fakauvea
$wgExtraLanguageNames['twd'] = 'Tweants'; # Twents
$wgExtraLanguageNames['trp'] = 'Kokborok (Tripuri)'; # Kokborok
$wgExtraLanguageNames['pko'] = 'Pökoot'; # Pökoot
$wgExtraLanguageNames['pru'] = 'Prūsiskan'; # Prussian
$wgExtraLanguageNames['test'] = 'Test (site admin only)'; # Test
$wgExtraLanguageNames['swb'] = 'Shikomoro'; # Comorian
$wgExtraLanguageNames['njo'] = 'Ao'; # Ao Naga
$wgExtraLanguageNames['mni'] = 'মেইতেই লোন্'; # Meitei / Siebrand 2008-02-11
$wgExtraLanguageNames['ttt'] = 'Tati'; # Tat / Siebrand 2008-04-04
$wgExtraLanguageNames['yrl'] = 'ñe\'engatú'; # Nheengatu / Siebrand 2008-04-06
$wgExtraLanguageNames['krl'] = 'Karjala'; # Karelian / Siebrand 2008-04-12
$wgExtraLanguageNames['mwv'] = 'Behase Mentawei'; # Mentawai / Siebrand 2008-05-07
$wgExtraLanguageNames['niu'] = 'ko e vagahau Niuē'; # Niue / Nike 2008-06-29
$wgExtraLanguageNames['bew'] = 'Bahasa Betawi'; # Betawi / Siebrand 2008-07-13
$wgExtraLanguageNames['rw'] = 'Kinyarwanda'; # Kinyarwanda / Siebrand 2008-07-23
$wgExtraLanguageNames['slr'] = 'Salırça'; # Salar / Siebrand 2008-08-18
$wgExtraLanguageNames['ryu'] = 'ʔucināguci'; # Central Okinawan / Siebrand 2008-08-28
$wgExtraLanguageNames['gom'] = 'कोंकणी/Konknni '; # Konkani (falls back to gom-deva) / Siebrand 2008-09-02
$wgExtraLanguageNames['gom-deva'] = 'कोंकणी'; # Konkani (Devanagari script) / Siebrand 2008-09-02
$wgExtraLanguageNames['akz'] = 'Albaamo innaaɬiilka'; # Alabama / Siebrand 2008-09-15
$wgExtraLanguageNames['kgp'] = 'Kaingáng'; # Siebrand 2008-12-05
$wgExtraLanguageNames['hu-formal'] = 'Magyar (magázó)'; # Siebrand 2009-01-01
$wgExtraLanguageNames['kea'] = 'Kabuverdianu'; # Kabuverdianu / Siebrand 2009-01-07
$wgExtraLanguageNames['ady'] = 'Адыгэбзэ / Adygabze'; # Adyghe / Siebrand 2009-07-02
$wgExtraLanguageNames['ady-cyrl'] = 'Адыгэбзэ'; # Adyghe / Siebrand 2009-07-02
$wgExtraLanguageNames['tsd'] = 'Τσακωνικά'; # Tsakonian / Siebrand 2009-08-20
$wgExtraLanguageNames['gcf'] = 'Guadeloupean Creole French'; # Guadeloupean Creole French / Siebrand 2009-09-21
$wgExtraLanguageNames['lld'] = 'Ladin'; # Ladin / Siebrand 2009-09-23
$wgExtraLanguageNames['ruq-grek'] = 'Megleno-Romanian (Greek script)'; # Megleno-Romanian (Greek script) / Siebrand 2009-09-23
$wgExtraLanguageNames['ydd'] = 'Eastern Yiddish'; # Eastern Yiddish / Siebrand 2009-09-23
$wgExtraLanguageNames['tzm'] = 'ⵜⴰⵎⴰⵣⵉⵖⵜ'; # Tamazight / Siebrand 2009-09-23
$wgExtraLanguageNames['bto'] = 'Iriga Bicolano'; # Iriga Bicolano / Siebrand 2009-09-23
$wgExtraLanguageNames['rap'] = 'arero rapa nui'; # Rapa Nui / Siebrand 2009-11-13
$wgExtraLanguageNames['bfq'] = 'படகா'; # UBadaga / Siebrand 2009-11-19
$wgExtraLanguageNames['guc'] = 'Wayúu'; # Wayuu / Siebrand 2009-12-12
$wgExtraLanguageNames['mui'] = 'Musi'; # Musi / Siebrand 2010-02-11
$wgExtraLanguageNames['kbd-latn'] = 'Qabardjajəbza'; # Kabardian (Latin script) / Siebrand 2010-02-21
$wgExtraLanguageNames['ase'] = 'American sign language'; # Siebrand 2010-03-13
$wgExtraLanguageNames['mnc'] = 'ᠮᠠᠨᠵᡠ ᡤᡳᠰᡠᠨ'; # Manchu / Siebrand 2010-08-11
$wgExtraLanguageNames['aro'] = 'Araona'; # Araona / Siebrand 2010-08-25
$wgExtraLanguageNames['hif-deva'] = 'फ़ीजी हिन्दी'; # Fiji Hindi (Devangari script) / Siebrand 2010-08-26
$wgExtraLanguageNames['gah'] = 'Alekano'; # Alekano / Siebrand 2010-10-08
$wgExtraLanguageNames['rki'] = 'ရခိုင်'; # Rakhine / Siebrand 2010-10-14
$wgExtraLanguageNames['es-formal'] = 'español (formal)'; # Spanish (formal address) / Siebrand 2010-11-22
$wgExtraLanguageNames['nqo'] = 'ߒߞߏ'; # N'Ko / Siebrand 2011-01-11
$wgExtraLanguageNames['gbz'] = 'Dari'; # Zoroastrian Dari / Siebrand 2011-01-20
$wgExtraLanguageNames['gur'] = 'Gurenɛ'; # Farefare / Siebrand 2011-01-27
$wgExtraLanguageNames['yrk'] = 'Ненэцяʼ вада'; # Tundra Nenets / Lcawte 2011-02-07
$wgExtraLanguageNames['esu'] = 'Yup\'ik'; # Central Alaskan Yupik / Siebrand 2011-02-14
$wgExtraLanguageNames['saz'] = 'ꢱꣃꢬꢵꢯ꣄ꢡ꣄ꢬꢵ'; # Saurashtra / Siebrand 2011-03-17
$wgExtraLanguageNames['hsn'] = '湘语'; # Xiang Chinese / Siebrand 2011-04-06
$wgExtraLanguageNames['yua'] = 'Maaya T\'aan'; # Yucatán Maya / Siebrand 2011-04-09
$wgExtraLanguageNames['tkr'] = 'ЦӀаьхна миз'; # Tsakhur / Siebrand 2011-04-26
$wgExtraLanguageNames['aeb'] = ' زَوُن'; # Tunisian Arabic / Siebrand 2011-08-15
$wgExtraLanguageNames['pis'] = 'Pijin'; # Pijin / Siebrand 2011-08-25
$wgExtraLanguageNames['ppl'] = 'Nawat'; # Pipil / Siebrand 2011-08-30
$wgExtraLanguageNames['shn'] = 'လိၵ်ႈတႆး'; # Shan / Siebrand 2011-09-06
$wgExtraLanguageNames['mfe'] = 'Morisyen'; # Robin 2011-10-18
$wgExtraLanguageNames['ksf'] = 'Bafia'; # Robin 2011-10-21
$wgExtraLanguageNames['hne'] = 'छत्तीसगढ़ी'; # Amir 2011-12-01
$wgExtraLanguageNames['sly'] = 'Bahasa Selayar'; # A Selayar / Siebrand 2012-01-09
$wgExtraLanguageNames['ahr'] = 'अहिराणी'; # # Ahirani / Amir 2012-02-25
$wgExtraLanguageNames['mic'] = 'Mi\'kmaq'; # Micmac / Nikerabbit 2012-02-27
$wgExtraLanguageNames['mnw'] = 'ဘာသာ မန်'; # Mon / Amir 2012-05-31
$wgExtraLanguageNames['rut'] = 'мыхӀабишды'; # Rutul / Robin 2012-07-24
$wgExtraLanguageNames['acf'] = 'Saint Lucian Creole French'; # Saint Lucian Creole French / Siebrand 2012-08-05
$wgExtraLanguageNames['azb'] = 'تورکجه'; # South Azerbaijani / Robin 2012-11-14
$wgExtraLanguageNames['izh'] = 'ižoran keel'; # Ingrian / Robin 2012-11-17
$wgExtraLanguageNames['ban'] = 'ᬩᬲᬩᬮᬶ'; # Balinese / Siebrand 2012-11-25
$wgExtraLanguageNames['miq'] = 'Mískitu'; # Miskito / Siebrand 2013-07-02
$wgExtraLanguageNames['abs'] = 'Bahasa Ambon'; # Ambonese Malay / Siebrand 2013-07-05
$wgExtraLanguageNames['pbb'] = 'Nasa Yuwe'; # Páez / Siebrand 2013-08-08
$wgExtraLanguageNames['ses'] = 'Koyraboro Senni'; # Koyraboro Senni Songhay / Siebrand 2013-10-07
$wgExtraLanguageNames['sdh'] = 'کوردی خوارگ'; # Southern Kurdish / Siebrand 2013-10-07
$wgExtraLanguageNames['mww-latn'] = 'Hmoob Dawb'; # Hmong Daw / Siebrand 2013-12-02
$wgExtraLanguageNames['rmf'] = 'kaalengo tšimb'; # Romani language spoken in Finland / Nike 2014-03-05
$wgExtraLanguageNames['zgh'] = 'ⵜⴰⵎⴰⵣⵉⵖⵜ'; # Standard Moroccan Tamazight / Siebrand 2014-03-11
$wgExtraLanguageNames['luz'] = 'لری دومنی'; # Southern Luri / Ebe123  2014-07-29
$wgExtraLanguageNames['ciw'] = 'Ojibwemowin'; # Chippewa / Siebrand 2014-03-21
$wgExtraLanguageNames['lki'] = 'لەکی‎'; # Laki / Siebrand 2014-04-08
$wgExtraLanguageNames['kac'] = 'Jinghpaw'; # Jingpho / Siebrand 2014-04-08
$wgExtraLanguageNames['rcf'] = 'Kreol Réyoné'; # Réunion Creole French / 2014-04-08
$wgExtraLanguageNames['lkt'] = 'Lakȟótiyapi'; # Lakota / 2014-04-08
$wgExtraLanguageNames['jdt-cyrl'] = 'жугьури'; # Judeo-Tat / Siebrand 2014-06-17
$wgExtraLanguageNames['kjh'] = 'хакас'; # Khakas / Amire80 2014-06-17
$wgExtraLanguageNames['cnh'] = 'Lai holh'; # Haka Chin / Siebrand 2014-08-06

$wgExtraLanguageNames['nl-be'] = 'nl-be'; # Nikerabbit 2008-xx-xx - For FreeCol
$wgExtraLanguageNames['qqq'] = 'Message documentation'; # No linguistic content. Used for documenting messages
$wgExtraLanguageNames['ike'] = 'ᐃᓄᒃᑎᑐᑦ/inuktitut'; # Dunny to have portal appear in Special:SupportedLanguages

include( "$IP/extensions/WikiEditor/WikiEditor.php" ); # Re-enabled by Siebrand / 2011-08-30
$wgWikiEditorModules = array(
	'toolbar' => array( 'global' => false, 'user' => true ),
	'highlight' => array( 'global' => false, 'user' => true ),
	'preview' => array( 'global' => false, 'user' => true ),
	'publish' => array( 'global' => false, 'user' => true ),
//	'toc' => array( 'global' => false, 'user' => true ),
//	'templateEditor' => array( 'global' => false, 'user' => true ),
);

# Just for fun, I guess --- 2009-08-13 --Nike
include( "$IP/extensions/UserOptionStats/UserOptionStats.php" );

require_once "$IP/extensions/EventLogging/EventLogging.php";
$wgEventLoggingBaseUri = 'http://bits.wikimedia.org/event.gif';

require( "$IP/extensions/UniversalLanguageSelector/UniversalLanguageSelector.php" );
$wgULSEventLogging = true;
$wgDefaultUserOptions['uls-enable'] = true; # Remove when the option is no longer needed.

require_once( "$IP/extensions/TwnMainPage/MainPage.php" );
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

require "$IP/extensions/InviteSignup/InviteSignup.php";
$wgISGroups = array( 'translator' );

$wgUseExternalEditor = false;
$wgHiddenPrefs[] = 'stubthreshold';
$wgHiddenPrefs[] = 'userid';
$wgHiddenPrefs[] = 'math';
$wgHiddenPrefs[] = 'imagesize';
$wgHiddenPrefs[] = 'thumbsize';
$wgHiddenPrefs[] = 'highlightbroken';
$wgHiddenPrefs[] = 'nocache';
$wgHiddenPrefs[] = 'showtoc';
$wgHiddenPrefs[] = 'showjumplinks';
$wgHiddenPrefs[] = 'justify';
$wgHiddenPrefs[] = 'numberheadings';
$wgHiddenPrefs[] = 'livepreview';
//$wgHiddenPrefs[] = 'editondblclick';
$wgHiddenPrefs[] = 'watchmoves';
$wgHiddenPrefs[] = 'watchdeletion';
$wgHiddenPrefs[] = 'disablesuggest';
$wgHiddenPrefs[] = 'searchlimit';
$wgHiddenPrefs[] = 'contextlines';
$wgHiddenPrefs[] = 'contextchars';
$wgHiddenPrefs[] = 'diffonly';
$wgHiddenPrefs[] = 'norollbackdiff';
$wgHiddenPrefs[] = 'cols';
$wgSkipSkins[] = 'chick';
$wgSkipSkins[] = 'simple';
$wgSkipSkins[] = 'standard';
$wgSkipSkins[] = 'nostalgia';
$wgSkipSkins[] = 'cologneblue';

$wgAllowPageInfo = true;
$wgAllowCopyUploads = true;

$wgDefaultUserOptions['usenewrc'] = 1;
# Disabled 2012-08-20 / Nike / Too spammy/buggy.
#$wgDefaultUserOptions['lqtnotifytalk'] = true;
$wgDefaultUserOptions['watchcreations'] = true;

$wgResourceLoaderValidateJS = false;
#$wgIncludeLegacyJavaScript = false;
$wgLegacyJavaScriptGlobals = false;

$wgDeprecationReleaseLimit = '1.19';

$wgFooterIcons['poweredby']['netcup'] = "<div class='mw_poweredby'><a href=\"http://www.netcup.de/\" title=\"Powered by netcup - netcup.de – Webhosting, vServer, Servermanagement\" target=\"_blank\">Powered by netcup - netcup.de – Webhosting, vServer, Servermanagement</a></div>";

require "$IP/extensions/BetaFeatures/BetaFeatures.php";

# Dynamic code starts here

if ( $wgCanonicalServer !== "https://translatewiki.net" ) {
	$wgHooks['SiteNoticeAfter'] = array( 'nbwWarn' );
}

function nbwWarn( &$siteNotice ) {
	$siteNotice = "
<big align=\"center\" dir='ltr'><strong>This is not a production site!
Go to <a href='https://translatewiki.net'>translatewiki.net</a>!</strong></big>";
	return true;
}

$wgHooks['GetLocalURL'][] = 'cleanUrlExceptions';
function cleanUrlExceptions( &$title, &$url, $query ) {
	if ( !$title->isExternal() && $query == '' ) {
		$dbkey = wfUrlencode( $title->getPrefixedDBkey() );
		if ( strpos( $dbkey, '%3F' ) !== false || strpos( $dbkey, '%26' ) !== false || strpos( $dbkey, '//' ) !== false ) {
			global $wgScript;
			$url = "$wgScript?title=$dbkey";
		}
	}
	return true;
}

$wgExtensionFunctions[] = 'banAmp';
function banAmp() {
	global $wgRequest;
	try {
		$url = $wgRequest->getRequestURL();
		if ( strpos( $url, '&amp;' ) !== false ) {
			echo "&amp;amp; is disallowed in request urls";
			header( "HTTP/1.1 403 Forbidden" );
			exit();
		}
	} catch ( MWException $e ) {}
}

$wgHooks['LanguageGetNamespaces'][] = 'sortNamespaces';
function sortNamespaces( &$list ) {
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
}

$wgResourceModules['twn.jserrorlog'] = array(
	'localBasePath' => __DIR__ . '/webfiles',
	'remoteBasePath' => "$wgScriptPath/webfiles",
	'scripts' => 'twn.jserrorlog.js',
);

$wgHooks['BeforePageDisplay'][] = function ( $out ) {
	$out->addModules( 'twn.jserrorlog' );
};
