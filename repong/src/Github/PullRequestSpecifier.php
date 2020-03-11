<?php

namespace Translatewiki\RepoNg\Github;

/**
 * Uniquely specifies a GitHub pull request.
 */
class PullRequestSpecifier {
	private $repositoryOwner;
	private $repositoryName;
	private $base;
	private $head;

	public function __construct(
		string $repositoryOwner,
		string $repositoryName,
		string $base,
		string $head
	) {
		$this->repositoryOwner = $repositoryOwner;
		$this->repositoryName = $repositoryName;
		$this->base = $base;
		$this->head = $head;
	}

	/**
	 * @return string
	 */
	public function getRepositoryOwner(): string {
		return $this->repositoryOwner;
	}

	/**
	 * @return string
	 */
	public function getRepositoryName(): string {
		return $this->repositoryName;
	}

	/**
	 * @return string
	 */
	public function getBase(): string {
		return $this->base;
	}

	/**
	 * @return string
	 */
	public function getHead(): string {
		return $this->head;
	}
}
