<?php
/**
 * Similar to Nocc/NoccInsertablesSuggester.php
 *
 * @file
 * @author Justin Du
 * @license GPL-2.0-or-later
 */

class MantisBTInsertablesSuggester {
	public function getInsertables( $text ) {
		$insertables = [];

		// %1$s, %s
		$matches = [];
		preg_match_all(
			'/%\d\$s|%s/',
			$text,
			$matches,
			PREG_SET_ORDER
		);
		$new = array_map( function ( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		return $insertables;
	}
}
