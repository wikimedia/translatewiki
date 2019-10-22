<?php
/**
 * Insertables for crosswatch
 */
class CrosswatchInsertablesSuggester {
	public function getInsertables( $text ) {
		$matches = [];
		// <user name={{title}}>, <user>, </a>, {{number}}
		preg_match_all( '/<\/?[a-zA-Z\=\{\}\s_\|]+>|{{[a-zA-Z_\|]+}}/', $text, $matches, PREG_SET_ORDER );
		$insertables = array_map( function ( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );

		return $insertables;
	}
}
