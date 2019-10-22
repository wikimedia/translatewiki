<?php

/**
 * @author Siebrand Mazeland
 * @author Justin Du
 * @license GPL-2.0-or-later
 */
class FudForumSuggester implements InsertablesSuggester {
	public function getInsertables( $text ) {
		$insertables = [];

		// Variables like {VAR: <string>}
		$matches = [];
		preg_match_all(
			'/{VAR: [^}]+}/',
			$text,
			$matches,
			PREG_SET_ORDER
		);
		$new = array_map( function ( $match ) {
			return new Insertable( "{$match[0]}", $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		return $insertables;
	}
}
