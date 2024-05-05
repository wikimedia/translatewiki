<?php
declare( strict_types = 1 );

namespace Translatewiki\RepoNg\App;

use FilesystemIterator;
use SplFileInfo;
use SplObjectStorage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Traversable;

class PurgeCommand extends Command {
	protected function configure() {
		parent::configure();
		$this->setName( 'purge' );
		$this->setDescription( 'Purge unused project directories' );
		$this->addOption(
			'really',
			null,
			InputOption::VALUE_NONE,
			'Actually delete, instead of listing what to delete'
		);
	}

	protected function execute( InputInterface $input, OutputInterface $output ): int {
		$knownPaths = [];
		foreach ( array_keys( $this->config ) as $project ) {
			if ( $project === '@meta' ) {
				continue;
			}

			$config = $this->getConfig( $project, $this->defaultVariant );
			$knownPaths = array_merge( $knownPaths, array_keys( $config['repos'] ) );
		}

		$knownPathsString = implode( "\n", $knownPaths );
		$really = $input->getOption( 'really' );

		if ( !$really ) {
			$output->writeln( 'Following directories would be deleted: (add --really to delete)' );
		}

		$processes = new SplObjectStorage();

		foreach ( self::getUnknownDirectories( $this->getBase(), '', $knownPathsString ) as $path ) {
			$output->writeln( $path );
			if ( $really ) {
				$process = Process::fromShellCommandline( "rm -rf '$path'" );
				$process->setWorkingDirectory( $this->getBase() );
				$processes->attach( $process );
			}
		}

		$this->runParallelWithOutput( $processes, $output );

		return 0;
	}

	private static function getUnknownDirectories(
		string $base, string $sub, string $knownPathsString
	): Traversable {
		// This code assumes project directories are not nested, e.g. B must not be under A/B
		// within a project. This checks subdirectories if necessary.
		//
		// Example:
		// Project Apple uses single directory. Project Banana has multiple repositories like
		// Banana/A, Banana/C.
		//
		// This function would be called with known directories Apple, Banana/A, Banana/C (newline
		// separated). Let's assume the top level directories are Apple, Banana, Cucumber.
		// Apple is not returned, because it is a known directory. Banana is a prefix for
		// multiple entries, so this function calls itself recursively to check Banana. Let's
		// assume it contains A, B, C. A and C are not returned because they match, but Banana/B
		// is returned because it doesn't match. Finally it continues with Cucumber, which is
		// also returned because it does not match.

		$fsIterator = new FilesystemIterator( "$base/$sub" );
		/** @var SplFileInfo $value */
		foreach ( $fsIterator as $value ) {
			if ( $value->isFile() ) {
				continue;
			}

			$subPath = "$sub{$value->getFilename()}";

			$subPathPattern = '/^' . preg_quote( $subPath, '/' ) . '(\/.+)?$/m';
			$match = [];
			preg_match( $subPathPattern, $knownPathsString, $match );
			if ( $match !== [] ) {
				// Prefix match, recurse
				if ( ( $match[1] ?? '' ) !== '' ) {
					yield from self::getUnknownDirectories(
						$base,
						"$sub" . $value->getFilename() . '/',
						$knownPathsString
					);
				}
			} else {
				yield $subPath;
			}
		}
	}
}
