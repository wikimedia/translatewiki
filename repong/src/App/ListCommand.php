<?php

namespace Translatewiki\RepoNg\App;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command {
	protected function configure() {
		parent::configure();
		$this->setName( 'list-projects' );
		$this->setDescription( 'Lists all projects defined in repoconfig.yaml' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ): int {
		unset( $this->config[ '@meta' ] );
		echo implode( "\n", array_keys( $this->config ) ) . "\n";

		return 0;
	}
}
