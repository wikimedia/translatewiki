<?php

/**
 * snedUdpMessage.php: Provides a function for sending a UDP message to a port
 * on a specific IP address or host name, providing a command line interface if
 * called from the command line.
 *
 * This is version 1.5 (2008-07-30).
 *
 * Change log:
 *     1.0 (2008-07-29):
 *         - Original version.
 *     1.1 (2008-07-30):
 *         - CLI mode can be explicitly disabled by defining the
 *           DISABLE_SNEDUDP_CLI constant to true.
 *     1.2 (2008-07-30):
 *         - Spaces added to the top and bottom of help message.
 *         - Support and bug reporting information added to help message.
 *         - Period removed from invalid IP address error.
 *         - Host names now supported as well as IP addresses.
 *     1.3 (2008-07-30):
 *         - Command line flag -quiet added to supress STDOUT output.
 *     1.4 (2008-07-30):
 *         - Change log added.
 *         - Comments added to the end of functions giving their name.
 *         - Typo corrected in help 'STDIN' to 'STDOUT'.
 *     1.5 (2008-07-30):
 *         - Typo corrected in change log 'Type' to 'Typo'.
 *         - Dates added to change log and current version indicator.
 *         - Version and date added to help message.
 *
 * Include into your PHP application and the UdpSend() function will be made
 * available for use, it has three parameters:
 *     $host String:  IP address or host name to send data to.
 *     $port Integer: Port on target host to send data to.
 *     $data String:  Data to send.
 * It returns true or false based upon whether it thinks it managed to send the
 * data, or whether the sending was aborted due to bad parameters (errors will
 * be triggered if this is the case, however).
 *
 * Information regarding the command line mode can be gained by changing to the
 * directory that contains the script and typing:
 *     php snedUdpMessage.php -help
 *
 * Command line mode will be invoked automatically if using the 'cli' PHP SAPI,
 * if your application uses this SAPI then you can bypass this mode by defining
 * the constant 'DISABLE_SNEDUDP_CLI' to true:
 *     define( 'DISABLE_SNEDUDP_CLI', true );
 *
 * Copyright (c) 2008 MinuteElectron <minuteelectron@googlemail.com>
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 51
 * Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * To report bugs and for support please send an e-mail to:
 *     minuteelectron@googlemail.com
 */

// If this is CLI mode then it should accept arguments and send data, otherwise
// only the UdpSend function need be defined.
if( php_sapi_name() === 'cli' && !defined( 'DISABLE_SNEDUDP_CLI' ) ) {

	/**
	 * Take arguments from the command line and use them to send data or
	 * construct a response to the users query.
	 *
	 * @param $arguments Array: Arguments passed to the command line.
	 */
	function ModeShell( array $arguments ) {

		// Take the array of arguments and parse them.
		$arguments = ParseArguments( $arguments );

		// Store the help message.
		$help = <<<EOT

Send a UDP message to a given host and port.

This is version 1.5 (2008-07-30).

USAGE: php snedUdpMessage.php [-help|--host=host --port=port [-quiet] data]

    --host=host
        IP address (can be either ipv4 or ipv6) or host name to send data to.

    --port=port
        Port on target host to send data to.

    -quiet
        Do not output anything to STDOUT (ignored if -help used).

    -help
        Display this message.

Copyright (c) 2008 MinuteElectron <minuteelectron@googlemail.com>
snedUdpMessage.php comes with ABSOLUTELY NO WARRANTY.  This is free software,
and you are welcome to redistribute it under certain conditions.  See the file
LICENSE for more information.

To report bugs and for support please send an e-mail to:
     minuteelectron@googlemail.com


EOT;

		// Check if the user is asking for help, trying to make a request, or doing something wrong.
		if( array_key_exists( 'help', $arguments ) ) {

			// User request help, return help message.
			echo $help;

		} elseif( array_key_exists( 'host', $arguments ) && array_key_exists( 'port', $arguments ) ) {

			// Put the arguments into variables.
			$host = $arguments[ 'host' ];
			$port = $arguments[ 'port' ];
			$data = $arguments[ 0      ];

			// Send the UDP.
			$result = UdpSend( $host, $port, $data );

			// Check if the quiet switch was given.
			if( array_key_exists( 'quiet', $arguments ) ) {

				// Skip past output, not wanted.
				return;

			}

			// Output message based upon success or failiure.
			if( $result ) {

				echo 'Sent message to ';

			} else {

				echo 'Unable to send message to ';

			}

			// Output basic information, and a newline.
			echo $host . ' ' . $port . '.' . "\n";

		} else {

			// User made a malformed request, return help message.
			echo $help;

		}

	} /* ModeShell() */

	/**
	 * Parse an array of command line arguments, returning it in a usable form:
	 *     --name=value to $arguments[ 'name' ] = 'value';
	 *     -switch      to $arguments[ 'switch' ] = true;
	 *     data data    to $arguments[ 0 ] = 'data data';
	 *
	 * @param $arguments Array: Arguments to parse.
	 * @return Array: Parsed arguments.
	 */
	function ParseArguments( array $arguments ) {

		// Initialize array for holding the parsed array of arguments, with a blank
		// first index for concatenating to.
		$parsed = array( '' );

		// Forget first parameter (the name of the script).
		unset( $arguments[ 0 ] );

		// Loop through the original array of arguments.
		foreach( $arguments as $argument ) {

			if( strpos( $argument, '--' ) === 0 ) {

				// The argument is a named parameter.

				// Break the argument into two halves (name and value).
				$argument = explode( '=', $argument );
				$name  = $argument[ 0 ];
				$value = $argument[ 1 ];

				// Remove opening hyphens from the argument name.
				$name = ltrim( $name, '-' );

				// Add the parameter to the array of parsed arguments.
				$parsed[ $name ] = $value;

			} elseif( strpos( $argument, '-' ) === 0 ) {

				// The argument is a switch.

				// Remove opening hyphens from the switch.
				$switch = ltrim( $argument, '-' );

				// Add the switch to the array of parsed arguments.
				$parsed[ $switch ] = true;

			} else {

				// The argument is data.

				// Add the data to the data array element.
				$parsed[ 0 ] .= $argument . ' ';

			}

		}

		// Remove excess whitespace from the data argument.
		$parsed[ 0 ] = rtrim( $parsed[ 0 ] );

		// Return the array of parsed arguments.
		return $parsed;

	} /* ParseArguments() */

	// Launch the shell mode, try to send data.
	ModeShell( $argv );

}

/**
 * Send a string of data via UDP to a port on a given IP address, automatically
 * detecting if ipv6 should be used or not.
 *
 * @param $host String:  IP address or host name to send data to.
 * @param $port Integer: Port on target host to send data to.
 * @param $data String:  Data to send.
 * @return Boolean: True if a message was sent, false if it couldn't be.
 */
function UdpSend( $host, $port, $data ) {

	// Test if the host contains a letter, to check if it is an IP address or an
	// hostname (it is a hostname if the last character is an alphabetic one.
	if( ctype_alpha( $host[ strlen( $host ) - 1 ] ) ) {

		// Translate the hostname into an IP address.
		$ip = gethostbyname( $host );

		// Check if an IP address was returned.
		if( $ip === $host ) {

			// Unable to resolve hostname, trigger a warning.
			trigger_error( 'Unable to resolve hostname to an IP address', E_USER_WARNING );

			// Exit the function as data will not be able to be sent.
			return false;

		}

	} else {

		// Host is an IP address, or at least should be.
		$ip = $host;

	}

	// Select what domain to use, either AF_INET or AF_INET6 depending on if the
	// IP address contains colons or periods.  Should the IP address contain
	// neither of these then a user warning will be triggered.
	if( strpos( $ip, ':' ) !== false ) {

		// IP address contains a colon, use ipv6.
		$domain = AF_INET6;

	} elseif( strpos( $ip, '.' ) !== false ) {

		// IP address contains a period, use ipv4.
		$domain = AF_INET;

	} else {

		// IP address contains neither a colon nor a period, trigger a warning.
		trigger_error( 'Invalid IP address, IP addresses must contain either a colon or a period to be valid', E_USER_WARNING );

		// Exit the function as data will not be able to be sent.
		return false;

	}

	// Create a socket that will be used to send the data.
	$socket = socket_create( $domain, SOCK_DGRAM, SOL_UDP );

	// Send the data, using the socket, to the given IP address and port.
	socket_sendto( $socket, $data, strlen( $data ), 0, $ip, $port );

	// Close the socket, no longer needed as data has been sent.
	socket_close( $socket );

	// UDP should have been sent, or at least we tried.
	return true;

} /* UdpSend() */