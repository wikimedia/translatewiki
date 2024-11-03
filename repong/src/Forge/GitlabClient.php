<?php
declare( strict_types=1 );

namespace Translatewiki\RepoNg\Forge;

use DateTime;
use Exception;
use Gitlab\Client;
use Gitlab\Exception\RuntimeException;
use RangeException;

class GitlabClient implements ForgeClient {
	/** @var Client */
	private $client;

	public function __construct( string $token, ?string $url ) {
		$this->client = new Client();
		if ( $url ) {
			$this->client->setUrl( $url );
		}
		$this->client->authenticate( $token, Client::AUTH_HTTP_TOKEN );
	}

	public function createPullRequest(
		PullRequestSpecifier $pr,
		string $title,
		?string $body = null
	): PullRequestResponse {
		$isNew = true;
		$mergeRequestId = null;

		try {
			$response = $this->client->mergeRequests()->create(
				$pr->getRepositoryOwner() . '/' . $pr->getRepositoryName(),
				$pr->getHead(),
				$pr->getBase(),
				$title,
				[
					'description' => $body,
				]
			);
			$mergeRequestId = (int)$response['iid'];
		} catch ( RuntimeException $e ) {
			if (
				$e->getCode() === 409 &&
				strpos( $e->getMessage(), 'merge request already exists' ) !== false
			) {
				$isNew = false;
				if ( preg_match( '~!([0-9]+)$~', $e->getMessage(), $matches ) ) {
					$mergeRequestId = (int)$matches[1];
				}
			} else {
				throw $e;
			}
		}

		return new GitlabPullRequestResponse( $pr, $this, $isNew, $mergeRequestId );
	}

	public function getPullRequestCreationTime(
		PullRequestSpecifier $pr,
		int $mergeRequestId
	): DateTime {
		$response = $this->client->mergeRequests()->show(
			$pr->getRepositoryOwner() . '/' . $pr->getRepositoryName(),
			$mergeRequestId
		);

		try {
			return new DateTime( $response['created_at'] );
		} catch ( Exception $e ) {
			throw new RangeException(
				"Unable to create a DateTime from '{$response['created_at']}'"
			);
		}
	}
}
