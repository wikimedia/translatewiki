<?php
declare( strict_types=1 );

namespace Translatewiki\RepoNg\Forge;

use RuntimeException;
use Symfony\Component\Process\Process;
use Translatewiki\RepoNg\App\CommitCommand;

/** Submit translation commits through Forgejo's Git-only pull request workflow. */
class AgitSubmission {

	public static function isEnabled( array $repo ): bool {
		return $repo['type'] === 'forgejo';
	}

	public static function getPushCommand(
		array $repo,
		?string $backportBranch = null
	): string {
		if ( isset( $repo['push-branch'] ) || isset( $repo['pull-branch'] ) ) {
			throw new RuntimeException( 'Forgejo PR delivery does not use push-branch or pull-branch' );
		}
		if ( $backportBranch !== null ) {
			throw new RuntimeException( 'Backport committing is not supported for Forgejo PR delivery' );
		}
		$title = isset( $repo['pr-title-prefix'] ) ? $repo['pr-title-prefix'] . ' ' : '';
		$title .= CommitCommand::MESSAGE;
		// Git push options cannot contain newlines or NUL bytes.
		if ( strpbrk( $title, "\r\n\0" ) !== false ) {
			throw new RuntimeException( 'AGit title must be a single-line string without NUL bytes' );
		}

		$branch = $repo['branch'] ?? 'master';
		return ( new Process( [
			'git', 'push', 'origin', "HEAD:refs/for/$branch",
			'-o', 'topic=translatewiki', '-o', 'force-push=true',
			'-o', "title=$title", '-o', 'description=' . CommitCommand::PR_MESSAGE
		] ) )->getCommandLine();
	}
}
