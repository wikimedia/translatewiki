<?php
declare( strict_types=1 );

namespace Translatewiki\RepoNg\Tests;

use PHPUnit\Framework\TestCase;
use SplObjectStorage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Translatewiki\RepoNg\App\CommitCommand;

/**
 * @covers \Translatewiki\RepoNg\App\CommitCommand
 * @covers \Translatewiki\RepoNg\App\Command
 */
class CommitCommandTest extends TestCase {
	/**
	 * @dataProvider provideRoutingCases
	 */
	public function testForgejoRouting( array $settings, ?string $filter, ?string $expected ): void {
		$command = new class extends CommitCommand {
			public array $repo;
			public array $processes = [];

			public function initialize( InputInterface $input, OutputInterface $output ) {
				$this->base = sys_get_temp_dir();
				$this->config = [ 'example' => [ 'repos' => [ 'example' => $this->repo ] ] ];
			}

			protected function runParallelWithOutput( SplObjectStorage $processes, OutputInterface $output ) {
				$this->processes = iterator_to_array( $processes );
			}
		};
		$command->repo = $settings + [
			'type' => 'forgejo',
			'url' => 'https://codeberg.org/example/example.git',
			'branch' => 'main'
		];
		$tester = new CommandTester( $command );
		$args = [ 'project' => 'example' ];
		if ( $filter !== null ) {
			$args['--filter'] = $filter;
		}
		$this->assertSame( 0, $tester->execute( $args ) );
		// An accidental API call would report an error (there is no checkout/token).
		$this->assertSame( '', $tester->getDisplay() );
		$this->assertCount( $expected === null ? 0 : 1, $command->processes );
		if ( $expected !== null ) {
			$this->assertStringContainsString( $expected, $command->processes[0]->getCommandLine() );
		}
	}

	public static function provideRoutingCases(): array {
		return [
			'AGit' => [ [], null, 'HEAD:refs/for/main' ],
			'direct push' => [ [ 'type' => 'git' ], null, "git push origin 'main'" ],
			'push branch' => [ [ 'type' => 'git', 'push-branch' => 'translations' ], null, "HEAD:'translations'" ],
			'filtered out' => [ [], 'other', null ]
		];
	}
}
