<?php
declare( strict_types = 1 );

namespace Translatewiki\RepoNg\App;

use RuntimeException;
use SplObjectStorage;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class BackportCommand extends Command {
	protected function configure() {
		parent::configure();
		$this->setName( 'backport' );
		$this->setDescription( 'Backports updates to other branches' );
		$this->addArgument( 'project', InputArgument::REQUIRED );
		$this->addOption( 'variant', null, InputOption::VALUE_OPTIONAL );
		$this->addOption( 'branch', null, InputOption::VALUE_REQUIRED );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$this->parallelism = min( self::MAX_CONNECTIONS, $this->parallelism );
		$project = $input->getArgument( 'project' );
		$variant = $input->getOption( 'variant' ) ?: $this->defaultVariant;
		$config = $this->getConfig( $project, $variant );
		$defaultConfig = $this->getConfig( $project );
		$base = $this->getBase();
		$bindir = $this->bindir;
		$backportBranch = $input->getOption( 'branch' );

		$meta = $this->getConfig( '@meta', $variant );
		$stateDir = $meta[ 'state-directory' ] ?? false;
		if ( !$stateDir ) {
			throw new RuntimeException( "Config option state-directory is required for backports" );
		}

		$processes = new SplObjectStorage();
		foreach ( $config['repos'] as $name => $repo ) {
			$repoDefaults = $defaultConfig['repos'][$name];
			$problem = $this->checkBackportSupport( $repo, $repoDefaults );
			if ( $problem ) {
				throw new RuntimeException( "Unable to backport repository $name: $problem" );
			}

			$state = "origin/$backportBranch";
			$forge = $this->getForgeType( $repo );
			$updateCommand = "$bindir/clupdate-git-repo '{$repo['url']}' '$base/$name' '$backportBranch' '$state' '$forge'";
			$backportCommand = $this->buildCommandline(
				$meta['backport'],
				[
					'source-path' => $stateDir,
					'target-path' => $base,
					'filter-path' => "$name/",
					'group' => $config['group'],
					'never-export-languages' => $config['no-export-languages'] ?? null,
				]
			);

			$branch = $repo['branch'] ?? 'master';
			$resetCommand = "$bindir/clupdate-git-repo '{$repo['url']}' '$base/$name' '$branch' 'origin/$branch' '$forge'";

			$command = "$updateCommand && $backportCommand || $resetCommand";

			$process = new Process( $command );
			$process->setTimeout( 60 );
			$processes->attach( $process );
		}

		$this->runParallelWithOutput( $processes, $output );
	}

	private function checkBackportSupport( array $repo, array $repoDefaults ): ?string {
		$genericType = $this->getGenericRepositoryType( $repo['type'] );
		if ( $genericType !== 'git' ) {
			return "Backporting is not supported for type '$genericType'";
		}

		if ( $repo['push-branch'] ?? $repo['pull-branch'] ?? false ) {
			return 'Backporting is not supported with push-branch or pull-branch';
		}

		$targetBranch = $repo['branch'] ?? 'master';
		$sourceBranch = $repoDefaults['branch'] ?? 'master';
		if ( $targetBranch !== $sourceBranch ) {
			return 'Backporting is not supported with divergent export branch';
		}

		return null;
	}
}
