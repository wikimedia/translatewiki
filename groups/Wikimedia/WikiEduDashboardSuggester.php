<?php
/**
 * @file
 * @author Niklas Laxström
 * @license GPL-2.0-or-later
 */

class WikiEduDashboardInsertablesSuggester {
	public function getInsertables( $text ) {
		$insertables = [];

		// %{title}
		$matches = [];
		preg_match_all( '/\%{[^}]+}|%s/', $text, $matches, PREG_SET_ORDER );
		$new = array_map( function ( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		// &nbsp;
		$matches = [];
		preg_match_all( '/&(?:[a-z]+|#\d+);/', $text, $matches, PREG_SET_ORDER );
		$new = array_map( function ( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		return $insertables;
	}
}
