<?php
/**
 * @file
 * @author Niklas Laxström
 * @license GPL-2.0-or-later
 */

class PywikibotInsertablesSuggester {
	public function getInsertables( $text ) {
		// %(title)s
		$matches = [];
		preg_match_all( '/\%\([^)]+\)[diouxXeEfFgGcrs]/U', $text, $matches, PREG_SET_ORDER );
		$insertables = array_map( function ( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );

		return $insertables;
	}
}
