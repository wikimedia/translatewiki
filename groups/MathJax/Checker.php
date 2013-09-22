<?php
/**
 * @file
 * @author Niklas Laxström
 * @license GPL-2.0+
 */


class MathJaxMessageChecker extends MessageChecker {
	protected function variablesCheck( $messages, $code, &$warnings ) {
		return parent::parameterCheck( $messages, $code, $warnings, '/\\$[0-9]/' );
	}
}
