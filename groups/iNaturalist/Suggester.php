<?php

/**
 * @author Niklas Laxström
 * @license GPL-2.0-or-later
 */
class INaturalistSuggester implements InsertablesSuggester {
	public function getInsertables( $text ) {
		$insertables = [];

		// Variables and html entities
		$matches = [];
		preg_match_all( '/%{[a-z_]+}|&[a-z]+;/', $text, $matches, PREG_SET_ORDER );
		$new = array_map( function( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		return $insertables;
	}
}
