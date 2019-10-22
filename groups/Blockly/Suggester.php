<?php
/**
 * @file
 * @author Niklas Laxström
 * @author Siebrand Mazeland
 * @license GPL-2.0-or-later
 */

class BlocklySuggester {
	public function getInsertables( $text ) {
		$insertables = [];

		$matches = [];
		// %1, %2, .., %9
		preg_match_all( '/%[1-9]+/', $text, $matches, PREG_SET_ORDER );
		$new = array_map( function ( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		return $insertables;
	}
}
