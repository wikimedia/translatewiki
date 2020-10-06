<?php
declare( strict_types=1 );

namespace Translatewiki\RepoNg\Forge;

use DateTime;

class GithubPullRequestResponse implements PullRequestResponse {
	/** @var PullRequestSpecifier */
	private $pr;
	/** @var GithubClient */
	private $client;
	/** @var bool */
	private $isNew;

	public function __construct( PullRequestSpecifier $pr, GithubClient $client, bool $isNew ) {
		$this->pr = $pr;
		$this->client = $client;
		$this->isNew = $isNew;
	}

	public function isNew(): bool {
		return $this->isNew;
	}

	public function getCreationTime(): DateTime {
		return $this->client->getPullRequestCreationTime( $this->pr );
	}
}
