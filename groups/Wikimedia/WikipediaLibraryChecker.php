<?php

/**
 * @author Niklas Laxström
 * @license GPL-2.0-or-later
 */
class WPLMessageChecker extends MessageChecker {
	protected function WPLVariablesCheck( $messages, $code, &$warnings ) {
		return parent::parameterCheck( $messages, $code, $warnings, '/%\([^)]+\)s|\{[^|} ]+\}/' );
	}
}
