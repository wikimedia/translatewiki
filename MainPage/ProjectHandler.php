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
