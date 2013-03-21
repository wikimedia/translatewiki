<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	die();
}
/**
 * Let our main page be usable and pretty.
 *
 * @file
 * @ingroup Extensions
 *
 * @author Niklas Laxström
 * @license GPL2+
 */

$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'Translatewiki.net main page',
	'version' => '2013-03-20',
	'author' => array( 'Niklas Laxström', 'Santhosh Thottingal' ),
	'descriptionmsg' => 'twnmp-desc',
);

$dir = __DIR__;

require_once( "$dir/Resources.php" );
require_once( "$dir/Autoload.php" );

$wgExtensionMessagesFiles['MainPage'] = "$dir/MainPage.i18n.php";
$wgExtensionMessagesFiles['MainPageAlias'] = "$dir/MainPage.alias.php";

$wgSpecialPages['TwnMainPage'] = 'SpecialTwnMainPage';


