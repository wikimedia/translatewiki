<?php
/**
 * Insertables for CitationHunt
 */
class CitationHuntInsertablesSuggester {
	public function getInsertables( $text ) {
		$matches = [];
		// %s
		preg_match_all( '/%s/', $text, $matches, PREG_SET_ORDER );
		$insertables = array_map( function ( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );

		return $insertables;
	}
}
