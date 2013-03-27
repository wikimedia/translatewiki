<?php
/**
 * Helper for project handling.
 *
 * @file
 * @author Niklas Laxström
 * @license GPL2+
 */

class ProjectHandler {
	public static function getProjects() {
		$projects = array();
		$groups = MessageGroups::getGroupStructure();

		foreach ( $groups as $mixed ) {
			if ( is_array( $mixed ) ) {
				$group = array_shift( $mixed );
			} else {
				$group = $mixed;
			}

			if ( $group->getIcon() !== null ) {
				$projects[] = $group;
			}
		}
		return $projects;
	}

	/**
	 * Sort the projects by to be determined algorithm. Like most sorting
	 * functions in PHP this modifies passed list in place.
	 * @param MessageGroup[] $groups
	 * @param string $language Language code.
	 */
	public static function sortByPriority( &$groups, $language ) {
		foreach ( $groups as $index => $g ) {
			$supported = $g->getTranslatableLanguages();
			if ( is_array( $supported ) && !isset( $supported[$language] ) ) {
				unset( $groups[$index] );
			}
		}

		usort( $groups, function ( $a, $b ) use ( $language ) {
			$aStats = MessageGroupStats::forItem( $a->getId(), $language );
			$bStats = MessageGroupStats::forItem( $b->getId(), $language );

			$aVal = $aStats[MessageGroupStats::PROOFREAD];
			$bVal = $aStats[MessageGroupStats::PROOFREAD];

			if ( $aVal === $bVal ) {
				return 0;
			} else {
				return ($aVal > $bVal) ? -1 : 1;
			}
		} );
	}

	// @todo FIXME: This is duplicate code Translate - ApiQueryMessageGroup. We can avoid
	// duplication if we make getIcon of that API public static.
	public static function getIcon( MessageGroup $g, $size ) {
		global $wgServer;
		$icon = $g->getIcon();
		if ( substr( $icon, 0, 7 ) !== 'wiki://' ) {
			return null;
		}

		$formats = array();

		$filename = substr( $icon, 7 );
		$file = wfFindFile( $filename );
		if ( !$file ) {
			return '';
		}

		if ( $file->isVectorized() ) {
			$formats['vector'] = $file->getUrl();
		}

		$formats['raster'] = $wgServer . $file->createThumb( $size, $size );

		foreach ( $formats as $key => &$url ) {
			$url = wfExpandUrl( $url, PROTO_RELATIVE );
		}

		return $formats;
	}
}
