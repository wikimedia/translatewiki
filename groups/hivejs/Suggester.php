<?php
/**
 * @file
 * @author Niklas Laxström
 * @license GPL-2.0+
 */

class HivejsInsertablesSuggester {
	public function getInsertables( $text ) {
		$matches = array();
		preg_match_all( '/{[a-z_]+}/', $text, $matches, PREG_SET_ORDER );
		$insertables = array_map( function( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );

		return $insertables;
	}
}
