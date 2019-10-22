<?php
/**
 * @file
 * @author Niklas Laxström
 * @license GPL-2.0-or-later
 */

class WikimediaMobileAndroidInsertablesSuggester {
	public function getInsertables( $text ) {
		// %s, %2$s %.2f
		$matches = [];
		preg_match_all( '/%(\d+\$|\.\d+)?[sduf]/U', $text, $matches, PREG_SET_ORDER );
		$insertables = array_map( function ( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );

		return $insertables;
	}
}
