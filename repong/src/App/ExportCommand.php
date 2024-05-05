<?php

namespace Translatewiki\RepoNg\App;

use SplObjectStorage;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ExportCommand extends Command {
	protected function configure() {
		parent::configure();
		$this->setName( 'export' );
		$this->setDescription( 'Updates translation files' );
		$this->addArgument( 'project', InputArgument::REQUIRED );
		$this->addOption( 'variant', null, InputOption::VALUE_REQUIRED );
		$this->addOption( 'filter', null, InputOption::VALUE_REQUIRED );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$project = $input->getArgument( 'project' );
		$variant = $input->getOption( 'variant' ) ?: $this->defaultVariant;
		$filter = $input->getOption( 'filter' );
		$config = $this->getConfig( $project, $variant );
		$exporter = $this->config['@meta']['export'];
		$expander = $this->config['@meta']['expand'];

		$defaultOptions = [
			'group' => null,
			'quiet' => true,
			'lang' => '*',
			'always-export-languages' => 'qqq',
			'never-export-languages' => $config['no-export-languages'] ?? null,
			'skip-source-language' => true,
			'threshold' => 25,
			'target' => $this->getBase(),
		];

		if ( isset( $config['always-export-languages'] ) ) {
			// Append message documentation by default. no-export-languages can override it
			$defaultOptions['always-export-languages'] = $config['always-export-languages'] . ',qqq';
		}

		if ( isset( $config['export-threshold'] ) ) {
			$defaultOptions['threshold'] = (int)$config['export-threshold'];
		}

		$groupSpec = $filter ?? $config[ 'group' ];
		$command = "$expander '$groupSpec'";
		$process = new Process( $command );
		$process->setTimeout( 10 );
		$process->mustRun();
		$groupsOutput = trim( $process->getOutput() );

		if ( $groupsOutput === '' ) {
			$formatter = $output->getFormatter();
			$msg = sprintf(
				"<options=bold>%s</>\n<error>No exportable groups found for pattern '%s'</>\n",
				$formatter->escape( $project ),
				$formatter->escape( $config[ 'group' ] )
			);
			$output->write( $msg );
			return;
		}

		$groups = explode( "\n", $groupsOutput );

		$processes = new SplObjectStorage();

		foreach ( $groups as $group ) {
			$jobOptions = $defaultOptions;
			$jobOptions[ 'group' ] = $group;
			$command = $this->buildCommandline( $exporter, $jobOptions );
			$process1 = new Process( $command );
			$process1->setTimeout( 300 );
			$processes->attach( $process1 );
		}

		$this->runParallelWithOutput( $processes, $output );
	}
}
