<?php
declare( strict_types=1 );

namespace Translatewiki\RepoNg\Forge;

interface ForgeClient {
	public function createPullRequest(
		PullRequestSpecifier $pr,
		string $title,
		?string $body = null
	): PullRequestResponse;
}
