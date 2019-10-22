<?php

/**
 * @author Niklas Laxström
 * @license GPL-2.0-or-later
 */
class MifosSuggester implements InsertablesSuggester {
	public function getInsertables( $text ) {
		$insertables = [];

		// Variables of type {{params[0].value}}
		$matches = [];
		preg_match_all( '/{{params\[[0-9]\]\.value}}/', $text, $matches, PREG_SET_ORDER );
		$new = array_map( function ( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		return $insertables;
	}
}
