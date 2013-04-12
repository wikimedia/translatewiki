<?php
/**
 * Special:MainPage special page.
 *
 * @file
 * @author Niklas Laxström
 * @author Santhosh Thottingal
 * @license GPL2+
 */

/**
 * Provides the main page with stats and stuff.
 *
 * @ingroup SpecialPage
 */
class SpecialTwnMainPage extends SpecialPage {
	function __construct() {
		parent::__construct( 'TwnMainPage' );
	}

	public function getDescription() {
		return $this->msg( 'twnmp-mainpage' )->text();
	}

	public function execute( $parameters ) {
		$this->setHeaders();

		$out = $this->getOutput();
		$out->setArticleBodyOnly( true );
		$out->addModules( 'ext.translate.mainpage' );
		$out->addHtml( $out->headElement( $this->getSkin() ) );
		$out->addHTML( Html::openElement( 'div', array(
			'class' => 'grid twn-mainpage',
		) ) );
		$out->addHTML( $this->header() );
		$out->addHTML( $this->banner() );
		$out->addHTML( $this->searchBar() );
		$out->addHTML( $this->projectSelector() );
		$out->addHTML( $this->footer() );
		$out->addHtml( '</body></html>' );
	}

	public function header() {
		$out = Html::openElement( 'div', array( 'class' => 'row twn-mainpage-header' ) );
		$out .= Html::openElement( 'div', array( 'class' => 'ten columns twn-mainpage-title' ) );
		$out .= Html::element( 'div',
			array(
				'class' => 'twn-brand-name',
			)
			, 'translatewiki.net' );
		$out .= Html::element( 'div',
			array(
				'class' => 'twn-brand-motto',
			)
			, 'Opensource translation community' );
		$out .= Html::closeElement( 'div' );
		$out .= Html::element( 'span',
			array(
				'class' => 'uls-trigger column',
			)
			, Language::fetchLanguageName( $this->getLanguage()->getCode() ) );
		if ( $this->getUser()->isLoggedIn() ) {
			$out .= Html::element( 'a',
				array(
					'class' => 'login username column text-right',
					'href' => Title::makeTitle( NS_USER, $this->getUser()->getName() )->getLocalUrl(),
				)
				, $this->getUser()->getName() );
		} else {
			$out .= Html::element( 'a',
				array(
					'class' => 'login column text-right',
					'href' => SpecialPage::getTitleFor( 'Userlogin' )
						->getLocalUrl( array( 'returnto' => 'Special:MainPage' ) ),
				)
				, 'Login' );
		}
		$out .= Html::closeElement( 'div' );

		return $out;
	}

	public function searchBar() {
		$out = Html::openElement( 'form',
			array(
				'class' => 'row twn-mainpage-search',
				'action' => SpecialPage::getTitleFor( 'SearchTranslations' )->getLocalUrl(),
			) );
		$out .= Html::element( 'input',
			array(
				'class' => 'ten columns searchbox',
				'placeholder' => 'Find and fix translations',
				'type' => 'search',
				'name' => 'query',
			) );

		$out .= Html::element( 'button',
			array(
				'class' => 'columns end blue button',
				'type' => 'submit',
			),
			'Find' );
		$out .= Html::closeElement( 'form' );

		return $out;
	}

	public function projectSelector() {
		$out = Html::element( 'div', array( 'class' => 'row twn-mainpage-project-selector-title' ),
			'Choose a project to translate' );
		$out .= Html::openElement( 'div', array( 'class' => 'row twn-mainpage-project-tiles' ) );

		$projects = ProjectHandler::getProjects();
		ProjectHandler::sortByPriority( $projects, $this->getLanguage()->getCode() );

		$tiles = array();

		foreach ( $projects as $group ) {
			$tiles[] = $this->makeGroupTile( $group );
			if ( count( $tiles ) === 8 ) {
				break;
			}
		}


		$out .= implode( "\n\n", $tiles );
		$out .= Html::closeElement( 'div' );

		return $out;
	}

	protected function makeGroupTile( MessageGroup $group ) {
		$urls = TranslateUtils::getIcon( $group, 100 );
		if ( isset( $urls['vector'] ) ) {
			$url = $urls['vector'];
		} elseif ( isset( $urls['raster'] ) ) {
			$url = $urls['raster'];
		} else {
			$url = '';
		}

		$uiLanguage = $this->getLanguage();
		$stats = MessageGroupStats::forItem( $group->getId(),$uiLanguage->getCode() );
		$statsbar = StatsBar::getNew( $group->getId(), $uiLanguage->getCode(), $stats );

		$translated = $stats[MessageGroupStats::TRANSLATED];
		$proofread = $stats[MessageGroupStats::PROOFREAD];
		if ( $stats[MessageGroupStats::TOTAL] ) {
			$translated = round( 100 * $translated / $stats[MessageGroupStats::TOTAL] );
			$proofread = round( 100 * $proofread / $stats[MessageGroupStats::TOTAL] );
		}

		$image = Html::element( 'img', array( 'src' => $url, 'width' => '100' ) );
		$label = htmlspecialchars( $group->getLabel( $this->getContext() ) );
		$stats = $statsbar->getHtml( $this->getContext() );
		$acts = "$translated% $proofread%";

		$title = SpecialPage::getTitleFor( 'Translate' );
		$translate = Html::element( 'a', array(
				'href' => $title->getLocalUrl( array( 'group' => $group->getId() ) )
			), 'Translate' );

		$proofread = Html::element( 'a', array(
				'href' => $title->getLocalUrl( array( 'group' => $group->getId(), 'action' => 'proofread' ) )
			), 'Proofread' );

		$out = <<<HTML
<div class="three columns twn-mainpage-project-tile">
	<div class="project-tile">
		<div class="row project-top">
			<div class="project-icon four columns">$image</div>
			<div class="project-content eight columns">
				<div class="row project-name">$label</div>
				<div class="row project-statsbar">$stats</div>
				<div class="row project-stats">$acts</div>
			</div>
		</div>
		<div class="row project-actions hide">
			<div class="six columns action">$translate</div>
			<div class="six columns action">$proofread</div>
		</div>
	</div>
</div>
HTML;

		return $out;
	}

	public function banner() {
		global $wgMainPageImages;

		$image = array();
		$images = array_values( $wgMainPageImages );
		$imageIndex = date( 'z' ) % count( $images );
		if ( isset( $images[$imageIndex] ) ) {
			$image = $images[$imageIndex];
		}

		$bannerAttribs = array( 'class' => 'row twn-mainpage-banner' );
		if ( isset( $image['url'] ) ) {
			$url = $image['url'];
			$bannerAttribs['style'] = "background-image: url($url);";
		}

		$out = Html::openElement( 'div', $bannerAttribs );
		$out .= $this->twnStats();
		$out .= $this->userWidget();

		if ( isset( $image['attribution'] ) ) {
			$out .= Html::rawElement( 'div', array( 'class' => 'banner-attribution' ),
				$this->msg( 'twnmp-bannerwho', $image['attribution'] )->plain()
			);
		}

		$out .= Html::closeElement( 'div' );

		return $out;
	}

	public function footer() {
		$out = Html::openElement( 'div', array( 'class' => 'row twn-mainpage-footer' ) );
		$out .= Html::element( 'a', array(
			'class' => 'three column',
			'href' =>Title::newFromText( 'Special:MyLanguage/Project:About' )->getLocalUrl(),
		), 'About' );
		$out .= Html::element( 'a', array(
			'class' => 'three column',
			'href' => SpecialPage::getTitleFor( 'SupportedLanguages' )->getLocalUrl(),
		), 'Languages supported' );
		$out .= Html::element( 'a', array(
			'class' => 'three column',
			'href' => SpecialPage::getTitleFor( 'Specialpages' )->getLocalUrl(),
		), 'Special pages' );
		$out .= Html::element( 'a', array(
			'class' => 'three column',
			'href' => Title::newFromText( 'Translating:Index' )->getLocalUrl(),
		), 'Help' );
		$out .= Html::closeElement( 'div' );

		return $out;
	}

	public static function numberOfLanguages( $period ) {
		global $wgTranslateMessageNamespaces;

		$dbr = wfGetDB( DB_SLAVE );
		$tables = array( 'recentchanges' );
		$fields = array( 'substring_index(rc_title, \'/\', -1) as lang, count(rc_id) as count' );
		$conds = array(
			'rc_title' . $dbr->buildLike( $dbr->anyString(), '/', $dbr->anyString() ),
			'rc_namespace' => $wgTranslateMessageNamespaces,
			'rc_timestamp > ' . $dbr->timestamp( TS_DB, wfTimeStamp( TS_UNIX ) - 60 * 60 * 24 * $period ),
			'rc_bot' => 0,
		);
		$options = array( 'GROUP BY' => 'lang', 'HAVING' => 'count > 20' );

		$res = $dbr->select( $tables, $fields, $conds, __METHOD__, $options );

		$count = 0;
		foreach ( $res as $row ) {
			if ( Language::isKnownLanguageTag( $row->lang ) ) {
				$count++;
			}
		}

		return $count;
	}

	// Callback for CachedStat
	public static function getTwnStats() {
		$projects = count( ProjectHandler::getProjects() );
		$translators = SiteStats::numberingroup( 'translator' );
		$messages = count( MessageIndex::singleton()->retrieve() );
		$languages = self::numberOfLanguages( 30 );

		return array(
			'projects' => $projects,
			'translators' => $translators,
			'messages' => $messages,
			'languages' => $languages,
		);
	}

	// Callback for CachedStat
	public static function getUserStats( $code, $period ) {
		return array(
			'translators' => UserStats::getTranslationRankings( $code, $period ),
			'proofreaders' => UserStats::getProofreadRankings( $code, $period ),
		);
	}

	public function twnStats() {
		$stale = 60 * 60 * 6;
		$expired = 60 * 60 * 24;
		$cacher = new CachedStat( 'twnstats', $stale, $expired,
			array( 'SpecialTwnMainPage::getTwnStats' ) );
		$stats = $cacher->get();

		// Rows x columns
		$data = array(
			array(
				'twnmp-s-projects' => $stats['projects'],
				'twnmp-s-translators' => $stats['translators'],
				'twnmp-s-messages' => $stats['messages'],
			),
			array(
				null,
				null,
				'twnmp-s-languages' => $stats['languages'],
			)
		);

		$out = '';
		$out .= '<div class="six columns twn-mainpage-stats-tiles">';

		$lang = $this->getLanguage();

		foreach ( $data as $rows ) {
			$out .= '<div class="row stats-tile-row">';
			foreach ( $rows as $column => $value ) {
				if ( $value === null ) {
					$out .= <<<HTML
<div class="four columns">
	<div class="stats-tile unused"></div>
</div>
HTML;
					continue;
				}

				if ( $value > 1000 ) {
					$digits = 3 - ceil( log( $value, 100 ) );
					$value = number_format( $value / 1000, $digits );
					$value = $lang->formatNum( $value ) . 'k';
				} else {
					$value = $lang->formatNum( $value );
				}

				$value = htmlspecialchars( $value );
				$text = $this->msg( $column )->escaped();

				$out .= <<<HTML
<div class="four columns">
	<div class=stats-tile>
		<div class=stats-number>$value</div>
		<div class=stats-text>$text</div>
	</div>
</div>
HTML;
			}

			$out .= '</div>';
		}

		$out .= '</div>';

		return $out;
	}

	public function userWidget() {
		if ( $this->getUser()->isLoggedIn() ) {
			return $this->userStats();
		} else {
			return $this->loginForm();
		}
	}

	public function loginForm() {
		$languageCode = $this->getLanguage()->getCode();
		$languageName = TranslateUtils::getLanguageName( $languageCode );

		// Shortcut for creating row elements
		$row = array( 'class' => 'row' );

		$out = Html::openElement( 'form',
			array( 'class' => 'five columns offset-by-one main-widget login-widget',
				'method' => 'post',
				'action' => SpecialPage::getTitleFor( 'Userlogin' )
					->getLocalUrl( array(
						'returnto' => 'Special:MainPage',
						'type' => 'signup' ) ),
			) );
		$out .= Html::element( 'h1', $row, 'Become a translator' );
		$out .= Html::element( 'h2', $row, 'Choose languages you know' );
		$out .= Xml::checkLabel( $languageName, 'wpLanguage1', 'wpLanguage1', true );
		$out .= Html::openElement( 'div', $row );
		$out .= Html::element( 'div', array(
			'class' => 'eight columns offset-by-one signup-language-selector'
		), 'Choose another language...' );
		$out .= Html::closeElement( 'div' );
		$out .= Html::element( 'h2', $row, 'Fill in your account details' );
		$out .= Html::openElement( 'div', $row );
		$out .= Html::element( 'input', array(
			'class' => 'eleven columns',
			'name' => 'wpName',
			'placeholder' => 'Username',
		) );
		$out .= Html::closeElement( 'div' );

		$out .= Html::openElement( 'div', $row );
		$out .= Html::element( 'input', array(
			'class' => 'eleven columns',
			'name' => 'wpPassword',
			'type' => 'password',
			'placeholder' => 'Password',
		) );
		$out .= Html::closeElement( 'div' );

		$out .= Html::openElement( 'div', $row );
		$out .= Html::element( 'input', array(
			'class' => 'eleven columns',
			'name' => 'wpEmail',
			'type' => 'email',
			'placeholder' => 'Email',
		) );
		$out .= Html::closeElement( 'div' );

		$out .= Html::openElement( 'div', $row );
		$out .= Html::element( 'button', array(
			'class' => 'six columns green button offset-by-three',
		), 'Create account' );
		$out .= Html::closeElement( 'div' );

		$out .= Html::closeElement( 'form' );

		return $out;
	}

	public function userStats() {
		$languageCode = $this->getLanguage()->getCode();
		$languageName = TranslateUtils::getLanguageName( $languageCode );

		$stale = 60 * 5;
		$expired = 60 * 60 * 12;
		$cacher = new CachedStat( "userstats-$languageCode", $stale, $expired,
			array( 'SpecialTwnMainPage::getUserStats', $languageCode, 30 )
		);
		$statsArray = $cacher->get();

		$out = Html::openElement( 'div', array( 'class' => 'five columns main-widget stats-widget' ) );

		$out .= Html::openElement( 'div', array( 'class' => 'row user-stats-title' ) );
		$out .= Html::element( 'h2', array(), 'Your translation statistics' );
		$out .= Html::element( 'div', array(), $languageName );
		$out .= Html::closeElement( 'div' );

		$myuser = $this->getUser()->getName();

		$out .= Html::openElement( 'div', array( 'class' => 'row ranking' ) );
		$out .= Html::openElement( 'div', array( 'class' => 'row eight columns' ) );
		$stats = $statsArray['translators'];
		$i = 1;
		$translators = count( $stats );
		foreach ( $stats as $user => $count ) {
			if ( $user === $myuser ) {
				$out .= Html::element( 'div', array( 'class' => 'count' ), $count );
				$out .= Html::element( 'div', array( 'class' => 'count-description' ), 'Translations/month' );
				$out .= Html::element( 'div', array( 'class' => 'rank-description' ), "Ranked $i of $translators translators" );
				$out .= Html::element( 'div', array( 'class' => 'language-description' ), "in $languageName" );
				break;
			}
			$i++;
		}
		$out .= Html::closeElement( 'div' );
		$out .= Html::openElement( 'div', array( 'class' => 'four columns' ) );
		$out .= Html::element( 'button', array( 'class' => 'button green' ), 'Translate' );
		$out .= Html::closeElement( 'div' );
		$out .= Html::closeElement( 'div' );

		$out .= Html::openElement( 'div', array( 'class' => 'row ranking' ) );
		$out .= Html::openElement( 'div', array( 'class' => 'row eight columns' ) );
		$stats = $statsArray['proofreaders'];
		$i = 1;
		$translators = count( $stats );
		foreach ( $stats as $user => $count ) {
			if ( $user === $myuser ) {
				$out .= Html::element( 'div', array( 'class' => 'count' ), $count );
				$out .= Html::element( 'div', array( 'class' => 'count-description' ), 'Revisions/month' );
				$out .= Html::element( 'div', array( 'class' => 'rank-description' ), "Ranked $i of $translators translators" );
				$out .= Html::element( 'div', array( 'class' => 'language-description' ), "in $languageName" );
				break;
			}
			$i++;
		}
		$out .= Html::closeElement( 'div' );
		$out .= Html::openElement( 'div', array( 'class' => 'four columns' ) );
		$out .= Html::element( 'button', array( 'class' => 'button green' ), 'Proofread' );
		$out .= Html::closeElement( 'div' );
		$out .= Html::closeElement( 'div' );

		$out .= Html::openElement( 'div', array( 'class' => 'row langstats-link-row' ) );
		$out .= Html::element( 'a', array(
			'class' => 'twelve columns langstats-link',
			'href' => SpecialPage::getTitleFor( 'LanguageStats' )->getLocalUrl(),
		), 'View language statistics' );
		$out .= Html::closeElement( 'div' );
		$out .= Html::closeElement( 'div' );

		return $out;
	}
}
