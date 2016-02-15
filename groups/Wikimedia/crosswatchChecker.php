<?php
/**
 * Message checks for crosswatch
 *
 * @ingroup MessageCheckers
 */
class CrosswatchMessageChecker extends MessageChecker {
	/**
	 * Checks for missing and unknown variables in translations.
	 * Variable sytax examples: <user>, <user name={{title}}>, {{number}} or {{flags|list}}
	 *
	 * @param $messages \array Iterable list of TMessage objects.
	 * @param $code \string Language code of the translations.
	 * @param $warnings \array Array where warnings are appended to.
	 */
	protected function CrosswatchVariablesCheck( $messages, $code, &$warnings ) {
		return parent::parameterCheck( $messages, $code, $warnings, '/<[a-zA-Z\=\{\}\s_\|]{2,}>|{{[a-zA-Z_\|]+}}/' );
	}
}
