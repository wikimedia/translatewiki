#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

require_once __DIR__ . '/vendor/autoload.php';

abstract class RepoNgCommand extends Command {
	protected $bindir;
	protected $config;
	protected $usernameConversion = [
		'nike' => 'nikerabbit',
	];

	public function initialize() {
		$base = $this->getBase();

		if ( !file_exists( "$base/repoconfig.json" ) ) {
			throw new RuntimeException( 'Cannot find configuration' );
		}

		$this->bindir = realpath( __DIR__ . '/../bin' );
		if ( $this->bindir === false ) {
			throw new RuntimeException( __DIR__ . '/../bin/ does not exist' );
		}

		$json = file_get_contents( "$base/repoconfig.json" );
		$this->config = json_decode( $json, true );
	}

	protected function getBase() {
		$path = getcwd();
		if ( $path === false ) {
			return null;
		}

		while ( true ) {
			if ( file_exists( "$path/repoconfig.json" ) ) {
				return $path;
			}

			if ( $path === realpath( "$path/.." ) ) {
				return null;
			}

			$path = realpath( "$path/.." );
		}
	}

	protected function getConfig( $project ) {
		if ( !isset( $this->config[$project] ) ) {
			echo "Unknown project $project\n";
			die();
		}

		return $this->config[$project];
	}

	protected function buildCommandline( $command, $options ) {
		$str = $command;
		foreach ( $options as $key => $value ) {
			if ( $value !== null ) {
				$str .= " --$key='$value'";
			}
		}

		return $str;
	}
}

class UpdateCommand extends RepoNgCommand {
	protected function configure() {
		$this->setName( 'update' );
		$this->setDefinition( [
			new InputArgument( 'project', InputArgument::REQUIRED ),
		] );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$project = $input->getArgument( 'project' );
		$config = $this->getConfig( $project );
		$base = $this->getBase();
		$bindir = $this->bindir;

		foreach ( $config['repos'] as $name => $repo ) {
			$type = $repo['type'];
			$branch = isset( $repo['branch'] ) ? $repo['branch'] : 'master';

			if ( $type === 'git' ) {
				$userName = get_current_user();
				if ( isset( $this->usernameConversion[$userName] ) ) {
					$userName = $this->usernameConversion[$userName];
				}

				$repoUrl = $repo['url'];
				$repoUrl = str_replace( 'USERNAME', $userName, $repoUrl );

				$command = "$bindir/clupdate-git-repo '$repoUrl' '$base/$name' '$branch'";
			} elseif ( $type === 'github' ) {
				$command = "$bindir/clupdate-github-repo '{$repo['url']}' '$base/$name' '$branch'";
			} elseif ( $type === 'wmgerrit' ) {
				$command = "$bindir/clupdate-gerrit-repo '{$repo['url']}' '$base/$name' '$branch'";
			} else {
				throw new RuntimeException( 'Unknown repo type' );
			}
			$output->writeln( $command, OutputInterface::VERBOSITY_VERBOSE );

			$process = new Process( $command );
			$process->setTimeout( 600 );
			$process->mustRun();
			$output->write( $process->getOutput() );
		}
	}
}

class ExportCommand extends RepoNgCommand {
	protected function configure() {
		$this->setName( 'export' );
		$this->setDefinition( [
			new InputArgument( 'project', InputArgument::REQUIRED ),
		] );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$project = $input->getArgument( 'project' );
		$config = $this->getConfig( $project );
		$exporter = $this->config['@meta']['export'];

		$defaultOptions = [
			'quiet' => true,
			'group' => $config['group'],
			'threshold' => 35,
			'target' => $this->getBase(),
		];

		if ( isset( $config['export-hours'] ) ) {
			$defaultOptions['hours'] = (int)$config['export-hours'];
		}

		if ( isset( $config['no-export-languages'] ) ) {
			$defaultOptions['skip'] = $config['no-export-languages'];
		}

		if ( isset( $config['export-threshold'] ) ) {
			$defaultOptions['threshold'] = (int)$config['export-threshold'];
		}

		$jobOptions = [ 'lang' => '*' ] + $defaultOptions + [ 'skip' => 'en,qqq' ];
		$command = $this->buildCommandline( $exporter, $jobOptions );
		$output->writeln( $command, OutputInterface::VERBOSITY_VERBOSE );

		$process = new Process( $command );
		$process->setTimeout( 300 );
		$process->mustRun();
		$output->write( $process->getOutput() );

		// Then message documentation
		$jobOptions = [ 'lang' => 'qqq', 'threshold' => null ] + $defaultOptions;
		$command = $this->buildCommandline( $exporter, $jobOptions );
		$output->writeln( $command, OutputInterface::VERBOSITY_VERBOSE );

		$process = new Process( $command );
		$process->mustRun();
		$output->write( $process->getOutput() );

		// Last languages that have a forced export
		if ( isset( $config['always-export-languages'] ) ) {
			$lang = $config['always-export-languages'];
			$jobOptions = [ 'lang' => $lang, 'threshold' => null ] + $defaultOptions;
			$command = $this->buildCommandline( $exporter, $jobOptions );
			$output->writeln( $command, OutputInterface::VERBOSITY_VERBOSE );

			$process = new Process( $command );
			$process->setTimeout( 120 );
			$process->mustRun();
			$output->write( $process->getOutput() );
		}

	}
}

class CommitCommand extends RepoNgCommand {
	protected function configure() {
		$this->setName( 'commit' );
		$this->setDefinition( [
			new InputArgument( 'project', InputArgument::REQUIRED ),
		] );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$project = $input->getArgument( 'project' );
		$config = $this->getConfig( $project );
		$message = 'Localisation updates from https://translatewiki.net.';
		$base = $this->getBase();

		foreach ( $config['repos'] as $name => $repo ) {
			if ( $repo['type'] === 'git' || $repo['type'] === 'github' ) {
				$dir = "$base/$name";
				$branch = isset( $repo['branch'] ) ? $repo['branch'] : 'master';
				$command =
					"cd $dir; git add .; if ! git diff --cached --quiet; " .
					"then git commit -m '$message'; git push origin '$branch'; fi";
			} elseif ( $repo['type'] === 'wmgerrit' ) {
				$dir = "$base/$name";
				$command =
					"cd $dir; git add .; if ! git diff --cached --quiet; " .
					"then git commit -m '$message'; git review -r origin -t L10n; fi";
			} else {
				throw new RuntimeException( 'Unknown repo type' );
			}
			$output->writeln( $command, OutputInterface::VERBOSITY_VERBOSE );

			$process = new Process( $command );
			$process->setTimeout( 120 );
			$process->mustRun();
			$output->write( $process->getOutput() );

			$autoMerge = isset( $repo['auto-merge'] ) ? $repo['auto-merge'] : true;

			// Merge patch sets submitted to Wikimedia's Gerrit.
			if ( $repo['type'] === 'wmgerrit' && $autoMerge ) {
				$project = str_replace( 'ssh://l10n-bot@gerrit.wikimedia.org:29418/', '', $repo['url'] );
				$command = $this->bindir . "/merge-wmgerrit-patches '$project'";
				$output->writeln( $command, OutputInterface::VERBOSITY_VERBOSE );

				$process = new Process( $command );
				$process->setTimeout( 120 );
				$process->mustRun();
				$output->write( $process->getOutput() );
			}
		}
	}
}

$application = new Application();
$application->add( new UpdateCommand() );
$application->add( new ExportCommand() );
$application->add( new CommitCommand() );
$application->run();
