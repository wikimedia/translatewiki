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
	const MAX_CONNECTIONS = 4;

	protected $bindir;
	protected $config;
	protected $parallelism = 1;
	protected $base;
	protected $defaultVariant;

	public function initialize( InputInterface $input, OutputInterface $output ) {
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

		$config = $this->config[ $project ];

		// Step 1: Handle repo generator
		if ( isset( $config[ 'repos' ][ '@generator' ] ) ) {
			$generator = $config[ 'repos' ][ '@generator' ];
			$process = new Process( $generator );
			$process->setWorkingDirectory( $this->bindir );
			$process->setTimeout( 15 );
			$process->mustRun();

			$output = json_decode( $process->getOutput(), true );
			if ( !$output ) {
				echo "Repo generator for $project failed\n";
				exit( 1 );
			}

			$config[ 'repos' ] = $output;
		}

		// Step 2: Handle variants
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

		return $replacer( $config );
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
		$this->parallelism = min( RepoNgCommand::MAX_CONNECTIONS, $this->parallelism );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$project = $input->getArgument( 'project' );
		$variant = $input->getOption( 'variant' ) ?: $this->defaultVariant;
		$defaultConfig = $this->getConfig( $project, null );
		$config = $this->getConfig( $project, $variant );
		$meta = $this->getConfig( '@meta', $variant );
		$base = $this->getBase();
		$bindir = $this->bindir;
		// Without state synchronization, the repository we make commits
		// could be ahead of the state that has been processed in the wiki.
		// With state synchronization we ensure we do not overwrite any
		// changes that have been made in the between.
		$stateDir = $meta[ 'state-directory' ] ?? false;

		$processes = new SplObjectStorage();

		foreach ( $config['repos'] as $name => $repo ) {
			$type = $repo['type'];
			$branch = $repo['branch'] ?? 'master';

			// Check if we can use state synchronization for this repo
			$defaultConfigBranch = $defaultConfig[ 'repos' ][ $name ][ 'branch' ] ?? 'master';
			$branchCompatible = $branch === $defaultConfigBranch;
			$syncState = $stateDir && $branchCompatible && !isset( $repo[ 'no-state-sync' ] );

			// Determine the state to use, if possible
			$state = null;
			if ( $syncState && in_array( $type, [ 'git', 'github', 'wmgerrit' ] ) ) {
				$process = new Process( 'git log --pretty="%H" -n 1' );
				$process->setWorkingDirectory( "$stateDir/$name" );
				$process->setTimeout( 5 );
				$process->run();
				if ( $process->isSuccessful() ) {
					$state = trim( $process->getOutput() );
				} else {
					$output->writeln( "Unable to synchronize the state for repository $name" );
				}
			}

			if ( $type === 'git' ) {
				$command = "$bindir/clupdate-git-repo '{$repo['url']}' '$base/$name' '$branch'";
			} elseif ( $type === 'github' ) {
				$command = "$bindir/clupdate-github-repo '{$repo['url']}' '$base/$name' '$branch'";
			} elseif ( $type === 'wmgerrit' ) {
				$command = "$bindir/clupdate-gerrit-repo '{$repo['url']}' '$base/$name' '$branch'";
			} elseif ( $type === 'svn' ) {
				$command = "$bindir/clupdate-svn-repo '{$repo['url']}' '$base/$name'";
			} elseif ( $type === 'bzr' ) {
				$command = "$bindir/clupdate-bzr-repo '{$repo['url']}' '$base/$name' '$branch'";
			} else {
				$config = yaml_emit( [ $name => $repo ] );
				throw new RuntimeException( "Unknown repo type:\n$config" );
			}

			if ( $state ) {
				$command .= " '$state'";
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
			'threshold' => 25,
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

			// Then message documentation (unless in no-export-languages) and always-export-languages
			$lang = [ 'qqq' ];
			if ( isset( $config['always-export-languages'] ) ) {
				$extra = explode( ',', $config['always-export-languages'] );
				$lang = array_unique( array_merge( $lang, $extra ) );
			}

			$jobOptions = [ 'lang' => implode( ',', $lang ), 'threshold' => null ] + $defaultOptions;
			$command = $this->buildCommandline( $exporter, $jobOptions );
			$process2 = new Process( $command );
			$process2->setTimeout( 30 );
			$processes->attach( $process2, $process1 );
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
		$this->parallelism = min( RepoNgCommand::MAX_CONNECTIONS, $this->parallelism );
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
				$branch = $repo['branch'] ?? 'master';
				$command =
					"cd '$name'; git add .; if ! git diff --cached --quiet; " .
					"then git commit -m '$message'; " .
					"git rebase 'origin/$branch' && git push origin '$branch'; fi";
			} elseif ( $repo['type'] === 'wmgerrit' ) {
				$branch = $repo['branch'] ?? 'master';
				$command =
					"cd '$name'; git add .; if ! git diff --cached --quiet; " .
					"then git commit -m '$message'; " .
					"git rebase 'origin/$branch' && git review -r origin -t L10n; fi";
			} elseif ( $repo['type'] === 'svn' ) {
				$extra = '';
				if ( isset( $repo['svn-add-options'] ) ) {
					foreach ( (array)$repo['svn-add-options'] as $option ) {
						$extra .= " --config-option '$option'";
					}
				}

				$command =
					"cd '$name'; " .
					"svn add --force * --auto-props --parents --depth infinity -q$extra; " .
					"svn commit --message '$message'";
			} elseif ( $repo['type'] === 'bzr' ) {
				$branch = $repo['branch'] ?? 'master';
				$command = "cd '$name'; bzr add .;bzr commit -m '$message'";
			} else {
				throw new RuntimeException( 'Unknown repo type' );
			}

			$process = new Process( $command );
			$process->setTimeout( 120 );
			$process->setWorkingDirectory( $base );
			$processes->attach( $process );
		}

		$this->runParallelWithOutput( $processes, $output );

		// Merge patch sets submitted to Wikimedia's Gerrit.
		$mergePattern = $config[ 'auto-merge' ] ?? false;
		if ( $mergePattern ) {
			$command = $this->bindir . "/merge-wmgerrit-patches '$mergePattern'";
			echo $command . "\n";
			$mergeProcess = new Process( $command );
			$mergeProcess->setTimeout( 600 );
			$mergeProcess->mustRun();
		}
	}
}

class ListCommand extends RepoNgCommand {
	protected function configure() {
		parent::configure();
		$this->setName( 'list' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		unset( $this->config[ '@meta' ] );
		echo implode( "\n", array_keys( $this->config ) ) . "\n";
	}
}

$application = new Application();
$application->add( new UpdateCommand() );
$application->add( new ExportCommand() );
$application->add( new CommitCommand() );
$application->add( new ListCommand() );
$application->run();
