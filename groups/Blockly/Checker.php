<?php
/**
 * @file
 * @license GPL-2.0-or-later
 */

class BlocklyMessageChecker extends MessageChecker {
	protected function variablesCheck( $messages, $code, &$warnings ) {
		// %1, %2, .., %9
		return parent::parameterCheck( $messages, $code, $warnings, '/%[1-9]/' );
	}
}
