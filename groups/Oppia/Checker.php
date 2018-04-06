<?php
/**
 * Implements MessageChecker for Oppia
 *
 * @file
 * @author Niklas Laxström and Justin Du
 * @license GPL-2.0-or-later
 */

/**
 * Message checks for Oppia
 *
 * @ingroup MessageCheckers
 */
class OppiaMessageChecker extends MessageChecker {
	/**
	 * Checks for missing and unknown variables in translations.
	 *
	 * @param $messages \array Iterable list of TMessage objects.
	 * @param $code \string Language code of the translations.
	 * @param $warnings \array Array where warnings are appended to.
	 */
	protected function OppiaVariablesCheck( $messages, $code, &$warnings ) {
		return parent::parameterCheck( $messages, $code, $warnings, '/<\[[a-zA-Z_]+\]>/' );
	}
}
