<?php

namespace Translatewiki\RepoNg\App;

use RuntimeException;
use SplObjectStorage;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class StatusCommand extends Command {
	protected function configure() {
		parent::configure();
		$this->setName( 'status' );
		$this->addArgument( 'project', InputArgument::REQUIRED );
		$this->addOption( 'variant', null, InputOption::VALUE_REQUIRED );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$project = $input->getArgument( 'project' );
		$variant = $input->getOption( 'variant' ) ?: $this->defaultVariant;
		$config = $this->getConfig( $project, $variant );
		$base = $this->getBase();

		$processes = new SplObjectStorage();

		foreach ( $config['repos'] as $name => $repo ) {
			$type = $repo['type'];

			$command = "cd '$name' && ";
			if ( in_array( $type, [ 'git', 'github', 'wmgerrit' ] ) ) {
				$command .= "git status -s";
			} elseif ( $type === 'svn' ) {
				$command .= "svn status";
			} elseif ( $type === 'bzr' ) {
				$command .= "bzr status";
			} else {
				throw new RuntimeException( "Unknown repo type '$type' for repository: $name" );
			}

			$process = new Process( $command );
			$process->setTimeout( 10 );
			$process->setWorkingDirectory( $base );
			$processes->attach( $process );
		}

		$this->runParallelWithOutput( $processes, $output );
	}
}
