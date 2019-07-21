<?php

use MediaWiki\Extensions\Translate\MessageValidator\ValidationHelper;
use MediaWiki\Extensions\Translate\MessageValidator\Validator;

class WikiBlameValidator implements Validator {
	use ValidationHelper;

	public function validate( TMessage $message, $code, array &$notices ) {
		if ( $message->key() === 'Messages\x5b\'date_format\'\x5d' ) {
			self::parameterCheck( $message, $code, $notices, '/YYYY|MM|DD/' );
		}
	}
}
