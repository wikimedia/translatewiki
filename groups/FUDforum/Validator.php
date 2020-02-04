<?php

use MediaWiki\Extensions\Translate\MessageValidator\Validator;

/**
 * Ensures that translators use {PLURAL, instead of {{PLURAL
 */
class FUDforumValidator implements Validator {
	public function validate( TMessage $message, $code, array &$notices ) {
		$key = $message->key();
		$translation = $message->translation();
		$error = strpos( $translation, '{{PLURAL' ) !== false;

		if ( $error ) {
			$notices[$key][] = [
				[ 'syntax', 'plural', $key, $code ],
				'translate-checks-fudforum-syntax',
				'{PLURAL:',
				'{{PLURAL:',
			];
		}
	}
}
