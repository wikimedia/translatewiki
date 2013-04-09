<?php
/**
 * Code for getting user rankings.
 *
 * @file
 * @author Niklas Laxström
 * @license GPL2+
 */

class UserStats {
	public static function getTranslationRankings( $language, $days = 7 ) {
		global $wgTranslateMessageNamespaces;

		$now = wfTimestamp( TS_UNIX );
		$weekago = $now - $days * 24 * 60 * 60;
		$ret = array();

		$dbr = wfGetDB( DB_SLAVE );

		$fields = array(
			'user_id' => 'rc_user',
			'user_name' => 'rc_user_text',
			'rc_timestamp',
			'rc_id',
		);

		$conds = array(
			'rc_namespace' => $wgTranslateMessageNamespaces,
			'rc_title' . $dbr->buildLike( $dbr->anyString(), "/$language" ),
			'rc_bot' => 0,
		);

		$options = array(
			'ORDER BY' => 'rc_id DESC',
			'LIMIT' => 500
		);

		$cutoff = false;

		while ( true ) {
			$myconds = $conds;

			if ( $cutoff ) {
				$myconds[] = "rc_id < $cutoff";
			}

			$res = $dbr->select( 'recentchanges', $fields, $myconds, __METHOD__, $options );
			foreach ( $res as $row ) {
				if ( wfTimestamp( TS_UNIX, $row->rc_timestamp ) < $weekago ) {
					break 2;
				}

				if ( !isset( $ret[$row->user_name] ) ) {
					$ret[$row->user_name] = 0;
				}
				$ret[$row->user_name]++;
				$cutoff = $row->rc_id;
			}

			if ( $res->numRows() < 1000 ) {
				break;
			}
		}

		arsort( $ret );

		return $ret;
	}

	public static function getProofreadRankings( $language, $days = 7 ) {
		global $wgTranslateMessageNamespaces;

		$now = wfTimestamp( TS_UNIX );
		$weekago = $now - $days * 24 * 60 * 60;
		$ret = array();

		$dbr = wfGetDB( DB_SLAVE );

		$fields = array(
			'user_id' => 'log_user',
			'user_name' => 'log_user_text',
			'actions' => 'count(log_id)'
		);

		$conds = array(
			'log_namespace' => $wgTranslateMessageNamespaces,
			'log_title' . $dbr->buildLike( $dbr->anyString(), "/$language" ),
			'log_type' => 'translationreview',
			'log_action' => 'message',
			'log_timestamp >= ' . $dbr->timestamp( $weekago ),
		);

		$options = array(
			'GROUP BY' => 'user_name',
		);

		$res = $dbr->select( 'logging', $fields, $conds, __METHOD__, $options );
		foreach ( $res as $row ) {
			$ret[$row->user_name] = $row->actions;
		}

		arsort( $ret );

		return $ret;
	}
}
