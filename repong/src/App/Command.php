<?php

namespace Translatewiki\RepoNg\App;

use RuntimeException;
use SplObjectStorage;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

abstract class Command extends SymfonyCommand {
	protected const MAX_CONNECTIONS = 4;

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
			throw new RuntimeException( "Cannot find configuration: $configFile" );
		}

		// FIXME: This project should be self contained
		$binpath = __DIR__ . '/../../../bin';
		$this->bindir = realpath( __DIR__ . '/../../../bin' );
		if ( $this->bindir === false ) {
			throw new RuntimeException( "$binpath does not exist" );
		}

		$yaml = file_get_contents( $configFile );
		$this->config = yaml_parse( $yaml );

		$cores = preg_match_all( '/^processor/m', file_get_contents( '/proc/cpuinfo' ) );
		if ( $cores ) {
			$this->parallelism = $cores;
		}

		$variantFile = "$base/REPONG-VARIANT";
		if ( file_exists( $variantFile ) ) {
			$this->defaultVariant = trim( file_get_contents( $variantFile ) );
		}
	}

	protected function getBase(): ?string {
		return $this->base;
	}

	protected function findBase( $configName ): ?string {
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
			$process = Process::fromShellCommandline( $generator );
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
		$replacer = static function ( $array ) use ( &$replacer, $targetVariant ) {
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

	protected function buildCommandline( $command, $options ): string {
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
			static function ( $process ) use ( $output ) {
				$output->writeln( $process->getCommandLine(), OutputInterface::VERBOSITY_VERBOSE );
			},
			static function ( $process, $exception = null ) use ( $output ) {
				if ( $process->isSuccessful() ) {
					$processOutput = trim( $process->getOutput() );
					$errorOutput = trim( $process->getErrorOutput() );
					if ( $processOutput . $errorOutput ) {
						$formatter = $output->getFormatter();
						$command = $formatter->escape( $process->getCommandLine() );
						$output->writeln( "<options=bold>$command</>" );
						if ( $processOutput ) {
							$output->writeln( $formatter->escape( $processOutput ) );
						}
						if ( $errorOutput ) {
							$output->writeln( '<comment>' . $formatter->escape( $errorOutput ) . '</>' );
						}
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
				[ $ok, $process ] = self::getNextExecutableProcessIndex( $queue );

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

	private static function getNextExecutableProcessIndex( $queue ): array {
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

	protected function getGenericRepositoryType( string $type ): string {
		$map = [
			'github' => 'git',
			'gitlab' => 'git',
			'wmgerrit' => 'git',
		];

		return $map[$type] ?? $type;
	}

	protected function getForgeType( array $repo ): string {
		// This can potentially automatically determine the type from the URL in the future
		$map = [ 'wmgerrit' => 'wikimedia-gerrit' ];

		return $map[ $repo[ 'type' ] ] ?? 'none';
	}
}
