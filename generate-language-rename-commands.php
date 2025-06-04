<?php

use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . '/vendor/autoload.php';

if ( $argc < 3 ) {
	echo "Usage: php $argv[0] <source-language> <target-language>\n";
	exit( 1 );
}

$sourceLanguage = $argv[1];
$targetLanguage = $argv[2];

$config = Yaml::parseFile( 'repoconfig.yaml' );

foreach ( $config as $key => $value ) {
	if ( $key === '@meta' ) {
		continue;
	}

	echo "sudo -u l10n-bot repomulti update {$key}\n";

	$group = $value['group'];
	echo "sudo -u l10n-bot php /srv/mediawiki/targets/production/extensions/Translate/scripts/export-rename-language.php --group='{$group}' --target=. --source-language {$sourceLanguage} --target-language {$targetLanguage}\n";

	echo "sudo -u l10n-bot repomulti status {$key}\n";
	echo "sudo -u l10n-bot repomulti commit {$key}\n";
	echo "\n";
}
