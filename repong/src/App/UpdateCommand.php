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
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$this->parallelism = min( self::MAX_CONNECTIONS, $this->parallelism );
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
					$output->writeln( "Unable to synchronize the state for repository: $name" );
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
				throw new RuntimeException( "Unknown repo type '$type' for repository: $name" );
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
