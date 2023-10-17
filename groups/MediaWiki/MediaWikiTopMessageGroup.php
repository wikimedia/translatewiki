<?php

use MediaWiki\Extension\Translate\MessageGroupProcessing\MessageGroups;

/**
 * @file
 * @author Niklas Laxström
 * @license GPL-2.0-or-later
 */

class MediaWikiTopMessageGroup extends MessageGroupOld {
	protected $label = 'MediaWiki (most important messages)';

	private $parentGroup;
	private $file;
	private $keys;
	private ?array $tagCache = null;

	public function __construct( $id, $file ) {
		$this->id = $id;
		$this->file = $file;
	}

	public function isMeta() {
		return true;
	}

	public function exists() {
		return true;
	}

	public function load( $code ) {
		return [];
	}

	public function getParentGroup() {
		if ( !$this->parentGroup ) {
			$this->parentGroup = MessageGroups::getGroup( 'mediawiki' );
		}

		return $this->parentGroup;
	}

	public function getKeys() {
		if ( $this->keys === null ) {
			$few = explode( "\n", trim( file_get_contents( $this->file ) ) );
			$lots = $this->getParentGroup()->getKeys();

			$fewer = array_intersect( $few, $lots );
			if ( count( $fewer ) < count( $few ) ) {
				error_log( 'Invalid top messages: ' . implode( ', ', array_diff( $few, $fewer ) ) );
			}

			$this->keys = array_values( $fewer );
		}

		return $this->keys;
	}

	public function getDefinitions() {
		$parent = $this->getParentGroup();
		$sourceLanguage = $parent->getSourceLanguage();

		$defs = [];
		foreach ( $this->getKeys() as $key ) {
			$defs[$key] = $parent->getMessage( $key, $sourceLanguage );
		}

		return $defs;
	}

	public function getTags( $type = null ) {
		$this->tagCache ??= $this->getParentGroup()->getTags( null );
		return $type ? $this->tagCache[$type] ?? [] : $this->tagCache;
	}

	public function getMessage( $key, $code ) {
		return $this->getParentGroup()->getMessage( $key, $code );
	}

	public function getValidators() {
		return $this->getParentGroup()->getValidators();
	}

	public function getInsertablesSuggester() {
		return $this->getParentGroup()->getInsertablesSuggester();
	}
}
