<?php
/**
 * Insertables for video2commons
 */
class Video2commonsInsertablesSuggester {
	public function getInsertables( $text ) {
		$matches = [];
		// {{#a}} and {{/a}}
		preg_match_all( '/\{\{[\/#]a\}\}/', $text, $matches, PREG_SET_ORDER );
		$insertables = array_map( function ( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );

		return $insertables;
	}
}
