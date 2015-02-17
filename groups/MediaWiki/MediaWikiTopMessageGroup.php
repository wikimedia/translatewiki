<?php
/**
 * @file
 * @author Niklas Laxström
 * @license GPL-2.0+
 */

class MediaWikiTopMessageGroup extends MessageGroupOld {
	protected $label = 'MediaWiki (most important messages)';

	private $parentGroup;
	private $file;

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
		return array();
	}

	public function getParentGroup() {
		if ( !$this->parentGroup ) {
			$this->parentGroup = MessageGroups::getGroup( 'mediawiki' );
		}

		return $this->parentGroup;
	}

	public function getKeys() {
		$few = explode( "\n", file_get_contents( $this->file ) );
		$lots = $this->getParentGroup()->getKeys();

		$fewer = array_intersect( $few, $lots );
		if ( count ( $fewer ) < count( $few ) ) {
			error_log( 'Invalid top messages: ' . implode( ', ', array_diff( $few, $fewer ) ) );
		}

		return array_values( $fewer );
	}

	public function getDefinitions() {
		$parent = $this->getParentGroup();
		$sourceLanguage = $parent->getSourceLanguage();

		$defs = array();
		foreach ( $this->getKeys() as $key ) {
			$defs[$key] = $parent->getMessage( $key, $sourceLanguage );
		}

		return $defs;
	}

	public function getTags( $type = null ) {
		return $this->getParentGroup()->getTags( $type );
	}

	public function getMessage( $key, $code ) {
		return $this->getParentGroup()->getMessage( $key, $code );
	}

	public function getChecker() {
		return $this->getParentGroup()->getChecker();
	}

	public function getInsertablesSuggester() {
		return $this->getParentGroup()->getInsertablesSuggester();
	}
}
