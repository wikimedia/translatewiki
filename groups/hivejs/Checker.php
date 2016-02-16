<?php
/**
 * @file
 * @author Niklas Laxström
 * @license GPL-2.0+
 */

class HivejsMessageChecker extends MessageChecker {
	protected function HivejsVariablesCheck( $messages, $code, &$warnings ) {
		return parent::parameterCheck( $messages, $code, $warnings, '/{[a-z]+}/' );
	}
}
