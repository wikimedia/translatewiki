<?php

$wgGroupPermissions['*'            ]['edit'] = false;

$wgGroupPermissions['user'         ]['skipcaptcha'] = false;
$wgGroupPermissions['autoconfirmed']['skipcaptcha'] = true;
$wgGroupPermissions['bot'          ]['skipcaptcha'] = true; // registered bots
$wgGroupPermissions['sysop'        ]['skipcaptcha'] = true;
$wgGroupPermissions['translator'   ]['skipcaptcha'] = true;

// ialex 2009-07-19
// move restricted to translators due to the move attack
$wgGroupPermissions['user'         ]['move']                    = false;
$wgGroupPermissions['user'         ]['move-subpages']           = false;
$wgGroupPermissions['user'         ]['move-rootuserpages']      = false;
$wgGroupPermissions['autoconfirmed']['move']                    = false;
$wgGroupPermissions['autoconfirmed']['move-subpages']           = false;
$wgGroupPermissions['sysop'        ]['move']                    = true;
$wgGroupPermissions['sysop'        ]['move-subpages']           = true;
$wgGroupPermissions['sysop'        ]['move-rootuserpages']      = true;
$wgGroupPermissions['translator'   ]['move']                    = true;
$wgGroupPermissions['translator'   ]['move-subpages']           = true;
$wgGroupPermissions['translator'   ]['move-rootuserpages']      = true;
$wgGroupPermissions['translator'   ]['translate-messagereview'] = true;
$wgGroupPermissions['bot'          ]['move']                    = true;
$wgGroupPermissions['bot'          ]['move-subpages']           = true;
$wgGroupPermissions['bot'          ]['move-rootuserpages']      = true;

// nike 2009-08-03, 2011-03-11
$wgGroupPermissions['*'            ]['createtalk']              = false;
$wgGroupPermissions['user'         ]['createtalk']              = true;
$wgGroupPermissions['*'            ]['createpage']              = false;
$wgGroupPermissions['user'         ]['createpage']              = true;

// Siebrand 2009-09-27: preventing spam send from new accounts
$wgGroupPermissions['user'         ]['sendemail']               = false;
$wgGroupPermissions['autoconfirmed']['sendemail']               = true;

$wgGroupPermissions['*'            ]['minoredit']               = true;
$wgGroupPermissions['*'            ]['webchat']                 = true;

$wgGroupPermissions['translator'   ]['editinterface']           = true;
$wgGroupPermissions['translator'   ]['translate']               = true;
$wgGroupPermissions['sysop'        ]['delete']                  = true;
$wgGroupPermissions['translator'   ]['deletedhistory']          = true;

$wgGroupPermissions['import'       ]['import']                  = true;
$wgGroupPermissions['import'       ]['importupload']            = true;

$wgGroupPermissions['sysop'        ]['import']                  = false;
$wgGroupPermissions['sysop'        ]['importupload']            = false;
$wgGroupPermissions['sysop'        ]['suppressredirect']        = true;

$wgGroupPermissions['bureaucrat'   ]['renameuser']              = false;
$wgGroupPermissions['bureaucrat'   ]['userrights']              = false;
$wgGroupPermissions['bureaucrat'   ]['deleterevision']          = true;
$wgGroupPermissions['bureaucrat'   ]['deletelogentry']          = true;
$wgGroupPermissions['bureaucrat'   ]['suppressionlog']          = true;
$wgGroupPermissions['bureaucrat'   ]['nuke']                    = true;
$wgGroupPermissions['bureaucrat'   ]['interwiki']               = true;
$wgGroupPermissions['bureaucrat'   ]['pagetranslation']         = true;

$wgGroupPermissions['staff'        ]['usermerge']               = true;
$wgGroupPermissions['staff'        ]['renameuser']              = true;
$wgGroupPermissions['staff'        ]['apc']                     = true;
$wgGroupPermissions['staff'        ]['reset-passwords']         = true;
$wgGroupPermissions['staff'        ]['translate-import']        = true;
$wgGroupPermissions['staff'        ]['translate-manage']        = true;
$wgGroupPermissions['staff'        ]['nuke']                    = true;
$wgGroupPermissions['staff'        ]['replacetext']             = true;
$wgGroupPermissions['sysop'        ]['replacetext']             = false;
$wgGroupPermissions['staff'        ]['suppressrevision']        = true;

$wgGroupPermissions['offline'      ]['translate-import']        = true;

$wgAddGroups['staff'] = $wgRemoveGroups['staff'] = true;
$wgAddGroups['translate-proofr'] = array();
$wgRemoveGroups['bureaucrat'] = $wgAddGroups['bureaucrat'   ] = array( 'sysop', 'translator', 'bot', 'offline' );
$wgGroupsRemoveFromSelf['translator'] = array( 'translator' );

$wgSysopUserBans  = true;
$wgSysopRangeBans = true;

$wgNamespaceProtection[NS_FILE] = array( 'translate' );
$wgNamespaceProtection[NS_TEMPLATE] = array( 'translate' );

# Patrolling
$wgGroupPermissions['translator']['autopatrol'] = true;
$wgUseRCPatrol = true;
