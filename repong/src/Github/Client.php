<?php

namespace Translatewiki\RepoNg\Github;

use DateTime;
use Exception;
use Github\Exception\ValidationFailedException;
use RangeException;
use RuntimeException;

class Client {
	private $client;

	public function __construct( string $token ) {
		$this->client = new \Github\Client();
		$this->client->authenticate( $token, null, \Github\Client::AUTH_HTTP_TOKEN );
	}

	/**
	 * @param PullRequestSpecifier $pr
	 * @param string $title
	 * @param string|null $body
	 * @return bool True on success, false if pull requests already exists.
	 * @throws ValidationFailedException
	 */
	public function createPullRequest(
		PullRequestSpecifier $pr,
		string $title,
		string $body = null
	): bool {
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

			return true;
		} catch ( ValidationFailedException $e ) {
			$code = $e->getCode();
			$msg = $e->getMessage();

			if ( $code === 422 && strpos( $msg, 'pull request already exists' ) !== false ) {
				return false;
			}

			throw $e;
		}
	}

	/**
	 * @param PullRequestSpecifier $pr
	 * @return DateTime
	 */
	public function getPullRequestCreationTime( PullRequestSpecifier $pr ): DateTime {
		$pullRequests = $this->client->api( 'pull_request' )->all(
			$pr->getRepositoryOwner(),
			$pr->getRepositoryName(),
			[
				'status' => 'open',
				'head' => $pr->getHead(),
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
			throw new RangeException( "Unable to create a DateTime from '{$pullRequests[0]['created_at']}'" );
		}
	}
}
