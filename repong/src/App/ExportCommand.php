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
