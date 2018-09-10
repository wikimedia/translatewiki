<?php
/**
 * Implements MessageChecker for iNaturalist.
 *
 * @file
 * @author Niklas Laxström, Siebrand Mazeland
 * @license GPL-2.0-or-later
 */

/**
 * Message checks for iNaturalist
 *
 * @ingroup MessageCheckers
 */
class MifosMessageChecker extends MessageChecker {
	/**
	 * Checks for missing and unknown variables in translations.
	 *
	 * @param $messages \array Iterable list of TMessage objects.
	 * @param $code \string Language code of the translations.
	 * @param $warnings \array Array where warnings are appended to.
	 */
	protected function variablesCheck( $messages, $code, &$warnings ) {
		return parent::parameterCheck( $messages, $code, $warnings, '/{{params\[[0-9]\]\.value}}/' );
	}
}
