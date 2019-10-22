<?php

/**
 * @author Siebrand Mazeland
 * @license GPL-2.0-or-later
 */
class EtherpadLiteSuggester implements InsertablesSuggester {
	public function getInsertables( $text ) {
		$insertables = [];

		// Variables like {{variable}}
		$matches = [];
		preg_match_all( '/{{[a-zA-Z_]+}}/', $text, $matches, PREG_SET_ORDER );
		$new = array_map( function ( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		return $insertables;
	}
}
