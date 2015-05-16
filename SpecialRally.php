<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	die();
}

$wgExtensionMessagesFiles['Rally'] = __FILE__;

$messages = array();
$messages['en'] = array(
	'rally500' => '500 new messages rally',
);

$aliases = array();
$specialPageAliases['en'] = array(
	'Rally500' => array( 'Rally500' ),
);

$wgSpecialPages['Rally500'] = 'SpecialRally500';

if ( !class_exists( 'SpecialRally500' ) ) {
	class SpecialRally500 extends SpecialPage {
		function __construct() {
			parent::__construct( 'Rally500' );
		}

		function execute( $par ) {
			$output = $this->getOutput();
			$currentUser = $this->getUser();

			$output->addHtml( '<div lang="en" dir="ltr" class="mw-content-ltr">' );

			$this->setHeaders();
			$this->outputHeader();

			$allowedGroups = array(
				'core', // core
				'ext-0-wikimedia', // Wikimedia extensions
				'out-wikimedia-mobile-0-all', // Wikimedia mobile apps
				'wiki-betawiki', // translatewiki.net UI
				'page-0-all', // translatewiki.net translatable pages
				'out-kiwix', // Kiwix
				'out-huggle', // Huggle
				'tsint-0-all', // Intuition
				'out-jquery-uls', // jQuery ULS
				'wiki-twn-mainpage', // translatewiki.net main page
				'out-pywikipedia-0-all', // Pywikibot
				'out-vicuna', // Vicona uploader
				'out-wikiblame', // WikiBlame
			);

			$allowedGroups = MessageGroups::expandWildcards( '*' );

			$allGroups = MessageGroups::singleton()->getGroups();
			foreach ( $allGroups as $groupId => $messageGroup ) {
				if ( in_array( $groupId, $allowedGroups ) ) {
					if( $messageGroup->isMeta() ) {
						// @todo Ugly work around for MediaWikiTopMessageGroup::getGroups()
						//       not existing.
						if( $messageGroup->getId() === 'core-0-mostused' )
							continue;

						$groups = $messageGroup->getGroups();
						foreach ( $groups as $gid => $groupDetails ) {
							$allowedGroups[] = $gid;
						}
					}
				}
			}

			$allowedGroups = array_flip( $allowedGroups );

			$dbr = wfGetDB( DB_SLAVE );
			$allowed = $currentUser->isAllowed( 'translate-manage' );
			if ( $allowed && !$par ) {
				$products = array(
					'MediaWiki core' => 0, // 8
					'MediaWiki extensions used by Wikimedia' => 0, // 8
					'Wikimedia Mobile apps' => 0, //
					'translatewiki.net' => 0, // 1198, 8,
					'jQuery.ULS' => 0,
					'Kiwix' => 0,
					'Huggle' => 0,
					'Intuition' => 0,
					'Pywikibot' => 0,
					'VicuñaUploader' => 0,
					'WikiBlame' => 0,
				);
				$languages = array();
				$userLangs = array();
				$tables = array( 'recentchanges', 'page' );
				$fields = array(
					'rc_title',
					'rc_namespace',
					'rc_user_text',
					'rc_this_oldid',
					'page_latest'
				);
				$conds = array(
					"rc_timestamp >= 20150517000000",
					"rc_timestamp <= 20150525235959",
					'rc_new' => 1,
					'rc_bot' => 0,
					'rc_namespace IN ( 8, 1198, 1206, 1238, 1240, 1244, 1248, 1252 )',
					'rc_cur_id = page_id'
				);

				$data = array();
				$nonlatest = array();
				$res = $dbr->select( $tables, $fields, $conds, __METHOD__ );
				foreach ( $res as $row ) {
					// Strip language code suffix.
					$title = preg_replace( '~/[a-z-]+$~i', '', $row->rc_title );
					$group = TranslateUtils::messageKeyToGroup( $row->rc_namespace, $title );
					if ( isset( $allowedGroups[$group] ) ) {
						if ( !isset( $data[$row->rc_user_text] ) ) {
							$data[$row->rc_user_text] = 0;
						}

						if ( !isset( $nonlatest[$row->rc_user_text] ) ) {
							$nonlatest[$row->rc_user_text] = 0;
						}

						$data[$row->rc_user_text]++;
						if ( $row->rc_this_oldid != $row->page_latest ) {
							$nonlatest[$row->rc_user_text]++;
						}

						if ( in_array( $row->rc_namespace, array( 8 ) ) ) {
							if ( in_array( $group, array( 'core' ) ) ) {
								$products['MediaWiki core']++;
							} elseif ( !in_array( $group, array( 'wiki-twn-mainpage', 'wiki-betawiki' ) ) ) {
								$products['MediaWiki extensions used by Wikimedia']++;
							} elseif ( $group === 'out-jquery-uls' ) {
								$products['jQuery.ULS']++;
							} else {
								$products['translatewiki.net']++;
							}
						} elseif ( in_array( $row->rc_namespace, array( 1198 ) ) ) {
							$products['translatewiki.net']++;
						} elseif ( in_array( $row->rc_namespace, array( 1238 ) ) ) {
							$products['Pywikibot']++;
						} elseif ( in_array( $row->rc_namespace, array( 1240 ) ) ) {
							$products['Intuition']++;
						} elseif ( in_array( $row->rc_namespace, array( 1244 ) ) ) {
							$products['Kiwix']++;
						} elseif ( in_array( $row->rc_namespace, array( 1248 ) ) ) {
							$products['Huggle']++;
						} elseif ( in_array( $row->rc_namespace, array( 1252 ) ) ) {
							$products['VicuñaUploader']++;
						} elseif ( $group === 'out-wikiblame' ) {
								$products['WikiBlame']++;
						} else {
							$products['Wikimedia Mobile apps']++;
						}

						$messageData = TranslateUtils::figureMessage( $row->rc_title );
						// Language totals
						if( isset( $languages[$messageData[1]] ) ) {
							$languages[$messageData[1]]++;
						} else {
							$languages[$messageData[1]] = 1;
						}
						// Per user per language totals
						if( isset( $userLangs[$row->rc_user_text][$messageData[1]] ) ) {
							$userLangs[$row->rc_user_text][$messageData[1]]++;
						} else {
							$userLangs[$row->rc_user_text][$messageData[1]] = 1;
						}
					}
				}

				arsort( $data );
				arsort( $products );
				arsort( $languages );
				array_reverse( $languages );

				$out = "Per user statistics:\n";
				foreach ( $data as $user => $count ) {
					if ( $count < 0 ) {
						continue;
					}

					$userCounts = $userLangs[$user];
					$langCounts = array();
					foreach ( $userCounts as $langCode => $langCount ) {
						$langCounts[] = "$langCode:$langCount";
					}
					$langCounts = '(' . implode( ',', $langCounts ) . ')';

					$out .= "# [[Special:Rally500/$user|$user]] $count $langCounts\n";
				}

				$output->addWikitext( $out );
				$output->addHtml( '<hr />' );

				$totalContribs = 0;
				$out = "Per product statistics:\n";
				ksort( $products );
				foreach ( $products as $product => $contribs ) {
					$out .= "* $product: $contribs\n";
					$totalContribs += $contribs;
				}
				$out .= "* Total: $totalContribs\n";
				$output->addWikitext( $out );
				$output->addHtml( '<hr />' );

				$languageNames = Language::getTranslatedLanguageNames( 'en' );
				$out = "Per language statistics:\n";
				foreach ( $languages as $language => $contribs ) {
					$enLanguage = $languageNames[$language];
					$out .= "# $enLanguage ($language): $contribs\n";
				}
				$output->addWikitext( $out );
				$output->addHtml( '<hr />' );
			}

			if ( $par && $allowed ) {
				$user = User::newFromName( $par );
			} else {
				$user = $currentUser;
			}

			$tables = array( 'recentchanges', 'page' );
			$fields = array( 'rc_title', 'rc_namespace', 'rc_this_oldid', 'page_latest' );
			$conds = array(
				"rc_timestamp >= 20150517000000",
				"rc_timestamp <= 20150525235959",
				'rc_new' => 1,
				'rc_bot' => 0,
				'rc_namespace IN ( 8, 1198, 1206, 1238, 1240, 1244, 1248, 1252 )',
				'rc_user' => $user->getId(),
				'rc_cur_id = page_id',
			);

			$res = $dbr->select( $tables, $fields, $conds, __METHOD__ );

			$total = 0;
			$output->addHtml( "The following pages qualify for user {$user->getName()}: <ul>");
			$userName = $user->getName();

			foreach ( $res as $_ ) {
				/*
				// This user can only make qualifying edits in "si".
				if ( $userName == 'Blah' &&
					!strrpos( $_->rc_title, 'si', -2 )
				) {
					continue;
				}

				// Disqualify a user.
				if( $userName == 'Blah' ) {
					continue;
				}
				*/

				$title = preg_replace( '~/[a-z-]+$~i', '', $_->rc_title );
				$group = TranslateUtils::messageKeyToGroup( $_->rc_namespace, $title );
				if ( !isset($allowedGroups[$group]) ) {
					$output->addHtml(
						'<li><s>' .
							Linker::linkKnown( Title::makeTitle( $_->rc_namespace, $_->rc_title ) ) .
							"</s> $group</li>"
					);
					continue;
				}

				$nonlatest = $_->rc_this_oldid != $_->page_latest;

				$total++;
				if ( $total > 501 ) {
					continue;
				}
				if ( $total == 501 ) {
					$output->addHtml(
						"<li>Translations over 500 are omitted.</li>");
					continue;
				}

				$output->addHtml(
					'<li>' . ( $nonlatest ? '<i>' : '' ) .
						Linker::linkKnown( Title::makeTitle( $_->rc_namespace, $_->rc_title ) ) .
						" $group " . ( $nonlatest ? '</i>' : '' ) . "</li>"
				);
			}

			$output->addHtml( "</ul> Total $total pages" );
			if ( $total >= 500 ) {
				$output->addHtml( "<br /><b>Qualified!</b>" );
			} else {
				$output->addHtml( "<br /><b>Not qualified!</b>" );
			}

			$output->addHtml( '</div>' ); # end of <div dir="ltr">
		}
	}
}
