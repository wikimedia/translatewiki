<?php
/**
 * Insertables for CopyPatrol
 */
class CopyPatrolInsertablesSuggester {
	public function getInsertables( $text ) {
		$matches = [];

		preg_match_all( '/\$[0-9]+/', $text, $matches, PREG_SET_ORDER );
		$insertables = array_map( function( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );

		return $insertables;
	}
}
