<?php
declare( strict_types = 1 );

use MediaWiki\CommentStore\CommentStoreComment;
use MediaWiki\MediaWikiServices;
use MediaWiki\Page\WikiPageFactory;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\User\UserFactory;

/**
 * Job to send a newly promoted user a welcome message on their talk page
 * @author Eugene Wang'ombe
 * @license GPL-2.0-or-later
 * @since 2023.08
 */
class NewUserMessageJob extends Job implements GenericParameterJob {
	private UserFactory $userFactory;
	private WikiPageFactory $wikiPageFactory;
	private const TEMPLATE_WELCOME = '{{Template:Welcome|realname=|name=%s}}';
	private const EDIT_SUMMARY = 'Adding [[Template:Welcome|welcome message]] to new user\'s talk page';
	private const SYSTEM_USER = 'WelcomeMessageBot';

	public function __construct( array $params ) {
		parent::__construct( 'NewUserMessageJob', $params );
	}

	public function run(): bool {
		$mediawikiServices = MediaWikiServices::getInstance();

		$this->userFactory = $mediawikiServices->getUserFactory();
		$this->wikiPageFactory = $mediawikiServices->getWikiPageFactory();
		$user = $this->userFactory->newFromId( (int)$this->params['userId'] );

		if ( !$user->isNamed() ) {
			return false;
		}

		$this->sendNewUserMessage( $user );

		return true;
	}

	/** Return the editor for sending new user messages. */
	private function fetchEditor(): User {
		$editor = $this->userFactory->newFromName( self::SYSTEM_USER );
		if ( $editor === null ) {
			throw new LogicException( 'Edit user instance could not be created for ' . self::SYSTEM_USER );
		}

		if ( !$editor->isNamed() || $editor->getBlock() ) {
			throw new LogicException( 'Edit user ' . self::SYSTEM_USER . ' does not exist or is blocked' );
		}

		return $editor;
	}

	/** Add the message if the users talk page does not already exist */
	private function sendNewUserMessage( User $user ): void {
		$talkPage = $user->getTalkPage();
		if ( $talkPage->exists() ) {
			return;
		}

		$name = $user->getRealName();
		if ( $name === '' ) {
			$name = $user->getName();
		}

		try {
			$editor = $this->fetchEditor();

			$text = sprintf( self::TEMPLATE_WELCOME, $name );
			$signature = self::SYSTEM_USER;
			$text =	"$text\n\n-- $signature  ~~~~~\n";

			$wikiPage = $this->wikiPageFactory->newFromTitle( $talkPage );
			$content = ContentHandler::makeContent( $text, $wikiPage->getTitle() );
			$updater = $wikiPage
				->newPageUpdater( $editor )
				->setContent( SlotRecord::MAIN, $content )
				->setFlags( EDIT_NEW | EDIT_FORCE_BOT );
			$summary = CommentStoreComment::newUnsavedComment( self::EDIT_SUMMARY );
			$updater->saveRevision( $summary );
			$updateStatus = $updater->getStatus();
			if ( !$updateStatus->isOK() ) {
				throw new RuntimeException(
					"Could not send user '{$name}' a message. Error: " . $updateStatus->getMessage()->text()
				);
			}
		} catch ( Exception $e ) {
			error_log( 'NewUserMessageJob: ' . $e->getMessage() . "\n--\n" . $e->getTraceAsString() );
		}
	}
}
