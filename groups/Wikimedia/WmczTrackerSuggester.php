<?php
class WmczTrackerSuggester implements InsertablesSuggester {
	public function getInsertables( $text ) {
		$insertables = [];

		// Python formatted strings
		$matches = [];
		preg_match_all( '/%(\([a-zA-Z_]+\))?[a-z]/', $text, $matches, PREG_SET_ORDER );
		$new = array_map( function( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		return $insertables;
	}
}
