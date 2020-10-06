<?php
declare( strict_types=1 );

namespace Translatewiki\RepoNg\Forge;

use DateTime;

interface PullRequestResponse {
	public function isNew(): bool;

	public function getCreationTime(): DateTime;
}
