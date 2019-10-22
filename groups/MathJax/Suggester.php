<?php
/**
 * @file
 * @author Niklas Laxström
 * @license GPL-2.0-or-later
 */

class MathJaxInsertablesSuggester {
	public function getInsertables( $text ) {
		$insertables = [];

		$matches = [];
		preg_match_all( '/\$[0-9]+/', $text, $matches, PREG_SET_ORDER );
		$new = array_map( function ( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		// \left \begin{$1}
		$matches = [];
		preg_match_all( '/\\\\[a-z0-9${}]+/', $text, $matches, PREG_SET_ORDER );
		$new = array_map( function ( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		// <math>
		$matches = [];
		preg_match_all( '/<[a-z]+>/', $text, $matches, PREG_SET_ORDER );
		$new = array_map( function ( $match ) {
			return new Insertable( $match[0], $match[0] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		// [title]{$1}
		$matches = [];
		preg_match_all( '/(\[)[^]]+(\]\([^)]+\))/', $text, $matches, PREG_SET_ORDER );
		$new = array_map( function ( $match ) {
			return new Insertable( "{$match[1]} {$match[2]}", $match[1], $match[2] );
		}, $matches );
		$insertables = array_merge( $insertables, $new );

		return $insertables;
	}
}
