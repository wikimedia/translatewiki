<?php
/**
 * Implements MessageChecker for FreeCol.
 *
 * @file
 * @author Niklas Laxström
 * @license GPL-2.0-or-later
 */

/**
 * Message checks for FreeCol
 *
 * @ingroup MessageCheckers
 */
class FreeColMessageChecker extends MessageChecker {
	/**
	 * Checks for missing and unknown variables in translations.
	 *
	 * @param $messages \array Iterable list of TMessage objects.
	 * @param $code \string Language code of the translations.
	 * @param $warnings \array Array where warnings are appended to.
	 */
	protected function FreeColVariablesCheck( $messages, $code, &$warnings ) {
		return parent::parameterCheck( $messages, $code, $warnings, '/%[a-zA-Z_]+%/' );
	}

	/**
	 * Checks for bad escapes in translations.
	 *
	 * @param $messages \array Iterable list of TMessage objects.
	 * @param $code \string Language code of the translations.
	 * @param $warnings \array Array where warnings are appended to.
	 */
	protected function FreeColEscapesCheck( $messages, $code, &$warnings ) {
		foreach ( $messages as $message ) {
			$key = $message->key();
			$translation = $message->translation();

			$varPattern = '\\\\[^nt\'"]';
			preg_match_all( "/$varPattern/U", $translation, $transVars );

			# Check for missing variables in the translation
			$subcheck = 'invalid';
			$params = $transVars[0];
			if ( count( $params ) ) {
				$warnings[$key][] = [
					[ 'escape', $subcheck, $key, $code ],
					'translate-checks-escape',
					[ 'PARAMS', $params ],
					[ 'COUNT', count( $params ) ],
				];
			}
		}
	}
}
