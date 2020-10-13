<?php
declare( strict_types=1 );

namespace Translatewiki\RepoNg\Forge;

use DateTime;
use RuntimeException;

class GitlabPullRequestResponse implements PullRequestResponse {
	/** @var PullRequestSpecifier */
	private $pr;
	/** @var GitlabClient */
	private $client;
	/** @var bool */
	private $isNew;
	/** @var int */
	private $mergeRequestId;

	public function __construct(
		PullRequestSpecifier $pr,
		GitlabClient $client,
		bool $isNew,
		int $mergeRequestId
	) {
		$this->pr = $pr;
		$this->client = $client;
		$this->isNew = $isNew;
		$this->mergeRequestId = $mergeRequestId;
	}

	public function isNew(): bool {
		return $this->isNew;
	}

	public function getCreationTime(): DateTime {
		if ( $this->mergeRequestId === null ) {
			throw new RuntimeException( 'Merge request id is missing' );
		}

		return $this->client->getPullRequestCreationTime( $this->pr, $this->mergeRequestId );
	}
}
