<?php
/**
 * Implements MessageChecker for FrontlineSMS.
 *
 * @file
 * @author Niklas Laxström
 * @copyright Copyright © 2010, Niklas Laxström
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

/**
 * Message checks for FrontlineSMS
 *
 * @ingroup MessageCheckers
 */
class FrontlineSMSMessageChecker extends MessageChecker {
	protected function FrontlineSMSVariablesCheck( $messages, $code, &$warnings ) {
		return parent::parameterCheck( $messages, $code, $warnings, '/{[0-9]}/' );
	}
}
