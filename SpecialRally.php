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
				'core', // 8
				'ext-0-all', // 8
				'out-jquery-uls', // 1206
				'out-kiwix', // 1244
				'out-okawix-0-all', // 1220
				'out-pywikipedia-0-all', // 1238
				'out-wikiblame', // 1206
				'out-wikimedia-mobile-0-all', // 1206
				'tsint-0-all' // 1240
			);

			$allowedGroups = MessageGroups::expandWildcards( '*' );

			$allGroups = MessageGroups::singleton()->getGroups();
			foreach ( $allGroups as $groupId => $messageGroup ) {
				if ( in_array( $groupId, $allowedGroups )  ) {
					if( $messageGroup->isMeta() ) {
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
					'MediaWiki' => 0,
					'MediaWiki extensions' => 0,
					'Mobile' => 0,
					'Offline' => 0,
					'Other' => 0
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
					"rc_timestamp >= 20140806100000",
					"rc_timestamp <= 20140810235959",
					'rc_new' => 1,
					'rc_bot' => 0,
					'rc_namespace IN (8, 1206, 1212, 1220, 1224, 1238, 1240, 1244)',
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

						if ( $row->rc_namespace == 8 ) {
							if( in_array( $group, array( 'core' ) ) ) {
								$products['MediaWiki']++;
							} else {
								$products['MediaWiki extensions']++;
							}
						} elseif ( in_array( $row->rc_namespace, array( 1212, 1220, 1224, 1244 ) ) ) {
							$products['Offline']++;
						} elseif ( in_array( $row->rc_namespace, array( 1206 ) ) &&
							!in_array( $group, array( 'out-jquery-uls' ) ) ) {
								$products['Mobile']++;
						} else {
							$products['Other']++;
						}

						$messageData = TranslateUtils::figureMessage( $row->rc_title );
						// Language totals
						if( isset(  $languages[$messageData[1]] ) ) {
							$languages[$messageData[1]]++;
						} else {
							$languages[$messageData[1]] = 1;
						}
						// Per user per language totals
						if( isset(  $userLangs[$row->rc_user_text][$messageData[1]] ) ) {
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
				"rc_timestamp >= 20121221220000",
				"rc_timestamp <= 20121231235959",
				'rc_new' => 1,
				'rc_bot' => 0,
				'rc_namespace IN (8, 1206, 1212, 1220, 1224, 1238, 1240, 1244)',
				'rc_user' => $user->getId(),
				'rc_cur_id = page_id',
			);

			$res = $dbr->select( $tables, $fields, $conds, __METHOD__ );

			$total = 0;
			$output->addHtml( "The following pages qualify for user {$user->getName()}: <ul>");
			$userName = $user->getName();

			foreach ( $res as $_ ) {
				// This user can only make qualifying edits in "si".
				if ( $userName == 'පසිඳු කාවින්ද' &&
					!strrpos( $_->rc_title, 'si', -2 )
				) {
					continue;
				}

				if( $userName == 'Zanatos' ) {
					continue;
				}

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
