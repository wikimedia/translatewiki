<?php
/**
 * Similar to Wikimedia/CitationHuntInsertablesSuggester.php
 *
 * @file
 * @author Justin Du
 * @license GPL-2.0-or-later
 */

class LibReviewsInsertablesSuggester {
	public function getInsertables( $text ) {
		$insertables = [];

		// %s, %1$s
		$matches = [];
		preg_match_all(
			'/\%s|%1\$s/',
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
