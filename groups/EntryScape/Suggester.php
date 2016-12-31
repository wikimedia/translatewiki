<?php
/**
 * @file
 * @author Niklas Laxström
 * @author Justin Du
 * @license GPL-2.0+
 */

class EntryScapeInsertablesSuggester implements InsertablesSuggester {
	public function getInsertables( $text ) {
		$insertables = [];

		// ${app}, {user}, %s
		$matches = [];
		preg_match_all(
			'/\$?{[a-z]+}|%s/',
			$text,
			$matches,
			PREG_SET_ORDER
		);
		$new = array_map( function( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		// {{PLURAL}} (case insensitive)
		$matches = [];
		preg_match_all(
			'/({{((?:PLURAL):[^|]*)\|).*?(}})/i',
			$text,
			$matches,
			PREG_SET_ORDER
		);
		$new = array_map( function( $match ) {
			return new Insertable( $match[2], $match[1], $match[3] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		// &nbsp;
		$matches = [];
		preg_match_all(
			'/&(?:[a-z]+|#\d+);/',
			$text,
			$matches,
			PREG_SET_ORDER
		);
		$new = array_map( function( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		return $insertables;
	}
}
