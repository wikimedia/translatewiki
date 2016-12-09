<?php
/**
 * @file
 * @author Justin Du
 * @license GPL-2.0+
 */

class NoccInsertablesSuggester {
	public function getInsertables( $text ) {
		$insertables = [];

		// %1$s
		$matches = [];
		preg_match_all(
			'/%\d\$s/',
			$text,
			$matches,
			PREG_SET_ORDER
		);
		$new = array_map( function( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		return $insertables;
	}
}
