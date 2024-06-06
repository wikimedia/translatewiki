<?php

namespace Translatewiki\RepoNg\App;

use RuntimeException;
use SplObjectStorage;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class UpdateCommand extends Command {
	protected function configure() {
		parent::configure();
		$this->setName( 'update' );
		$this->setDescription( 'Updates repositories from upstream' );
		$this->addArgument( 'project', InputArgument::REQUIRED );
		$this->addOption( 'variant', null, InputOption::VALUE_REQUIRED );
		$this->addOption( 'filter', null, InputOption::VALUE_REQUIRED );
	}

	protected function execute( InputInterface $input, OutputInterface $output ): int {
		$this->parallelism = min( self::MAX_CONNECTIONS, $this->parallelism );
		$project = $input->getArgument( 'project' );
		$variant = $input->getOption( 'variant' ) ?: $this->defaultVariant;
		$filter = $input->getOption( 'filter' );
		$config = $this->getConfig( $project, $variant );
		$defaultConfig = $this->getConfig( $project, null );
		$meta = $this->getConfig( '@meta', $variant );
		$base = $this->getBase();
		$bindir = $this->bindir;

		$processes = new SplObjectStorage();
		foreach ( $config['repos'] as $name => $repo ) {
			if ( $filter !== null && !fnmatch( $filter, $name ) ) {
				continue;
			}

			$genericType = $this->getGenericRepositoryType( $repo['type'] );
			$branch = $repo['branch'] ?? 'master';

			if ( $genericType === 'git' ) {
				// Check if we can use state synchronization for this repo.
				// Without state synchronization, the repository we make commits
				// could be ahead of the state that has been processed in the wiki.
				// With state synchronization we ensure we do not overwrite any
				// changes that have been made in the between.
				$stateDir = $meta[ 'state-directory' ] ?? false;
				$defaultConfigBranch = $defaultConfig[ 'repos' ][ $name ][ 'branch' ] ?? 'master';
				$branchCompatible = $branch === $defaultConfigBranch;
				$syncState = $stateDir && $branchCompatible && !isset( $repo[ 'no-state-sync' ] );

				// Determine the state to use, if possible
				$state = "origin/$branch";
				if ( $syncState ) {
					$process = Process::fromShellCommandline( 'git log --pretty="%H" -n 1' );
					$process->setWorkingDirectory( "$stateDir/$name" );
					$process->setTimeout( 5 );
					$process->run();
					if ( $process->isSuccessful() ) {
						$state = trim( $process->getOutput() );
					} else {
						$output->writeln( "Unable to synchronize the state for repository: $name" );
						// Skip processing this repository
						continue;
					}
				}

				$forge = $this->getForgeType( $repo );
				$command = "$bindir/clupdate-git-repo '{$repo['url']}' '$base/$name' '$branch' '$state' '$forge'";
			} elseif ( $genericType === 'svn' ) {
				$command = "$bindir/clupdate-svn-repo '{$repo['url']}' '$base/$name'";
			} elseif ( $genericType === 'bzr' ) {
				$command = "$bindir/clupdate-bzr-repo '{$repo['url']}' '$base/$name' '$branch'";
			} else {
				throw new RuntimeException( "Unknown repo type '$genericType' for repository: $name" );
			}

			$process = Process::fromShellCommandline( $command );
			$process->setTimeout( 450 );
			$processes->attach( $process );
		}

		$this->runParallelWithOutput( $processes, $output );

		return 0;
	}
}
