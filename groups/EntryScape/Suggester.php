<?php
/**
 * @file
 * @author Niklas Laxström
 * @license GPL-2.0+
 */

class EntryScapeInsertablesSuggester {
	public function getInsertables( $text ) {
		$insertables = array();

		// ${app}, {user}, %s, NOT {{PLURAL}}
		$matches = array();
		preg_match_all( '/\$?{[a-z]+}|%s/', $text, $matches, PREG_SET_ORDER );
		$new = array_map( function( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		// &nbsp;
		$matches = array();
		preg_match_all( '/&(?:[a-z]+|#\d+);/', $text, $matches, PREG_SET_ORDER );
		$new = array_map( function( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		return $insertables;
	}
}
