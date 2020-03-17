<?php

namespace Translatewiki\RepoNg\App;

use DomainException;
use RuntimeException;
use SplObjectStorage;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Translatewiki\RepoNg\Github\Client as GithubClient;
use Translatewiki\RepoNg\Github\PullRequestSpecifier as GithubPullRequestSpecifier;

class CommitCommand extends Command {
	const MESSAGE = 'Localisation updates from https://translatewiki.net.';

	/**
	 * @var GithubClient
	 */
	private $githubClient;

	protected function configure() {
		parent::configure();
		$this->setName( 'commit' );
		$this->setDescription( 'Creates commits and pushes them to upstream' );
		$this->addArgument( 'project', InputArgument::REQUIRED );
		$this->addOption( 'variant', null, InputOption::VALUE_REQUIRED );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$this->parallelism = min( self::MAX_CONNECTIONS, $this->parallelism );
		$project = $input->getArgument( 'project' );
		$variant = $input->getOption( 'variant' ) ?: $this->defaultVariant;
		$config = $this->getConfig( $project, $variant );
		$message = self::MESSAGE;
		$base = $this->getBase();

		$processes = new SplObjectStorage();

		foreach ( $config['repos'] as $name => $repo ) {
			$type = $repo['type'];

			if ( $type === 'git' || $type === 'github' ) {
				if ( $repo['push-branch'] ?? $repo['pull-branch'] ?? false ) {
					// This will use the default/source branch to base the commit on, and then push
					// to a different remote branch. This is useful when for example, the project
					// uses a pull-request model to review the commit.
					//
					// Subsequent commits will automatically re-create the branch (if it was merged
					// since), or force-update the existing branch (and associated pull request).
					// Note: Do NOT use 'branch|export' and 'push-branch' or 'pull-branch' together.
					$destBranch = $repo['push-branch'] ?? $repo['pull-branch'];
					$srcBranch = $repo['branch'] ?? 'master';
					$command =
						"cd '$name'; git add .; if ! git diff --cached --quiet; " .
						"then git commit -m '$message'; " .
						"git rebase 'origin/$srcBranch' && git push --force origin HEAD:'$destBranch'; fi";
				} else {
					$branch = $repo['branch'] ?? 'master';
					$command =
						"cd '$name'; git add .; if ! git diff --cached --quiet; " .
						"then git commit -m '$message'; " .
						"git rebase 'origin/$branch' && git push origin '$branch'; fi";
				}
			} elseif ( $type === 'wmgerrit' ) {
				$branch = $repo['branch'] ?? 'master';
				$command =
					"cd '$name'; git add .; if ! git diff --cached --quiet; " .
					"then git commit -m '$message'; " .
					"git rebase 'origin/$branch' && git review -r origin -t L10n; fi";
			} elseif ( $type === 'svn' ) {
				$extra = '';
				if ( isset( $repo['svn-add-options'] ) ) {
					foreach ( (array)$repo['svn-add-options'] as $option ) {
						$extra .= " --config-option '$option'";
					}
				}

				$command =
					"cd '$name'; " .
					"svn add --force * --auto-props --parents --depth infinity -q$extra; " .
					"svn commit --message '$message'";
			} elseif ( $type === 'bzr' ) {
				$branch = $repo['branch'] ?? 'master';
				$command = "cd '$name'; bzr add .;bzr commit -m '$message'";
			} else {
				throw new RuntimeException( "Unknown repo type '$type' for repository: $name" );
			}

			$process = new Process( $command );
			$process->setTimeout( 300 );
			$process->setWorkingDirectory( $base );
			$processes->attach( $process );
		}

		$this->runParallelWithOutput( $processes, $output );

		// Merge patch sets submitted to Wikimedia's Gerrit.
		$mergePattern = $config[ 'auto-merge' ] ?? false;
		if ( $mergePattern ) {
			$command = $this->bindir . "/merge-wmgerrit-patches '$mergePattern'";
			echo $command . "\n";
			$mergeProcess = new Process( $command );
			$mergeProcess->setTimeout( 600 );
			$mergeProcess->mustRun();
		}

		$this->makeGithubPullRequests( $config, $base, $output );
	}

	private function getTimeSince( DateTime $dt ): string {
		$interval = $dt->diff( new DateTime() );

		$unitsDisplayed = 0;
		$duration = '';
		foreach ( [ 'y', 'm', 'd', 'h', 'i', 's' ] as $unit ) {
			$value = $interval->$unit;

			if ( !$value ) {
				continue;
			}

			$unitsDisplayed++;
			if ( $unitsDisplayed === 1 ) {
				$duration = "$value $unit";
			} else {
				$duration .= " and $value $unit";
				break;
			}
		}

		return $duration;
	}

	private function getGitHubPullRequestSpecifier( array $config ): GithubPullRequestSpecifier {
		$repo = $this->getOwnerAndRepoFromGithubUrl( $config['url'] );
		return new GithubPullRequestSpecifier(
			$repo['owner'],
			$repo['repo'],
			$config['branch'] ?? 'master',
			$config['pull-branch']
		);
	}

	private function getOwnerAndRepoFromGithubUrl( string $url ): array {
		// Supports the following formats:
		// * https://github.com/owner/repo.git
		// * https://github.com/owner/repo
		// * git@github.com:owner/repo.git
		// * git@github.com:owner/repo

		$matches = [];
		preg_match( '~[:/](?<owner>[^/]+)/(?<repo>[^/.]+?)(\.git)?$~', $url, $matches );

		$ret = [
			'owner' => $matches['owner'] ?? '',
			'repo' => $matches['repo'] ?? '',
		];

		return $ret;
	}

	private function getGithubClient(): GithubClient {
		if ( $this->githubClient ) {
			return $this->githubClient;
		}

		$token = getenv( 'L10NBOT_GITHUB_TOKEN' );
		if ( !$token ) {
			throw new RuntimeException( "Environment variable L10NBOT_GITHUB_TOKEN not set" );
		}

		$this->githubClient = new GithubClient( $token );
		return $this->githubClient;
	}

	/**
	 * Check whether there are changes for a pull request.
	 * @param string $repositoryPath
	 * @param string $branch
	 * @return bool
	 */
	private function hasChanges( string $repositoryPath, string $branch ): bool {
		$process = new Process( "git log origin/$branch..HEAD" );
		$process->setWorkingDirectory( $repositoryPath );
		$process->setTimeout( 5 );
		$process->run();
		if ( !$process->isSuccessful() ) {
			throw new RuntimeException( "Failed to check for changes" );
		}

		return trim( $process->getOutput() ) !== '';
	}

	/**
	 * @param array $config
	 * @param string $base
	 * @param OutputInterface $output
	 */
	private function makeGithubPullRequests(
		array $config,
		string $base,
		OutputInterface $output
	) {
		// Create pull requests in GitHub. Run this serially:
		// https://developer.github.com/v3/guides/best-practices-for-integrators/#dealing-with-rate-limits
		foreach ( $config['repos'] as $name => $repo ) {
			try {
				$outputMessage = $this->makeGithubPullRequest( $repo, "$base/$name" );
				$output->writeln( "$name: $outputMessage" );
			} catch ( Exception $e ) {
				$formatter = $output->getFormatter();
				$errorMessage = $formatter->escape( $e->getMessage() );
				$output->writeln( "<error>Unable to create GitHub pull request for $name</error>" );
				$output->writeln( '<error>' . $errorMessage . '</error>' );
			}
		}
	}

	/**
	 * @param array $repo
	 * @param string $repositoryPath
	 * @return string|null
	 */
	private function makeGithubPullRequest( array $repo, string $repositoryPath ): ?string {
		if ( !( $repo['pull-branch'] ?? false ) ) {
			return null;
		}

		if ( $repo['type'] !== 'github' ) {
			throw new DomainException( 'Pull requests are only supported for GitHub' );
		}

		$hasChanges = $this->hasChanges( $repositoryPath, $repo['branch'] ?? 'master' );
		if ( !$hasChanges ) {
			return null;
		}

		$pr = $this->getGitHubPullRequestSpecifier( $repo );
		// Do not construct client unless needed (env variable may not be set up)
		$client = $this->getGithubClient();
		$created = $client->createPullRequest( $pr, self::MESSAGE, 'Translation updates' );
		if ( !$created ) {
			$createdAt = $client->getPullRequestCreationTime( $pr );
			$duration = $this->getTimeSince( $createdAt );
			return "Updated unmerged pull request which has been open for $duration.\n";
		}
	}
}
