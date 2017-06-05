<?php
class InternetArchiveBotChecker extends MessageChecker {
	protected function variablesCheck( $messages, $code, &$warnings ) {
		// Variables like {{variable}}
		return parent::parameterCheck( $messages, $code, $warnings, '/{{[a-zA-Z_]+}}/' );
	}
}
