#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;
use Translatewiki\RepoNg\App\CommitCommand;
use Translatewiki\RepoNg\App\DiffCommand;
use Translatewiki\RepoNg\App\ExportCommand;
use Translatewiki\RepoNg\App\ListCommand;
use Translatewiki\RepoNg\App\StatusCommand;
use Translatewiki\RepoNg\App\UpdateCommand;

require_once __DIR__ . '/vendor/autoload.php';

$commandList = [
	'commit' => CommitCommand::class,
	'diff' => DiffCommand::class,
	'export' => ExportCommand::class,
	'list-projects' => ListCommand::class,
	'status' => StatusCommand::class,
	'update' => UpdateCommand::class,
];

$commandFactory = static function ( $className ) {
	return static function () use ( $className ) {
		return new $className();
	};
};

$commandLoader = new FactoryCommandLoader(
	array_map( $commandFactory, $commandList )
);

$application = new Application();
$application->setCommandLoader( $commandLoader );
$application->run();
