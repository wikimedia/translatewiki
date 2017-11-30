#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

require_once __DIR__ . '/vendor/autoload.php';

abstract class RepoNgCommand extends Command {
	protected $bindir;
	protected $config;
	protected $usernameConversion = [
		'nike' => 'nikerabbit',
	];
	protected $parallelism = 1;
	protected $base;
	protected $defaultVariant;

	public function initialize() {
		$configName = 'repoconfig.yaml';
		$this->base = $base = $this->findBase( $configName );
		$configFile = "$base/$configName";

		if ( !file_exists( $configFile ) ) {
			throw new RuntimeException( 'Cannot find configuration' );
		}

		$this->bindir = realpath( __DIR__ . '/../bin' );
		if ( $this->bindir === false ) {
			throw new RuntimeException( __DIR__ . '/../bin/ does not exist' );
		}

		$yaml = file_get_contents( $configFile );
		$this->config = yaml_parse( $yaml );

		$cores = preg_match_all( '/^processor/m', file_get_contents( '/proc/cpuinfo' ) );
		if ( $cores ) {
			$this->parallelism = (int)$cores;
		}

		$variantFile = "$base/REPONG-VARIANT";
		if ( file_exists( $variantFile ) ) {
			$this->defaultVariant = trim( file_get_contents( $variantFile ) );
		}
	}

	protected function getBase() {
		return $this->base;
	}

	protected function findBase( $configName ) {
		$path = getcwd();
		if ( $path === false ) {
			return null;
		}

		while ( true ) {
			if ( file_exists( "$path/$configName" ) ) {
				return $path;
			}

			if ( $path === realpath( "$path/.." ) ) {
				return null;
			}

			$path = realpath( "$path/.." );
		}
	}

	protected function getConfig( $project, $targetVariant = null ) {
		if ( !isset( $this->config[ $project ] ) ) {
			echo "Unknown project $project\n";
			exit( 1 );
		}

		// Reference is needed for recursive closure to work
		$replacer = function ( $array ) use ( &$replacer, $targetVariant ) {
			$new = [];
			foreach ( $array as $key => $value ) {
				$split = strpos( $key, '|' );
				if ( $split !== false ) {
					$variant = substr( $key, $split + 1 );
					if ( $variant === $targetVariant ) {
						$key = substr( $key, 0, $split );
					} else {
						// Some other variant, drop it
						continue;
					}
				}

				if ( is_array( $value ) ) {
					$value = $replacer( $value );
				}

				// Assumption: custom variants always come after default variant.
				// Otherwise the default would override the custom one.
				$new[$key] = $value;
			}
			return $new;
		};

		return $replacer( $this->config[ $project ] );
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

	protected function runParallelWithOutput( SplObjectStorage $processes, OutputInterface $output ) {
		$this->runParaller(
			$processes,
			function ( $process ) use ( $output ) {
				$output->writeln( $process->getCommandLine(), OutputInterface::VERBOSITY_VERBOSE );
			},
			function ( $process, $exception = null ) use ( $output ) {
				if ( $process->isSuccessful() ) {
					$processOutput = $process->getOutput();
					if ( trim( $processOutput ) ) {
						$formatter = $output->getFormatter();
						$command = $formatter->escape( $process->getCommandLine() );
						$stdout = $formatter->escape( $processOutput );
						$output->write( "<options=bold>$command</>\n$stdout\n" );
					}
				} elseif ( $exception ) {
					throw $exception;
				} else {
					throw new ProcessFailedException( $process );
				}
			}
		);
	}

	protected function runParaller(
		SplObjectStorage $queue,
		callable $onStart = null,
		callable $onEnd = null
	) {
		$running = [];
		do {
			while ( count( $running ) < $this->parallelism ) {
				list( $ok, $process ) = self::getNextExecutableProcessIndex( $queue );

				// Check if anything to execute
				if ( $ok === 'NONE' ) {
					break;
				}

				unset( $queue[ $process ] );
				is_callable( $onStart ) && $onStart( $process );

				// Check whether to run or fail the process
				if ( $ok === 'OK' ) {
					$process->start();
					$running[] = $process;
				} else {
					$exception = new RuntimeException( 'A dependent task failed' );
					is_callable( $onEnd ) && $onEnd( $process, $exception );
				}
			}

			usleep( 10000 );

			foreach ( $running as $index => $process ) {
				if ( !$process->isRunning() ) {
					unset( $running[$index] );
					is_callable( $onEnd ) && $onEnd( $process );
				}

				try {
					$process->checkTimeout();
				} catch ( ProcessTimedOutException $e ) {
					unset( $running[$index] );
					is_callable( $onEnd ) && $onEnd( $process, $e );
				}
			}
		} while ( count( $queue ) > 0 || $running !== [] );
	}

	private static function getNextExecutableProcessIndex( $queue ) {
		foreach ( $queue as $process ) {
			$dependency = $queue[ $process ];
			if ( $dependency === null ) {
				return [ 'OK', $process ];
			} elseif ( $dependency->isTerminated() ) {
				return $dependency->isSuccessful() ? [ 'OK', $process ] : [ 'FAIL', $process ];
			}
		}

		return [ 'NONE', null ];
	}
}

class UpdateCommand extends RepoNgCommand {
	protected function configure() {
		parent::configure();
		$this->setName( 'update' );
		$this->addArgument( 'project', InputArgument::REQUIRED );
		$this->addOption( 'variant', null, InputOption::VALUE_REQUIRED );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$project = $input->getArgument( 'project' );
		$variant = $input->getOption( 'variant' ) ?: $this->defaultVariant;
		$config = $this->getConfig( $project, $variant );
		$base = $this->getBase();
		$bindir = $this->bindir;

		$processes = new SplObjectStorage();

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
			} elseif ( $type === 'svn' ) {
				$command = "$bindir/clupdate-svn-repo  '{$repo['url']}' '$base/$name'";
			} else {
				throw new RuntimeException( 'Unknown repo type' );
			}

			$process = new Process( $command );
			$process->setTimeout( 300 );
			$processes->attach( $process );
		}

		$this->runParallelWithOutput( $processes, $output );
	}
}

class ExportCommand extends RepoNgCommand {
	protected function configure() {
		parent::configure();
		$this->setName( 'export' );
		$this->addArgument( 'project', InputArgument::REQUIRED );
		$this->addOption( 'variant', null, InputOption::VALUE_REQUIRED );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$project = $input->getArgument( 'project' );
		$variant = $input->getOption( 'variant' ) ?: $this->defaultVariant;
		$config = $this->getConfig( $project, $variant );
		$exporter = $this->config['@meta']['export'];
		$expander = $this->config['@meta']['expand'];

		$defaultOptions = [
			'group' => null,
			'quiet' => true,
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

		$command = "$expander '{$config[ 'group' ]}'";
		$process = new Process( $command );
		$process->setTimeout( 10 );
		$process->mustRun();
		$groups = explode( "\n", trim( $process->getOutput() ) );

		$processes = new SplObjectStorage();

		foreach ( $groups as $group ) {
			$defaultOptions[ 'group' ] = $group;

			$jobOptions = [ 'lang' => '*' ] + $defaultOptions + [ 'skip' => 'en,qqq' ];
			$command = $this->buildCommandline( $exporter, $jobOptions );
			$process1 = new Process( $command );
			$process1->setTimeout( 300 );

			$processes->attach( $process1 );

			// Then message documentation
			$jobOptions = [ 'lang' => 'qqq', 'threshold' => null ] + $defaultOptions;
			$command = $this->buildCommandline( $exporter, $jobOptions );
			$process2 = new Process( $command );
			$process2->setTimeout( 30 );
			$processes->attach( $process2, $process1 );

			// Last languages that have a forced export
			if ( !isset( $config['always-export-languages'] ) ) {
				continue;
			}

			$lang = $config['always-export-languages'];
			$jobOptions = [ 'lang' => $lang, 'threshold' => null ] + $defaultOptions;
			$command = $this->buildCommandline( $exporter, $jobOptions );

			$process3 = new Process( $command );
			$process3->setTimeout( 120 );
			$processes->attach( $process3, $process2 );
		}

		$this->runParallelWithOutput( $processes, $output );
	}
}

class CommitCommand extends RepoNgCommand {
	protected function configure() {
		parent::configure();
		$this->setName( 'commit' );
		$this->addArgument( 'project', InputArgument::REQUIRED );
		$this->addOption( 'variant', null, InputOption::VALUE_REQUIRED );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$project = $input->getArgument( 'project' );
		$variant = $input->getOption( 'variant' ) ?: $this->defaultVariant;
		$config = $this->getConfig( $project, $variant );
		$message = 'Localisation updates from https://translatewiki.net.';
		$base = $this->getBase();

		$processes = new SplObjectStorage();

		foreach ( $config['repos'] as $name => $repo ) {
			if ( $repo['type'] === 'git' || $repo['type'] === 'github' ) {
				$dir = "$base/$name";
				$branch = isset( $repo['branch'] ) ? $repo['branch'] : 'master';
				$command =
					"cd '$dir'; git add .; if ! git diff --cached --quiet; " .
					"then git commit -m '$message'; git push origin '$branch'; fi";
			} elseif ( $repo['type'] === 'wmgerrit' ) {
				$dir = "$base/$name";
				$command =
					"cd '$dir'; git add .; if ! git diff --cached --quiet; " .
					"then git commit -m '$message'; git review -r origin -t L10n; fi";
			} elseif ( $repo['type'] === 'svn' ) {
				$dir = "$base/$name";
				$extra = '';
				if ( isset( $repo['svn-add-options'] ) ) {
					foreach ( (array)$repo['svn-add-options'] as $option ) {
						$extra .= " --config-option '$option'";
					}
				}

				$command =
					"cd '$dir'; " .
					"svn add --force * --auto-props --parents --depth infinity -q$extra; " .
					"svn commit --message '$message'";
			} else {
				throw new RuntimeException( 'Unknown repo type' );
			}

			$process = new Process( $command );
			$process->setTimeout( 120 );
			$processes->attach( $process );

			$autoMerge = isset( $repo['auto-merge'] ) ? $repo['auto-merge'] : true;

			// Merge patch sets submitted to Wikimedia's Gerrit.
			if ( $repo['type'] === 'wmgerrit' && $autoMerge ) {
				$project = str_replace( 'ssh://l10n-bot@gerrit.wikimedia.org:29418/', '', $repo['url'] );
				$command = $this->bindir . "/merge-wmgerrit-patches '$project'";

				$mergeProcess = new Process( $command );
				$mergeProcess->setTimeout( 120 );
				$processes->attach( $mergeProcess, $process );
			}
		}

		$this->runParallelWithOutput( $processes, $output );
	}
}

$application = new Application();
$application->add( new UpdateCommand() );
$application->add( new ExportCommand() );
$application->add( new CommitCommand() );
$application->run();
