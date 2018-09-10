<?php
/**
 * @file
 * @author Siebrand Mazeland
 * @license GPL-2.0-or-later
 */

/**
 * @ingroup MessageCheckers
 */
class EtherpadLiteMessageChecker extends MessageChecker {
	/**
	 * Checks for missing and unknown variables in translations.
	 *
	 * @param $messages \array Iterable list of TMessage objects.
	 * @param $code \string Language code of the translations.
	 * @param $warnings \array Array where warnings are appended to.
	 */
	protected function variablesCheck( $messages, $code, &$warnings ) {
		// Variables like {{variable}}
		return parent::parameterCheck( $messages, $code, $warnings, '/{{[a-zA-Z_]+}}/' );
	}
}
