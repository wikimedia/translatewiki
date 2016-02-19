<?php

/**
 * @author Siebrand Mazeland
 * @license GPL-2.0+
 */
class FudForumSuggester implements InsertablesSuggester {
	public function getInsertables( $text ) {
		$insertables = [];

		// Variables like {VAR: <string>}
		$matches = [];
		preg_match_all( '/{VAR: [^}]+}/', $text, $matches, PREG_SET_ORDER );
		$new = array_map( function( $match ) {
			return new Insertable( "{$match[1]}{$match[2]}", $match[1], $match[2] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		return $insertables;
	}
}
