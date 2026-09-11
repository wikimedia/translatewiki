<?php
declare( strict_types=1 );

namespace Translatewiki\RepoNg\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Process\Process;
use Translatewiki\RepoNg\Forge\AgitSubmission;

/**
 * @covers \Translatewiki\RepoNg\Forge\AgitSubmission
 */
class AgitSubmissionTest extends TestCase {
	private $directory;

	protected function setUp(): void {
		$this->directory = sys_get_temp_dir() . '/repong-agit-' . bin2hex( random_bytes( 8 ) );
		mkdir( $this->directory );
		// Record argument boundaries and simulate Git without contacting any forge.
		file_put_contents( $this->directory . '/git', <<<'PHPSTUB'
#!/usr/bin/env php
<?php
file_put_contents( getenv( 'CALLS' ), json_encode( array_slice( $argv, 1 ) ) . "\n", FILE_APPEND );
PHPSTUB
		);
		chmod( $this->directory . '/git', 0700 );
	}

	protected function tearDown(): void {
		foreach ( glob( $this->directory . '/*' ) as $file ) {
			unlink( $file );
		}
		rmdir( $this->directory );
	}

	public function testOptIn(): void {
		$this->assertTrue( AgitSubmission::isEnabled( [ 'type' => 'forgejo' ] ) );
		$this->assertFalse( AgitSubmission::isEnabled( [ 'type' => 'git' ] ) );
		$this->assertFalse( AgitSubmission::isEnabled( [ 'type' => 'github', 'pull-branch' => 'twn' ] ) );
	}

	public function testPushCommand(): void {
		$repo = [
			'type' => 'forgejo',
			'branch' => 'release/stable',
			'pr-title-prefix' => 'Bot\'s "$(echo unsafe)"'
		];
		$process = Process::fromShellCommandline( AgitSubmission::getPushCommand( $repo ) );
		$process->setEnv( [
			'PATH' => $this->directory . ':' . getenv( 'PATH' ),
			'CALLS' => $this->directory . '/calls'
		] );
		$process->mustRun();
		$this->assertSame( [
			'push', 'origin', 'HEAD:refs/for/release/stable',
			'-o', 'topic=translatewiki', '-o', 'force-push=true',
			'-o', 'title=' . $repo['pr-title-prefix'] .
				' Localisation updates from https://translatewiki.net.',
			'-o', 'description=Translation updates'
		], json_decode( file_get_contents( $this->directory . '/calls' ), true ) );
	}

	/**
	 * @dataProvider provideInvalidConfiguration
	 */
	public function testInvalidConfiguration( array $extra, ?string $backport ): void {
		$this->expectException( RuntimeException::class );
		AgitSubmission::getPushCommand(
			$extra + [ 'type' => 'forgejo' ],
			$backport
		);
	}

	public static function provideInvalidConfiguration(): array {
		return [
			'push-branch' => [ [ 'push-branch' => 'other' ], null ],
			'backport' => [ [], 'stable' ],
			'multiline title' => [ [ 'pr-title-prefix' => "first\nsecond" ], null ],
			'NUL title' => [ [ 'pr-title-prefix' => "twn\0invalid" ], null ],
			'pull-branch' => [ [ 'pull-branch' => 'twn' ], null ]
		];
	}
}
