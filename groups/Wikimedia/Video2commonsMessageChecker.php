<?php
/**
 * Message checks for video2commons
 *
 * @ingroup MessageCheckers
 */
class Video2commonsMessageChecker extends MessageChecker {
	/**
	 * Checks for missing and unknown variables in translations.
	 * Variable sytax: {{#a}}, {{/a}}
	 *
	 * @param $messages \array Iterable list of TMessage objects.
	 * @param $code \string Language code of the translations.
	 * @param $warnings \array Array where warnings are appended to.
	 */
	protected function Video2commonsVariablesCheck( $messages, $code, &$warnings ) {
		return parent::parameterCheck( $messages, $code, $warnings, '/\{\{[\/#]a\}\}/' );
	}
}
