<?php
/**
 * @file
 * @author Niklas Laxström
 * @license GPL-2.0+
 */

class EntryScapeMessageChecker extends MessageChecker {
	protected function EntryScapeVariablesCheck( $messages, $code, &$warnings ) {
		return parent::parameterCheck( $messages, $code, $warnings, '/\$?{[a-z]+}|%s/' );
	}
}
