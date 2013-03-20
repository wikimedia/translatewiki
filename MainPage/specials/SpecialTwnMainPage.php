<?php
/**
 * Special:MainPage special page.
 *
 * @file
 * @author Niklas Laxström
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

	public function execute( $parameters ) {
		$this->setHeaders();
		$this->getOutput()->addWikiText( "Hei maailma!" );
	}

	public function getDescription() {
		return $this->msg( 'twnmp-mainpage' );
	}
}
