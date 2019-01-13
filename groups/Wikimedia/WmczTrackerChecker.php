<?php
/**
 * @file
 * @author Martin Urbanec
 * @license GPL-3.0-or-later
 */

/**
 * @ingroup MessageCheckers
 */
class WmczTrackerMessageChecker extends MessageChecker {
	/**
	 * Checks for missing and unknown variables in translations.
	 *
	 * @param $messages \array Iterable list of TMessage objects.
	 * @param $code \string Language code of the translations.
	 * @param $warnings \array Array where warnings are appended to.
	 */
	protected function variablesCheck( $messages, $code, &$warnings ) {
		// Python formatted strings
		return parent::parameterCheck( $messages, $code, $warnings, '/%(\([a-zA-Z_]+\))?[a-z]/' );
	}
}
