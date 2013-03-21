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
		return $this->msg( 'twnmp-mainpage' );
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
			, 'English' );
		$out .= Html::element( 'a',
			array(
				'class' => 'login column text-right',
			)
			, 'Login' );
		$out .= Html::closeElement( 'div' );
		return $out;
	}

	public function searchBar() {
		$out = Html::openElement( 'div', array( 'class' => 'row twn-mainpage-search' ) );
		$out .= Html::element( 'span',
			array(
				'class' => 'search-label',
			) );
		$out .= Html::element( 'input',
			array(
				'class' => 'ten columns searchbox',
				'placeholder' => 'Find and fix translations'
			) );

		$out .= Html::element( 'button',
			array(
				'class' => 'one column end blue button',
			),
			'Find' );
		$out .= Html::closeElement( 'div' );
		return $out;
	}

	public function projectSelector() {
		$out = Html::element( 'div', array( 'class' => 'row twn-mainpage-project-selector-title' ),
			'Choose a project to translate' );
		$out .= Html::openElement( 'div', array( 'class' => 'row twn-mainpage-project-tiles' ) );
		$out .= Html::openElement( 'div', array( 'class' => 'three columns twn-mainpage-project-tile' ) );
		$out .= Html::closeElement( 'div' );
		$out .= Html::openElement( 'div', array( 'class' => 'three columns twn-mainpage-project-tile' ) );
		$out .= Html::closeElement( 'div' );
		$out .= Html::openElement( 'div', array( 'class' => 'three columns twn-mainpage-project-tile' ) );
		$out .= Html::closeElement( 'div' );
		$out .= Html::openElement( 'div', array( 'class' => 'three columns twn-mainpage-project-tile' ) );
		$out .= Html::closeElement( 'div' );
		$out .= Html::closeElement( 'div' );
		return $out;
	}

	public function banner() {
		$out = Html::openElement( 'div', array( 'class' => 'row twn-mainpage-banner' ) );
		$out .= $this->twnStats();
		$out .= $this->loginForm();
		$out .= Html::closeElement( 'div' );
		return $out;
	}

	public function footer() {
		$out = Html::openElement( 'div', array( 'class' => 'row twn-mainpage-footer' ) );
		$out .= Html::element( 'a', array( 'class' => 'three column' ), 'About' );
		$out .= Html::element( 'a', array( 'class' => 'three column' ), 'Languages supported' );
		$out .= Html::element( 'a', array( 'class' => 'three column' ), 'Special pages' );
		$out .= Html::element( 'a', array( 'class' => 'three column' ), 'Help' );
		$out .= Html::closeElement( 'div' );
		return $out;
	}

	public function twnStats() {
		$out = Html::openElement( 'div', array( 'class' => 'six columns twn-mainpage-stats-tiles' ) );
		$out .= Html::closeElement( 'div' );
		return $out;
	}

	public function loginForm() {
		$out = Html::openElement( 'div', array( 'class' => 'five columns twn-mainpage-loginform' ) );
		$out .= Html::closeElement( 'div' );
		return $out;
	}
}
