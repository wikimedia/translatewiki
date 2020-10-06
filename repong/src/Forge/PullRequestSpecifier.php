<?php
declare( strict_types=1 );

namespace Translatewiki\RepoNg\Forge;

/** Specifies a pull request. */
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

	public function getRepositoryOwner(): string {
		return $this->repositoryOwner;
	}

	public function getRepositoryName(): string {
		return $this->repositoryName;
	}

	/** Source branch */
	public function getBase(): string {
		return $this->base;
	}

	/** Target branch */
	public function getHead(): string {
		return $this->head;
	}
}
