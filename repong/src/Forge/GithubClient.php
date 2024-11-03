<?php
declare( strict_types=1 );

namespace Translatewiki\RepoNg\Forge;

use DateTime;
use Exception;
use Github\Client;
use Github\Exception\ValidationFailedException;
use RangeException;
use RuntimeException;

class GithubClient implements ForgeClient {
	/** @var Client */
	private $client;

	public function __construct( string $token ) {
		$this->client = new Client();
		$this->client->authenticate( $token, null, Client::AUTH_ACCESS_TOKEN );
	}

	public function createPullRequest(
		PullRequestSpecifier $pr,
		string $title,
		?string $body = null
	): PullRequestResponse {
		$isNew = true;
		try {
			$this->client->api( 'pull_request' )->create(
				$pr->getRepositoryOwner(),
				$pr->getRepositoryName(),
				[
					'base' => $pr->getBase(),
					'head' => $pr->getHead(),
					'title' => $title,
					'body' => $body,
					'maintainer_can_modify' => true,
				]
			);
		} catch ( ValidationFailedException $e ) {
			$code = $e->getCode();
			$msg = $e->getMessage();

			if ( $code === 422 && strpos( $msg, 'pull request already exists' ) !== false ) {
				$isNew = false;
			} else {
				throw $e;
			}
		}

		return new GithubPullRequestResponse( $pr, $this, $isNew );
	}

	public function getPullRequestCreationTime( PullRequestSpecifier $pr ): DateTime {
		$pullRequests = $this->client->api( 'pull_request' )->all(
			$pr->getRepositoryOwner(),
			$pr->getRepositoryName(),
			[
				'status' => 'open',
				// Head needs user/org prefix.
				// See https://developer.github.com/v3/pulls/#list-pull-requests
				'head' => $pr->getRepositoryOwner() . ':' . $pr->getHead(),
				'base' => $pr->getBase(),
			]
		);

		$count = count( $pullRequests );
		if ( $count !== 1 ) {
			throw new RuntimeException( "Expected one pull request, got $count" );
		}

		try {
			return new DateTime( $pullRequests[0]['created_at'] );
		} catch ( Exception $e ) {
			throw new RangeException(
				"Unable to create a DateTime from '{$pullRequests[0]['created_at']}'"
			);
		}
	}
}
