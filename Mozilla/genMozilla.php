<?php
/**
 * Script to generate YAML configuration for Mozilla projects.
 *
 * @file
 * @author Siebrand Mazeland
 * @copyright Copyright © 2012 Siebrand Mazeland
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

// Translate instance specific base folder. This one is for translatewiki.net.
$projectsFolder = '/resources/projects';
// Name of folder in which Mozilla source trees are cloned/checked out.
$projectFolder = 'mozilla';
// Array of supported products. Any of browser, calendar, chat, dom, editor,
// embedding, extensions, mail, mobile, netwerk, other-licenses, security,
// services, suite, toolkit.
// Currently only browser.
$products = array(
	'browser'
);
// Folder below $projectFolder in which source files are.
$sourceFolder = 'en';
// File pattern for gettext template files.
$fileTypes = array(
	// 'ID' => array( 'FileExtension', 'FFSPrefix' ),
	'dtd' => array( 'dtd', 'Dtd' ),
	'properties' => array( 'properties', 'Java' ),
);
// excluded files.
$excludedFiles = array(
	'chrome/browser/browser.properties',
	'chrome/browser/feeds/subscribe.dtd',
	'chrome/browser/preferences/selectBookmark.dtd',
);

foreach ( $products as $product ) {
	$baseFolder = "$projectsFolder/$projectFolder/${sourceFolder}/$product/";

	foreach ( $fileTypes as $fileType ) {
		$output = '';

		$fileExtension = $fileType[0];
		$FFS = $fileType[1];
		$findCmd = "find ${baseFolder} -name '*.${fileExtension}'";
		$files = shell_exec( $findCmd );
		$files = explode( "\n", $files );
		$files = array_filter( $files );

		foreach ( $files as $index => $file ) {
			$files[$index] = str_replace( $baseFolder, '', $file );
		}

		// Template header for YAML config file.
		$header = <<<PHP
TEMPLATE:
  BASIC:
    description: "{{int:translate-group-desc-mozilla-browser}}"
    namespace: NS_MOZILLA
    class: FileBasedMessageGroup

  FILES:
    class: ${FFS}FFS
    codeMap:
      gu: gu-IN

  MANGLER:
    class: StringMatcher
    patterns:
      - "*"

  CHECKER:
    class: MessageChecker
    checks:
      - printfCheck

  LANGUAGES:
    whitelist:
      - gu
      - he

PHP;

		$output .= $header . "\n";

		$localeFolder = "${projectFolder}/%CODE%/$product";

		asort( $files );

		// Add config for each file.
		foreach ( $files as $file ) {
			// Exclude some files.
			if ( in_array( $file, $excludedFiles ) ) {
				echo "excluding $file\n";
				continue;
			}
			echo "reading $file\n";

			// Strip extension from file name and break into parts for plugin name.
			// Later on, used to create a group name.
			$groupName = explode( "/", str_replace( '.' . $fileExtension, '', $file ) );

			// File name in lower case.
			$fileL = strtolower( $file );

			// Create group ID by replacing directory separators by dashes after
			// file extension was stripped.
			$groupId = str_replace( "/", '-', $fileL );
			$groupId = str_replace( '.' . $fileExtension, '', $groupId );
			$groupId .= '-' . strtolower( $FFS );

			// Create a group name by concatenating the directory and file name parts
			// after capitalising them.
			foreach ( $groupName as $index => $groupNamePart ) {
				$groupName[$index] = ucfirst( $groupNamePart );
			}
			$groupName = implode( ' - ', $groupName );
			$groupName .= " - ${FFS}";

			// Actual configuration.
			$output .= "---\n";
			$output .= "BASIC:\n";
			$output .= "  id: mozilla-${groupId}\n";
			$output .= "  label: Mozilla - ${groupName}\n";
			$output .= "\n";
			$output .= "FILES:\n";
			$output .= "  sourcePattern: %GROUPROOT%/${localeFolder}/${file}\n";
			$output .= "  targetPattern: ${localeFolder}/${file}\n";
			$output .= "\n";
			// Mangler prefix based on group ID.
			$output .= "MANGLER:\n";
			$output .= "  prefix: ${groupId}-\n";
			$output .= "\n";
		}

		// Write a file for each file type to Mozilla<FFSName>.yaml in current
		// directory.
		$fp = fopen( "Mozilla${FFS}.yaml", 'w' );
		fwrite( $fp, $output );
		fclose( $fp );
	}
}

echo "Done.\n";
