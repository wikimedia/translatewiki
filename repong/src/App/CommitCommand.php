<?php

namespace Translatewiki\RepoNg\App;

use DateTime;
use Exception;
use RuntimeException;
use SplObjectStorage;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Translatewiki\RepoNg\Forge\ForgeFactory;
use Translatewiki\RepoNg\Forge\PullRequestSpecifier;

class CommitCommand extends Command {
	// For commit messages and pull request titles
	const MESSAGE = 'Localisation updates from https://translatewiki.net.';
	// For pull request descriptions
	const PR_MESSAGE = 'Translation updates';

	protected function configure() {
		parent::configure();
		$this->setName( 'commit' );
		$this->setDescription( 'Creates commits and pushes them to upstream' );
		$this->addArgument( 'project', InputArgument::REQUIRED );
		$this->addOption( 'variant', null, InputOption::VALUE_REQUIRED );
		$this->addOption( 'filter', null, InputOption::VALUE_REQUIRED );
		$this->addOption(
			'backport-branch',
			null,
			InputOption::VALUE_REQUIRED,
			'Override the default export branch for translation backports. ' .
				'This can make a mess if you accidentally push wrong content to wrong branch'
		);
	}

	protected function execute( InputInterface $input, OutputInterface $output ): int {
		$this->parallelism = min( self::MAX_CONNECTIONS, $this->parallelism );
		$project = $input->getArgument( 'project' );
		$variant = $input->getOption( 'variant' ) ?: $this->defaultVariant;
		$filter = $input->getOption( 'filter' );
		$config = $this->getConfig( $project, $variant );
		$backportBranch = $input->getOption( 'backport-branch' );
		$base = $this->getBase();

		$processes = new SplObjectStorage();

		foreach ( $config['repos'] as $name => $repo ) {
			if ( $filter !== null && !fnmatch( $filter, $name ) ) {
				// Unset so that we also skip pull requests
				unset( $config['repos'][$name] );
				continue;
			}

			$message = self::MESSAGE;

			if ( isset( $repo['commit-message-suffix'] ) ) {
				$message .= ' ' . $repo['commit-message-suffix'];
			}

			$type = $repo['type'];
			$genericType = $this->getGenericRepositoryType( $type );

			if ( $genericType === 'git' ) {
				$branch = $backportBranch ?? $repo['branch'] ?? 'master';

				if ( $type === 'wmgerrit' ) {
					$push = "git review -r origin -t L10n '$branch'";
				} elseif ( $repo['push-branch'] ?? $repo['pull-branch'] ?? false ) {
					if ( $backportBranch !== null ) {
						throw new RuntimeException(
							'Backport committing is not supported when using push-branch or pull-branch'
						);
					}

					// This will use the default/source branch to base the commit on, and then push
					// to a different remote branch. This is useful when for example, the project
					// uses a pull-request model to review the commit.
					//
					// Subsequent commits will automatically re-create the branch (if it was merged
					// since), or force-update the existing branch (and associated pull request).
					// Note: Do NOT use 'branch|export' and 'push-branch' or 'pull-branch' together.
					$destBranch = $repo['push-branch'] ?? $repo['pull-branch'];
					$push = "git push --force origin HEAD:'$destBranch'";
				} else {
					$push = "git push origin '$branch'";
				}

				$rebase = "git rebase 'origin/$branch'";
				$command =
					"cd '$name'; git add .; if ! git diff --cached --quiet; " .
					"then git commit -m '$message'; $rebase && $push; fi";
			} elseif ( $type === 'svn' ) {
				if ( $backportBranch !== null ) {
					throw new RuntimeException( 'Backport committing is not supported for subversion' );
				}

				$command =
					"cd '$name'; " .
					"svn add --force * --auto-props --parents --depth infinity -q; " .
					"svn commit --message '$message'";
			} elseif ( $type === 'bzr' ) {
				if ( $backportBranch !== null ) {
					throw new RuntimeException( 'Backport committing is not supported for Bazaar' );
				}
				$command = "cd '$name'; bzr add .;bzr commit -m '$message'";
			} else {
				throw new RuntimeException( "Unknown repo type '$type' for repository: $name" );
			}

			$process = Process::fromShellCommandline( $command );
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
			$mergeProcess = Process::fromShellCommandline( $command );
			$mergeProcess->setTimeout( 600 );
			$mergeProcess->mustRun();
		}

		$this->makePullRequests( $config, $base, $output );

		return 0;
	}

	private function makePullRequests( array $config, string $base, OutputInterface $output ): void {
		$factory = new ForgeFactory();

		foreach ( $config['repos'] as $name => $repo ) {
			if ( !( $repo['pull-branch'] ?? false ) ) {
				continue;
			}

			$repositoryPath = "$base/$name";

			try {
				if ( !$this->hasChanges( $repositoryPath, $repo['branch'] ?? 'master' ) ) {
					continue;
				}

				$this->makePullRequest( $repo, $factory, $output, $name );
			} catch ( Exception $e ) {
				$formatter = $output->getFormatter();
				$errorMessage = $formatter->escape( $e->getMessage() );
				$output->writeln( "<error>Unable to create a pull request for $name: $errorMessage</error>" );
			}
		}
	}

	private function makePullRequest(
		$repo,
		ForgeFactory $factory,
		OutputInterface $output,
		string $name
	): void {
		$pr = $this->getPullRequestSpecifier( $repo );
		// Delay constructing clients until they are needed. It may be that pull requests
		// are not used with all forges, so trying to construct it may fail due to a lack
		// of a token. The forge factory also handles caching of clients, so we only log
		// in once (if applicable) per forge site.
		$domain = $this->getDomainFromForgeUrl( $repo['url'] );
		$client = $factory->getForgeClient( $repo['type'], $domain );
		$response = $client->createPullRequest( $pr, self::MESSAGE, self::PR_MESSAGE );

		if ( $response->isNew() ) {
			$output->writeln( "$name: Opened a new pull request." );
		} else {
			$createdAt = $response->getCreationTime();
			$duration = $this->getTimeSince( $createdAt );
			$output->writeln( "$name: Pull request has been open for $duration." );
		}
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

	private function getPullRequestSpecifier( array $config ): PullRequestSpecifier {
		$repo = $this->getOwnerAndRepoFromForgeUrl( $config['url'] );
		return new PullRequestSpecifier(
			$repo['owner'],
			$repo['repo'],
			$config['branch'] ?? 'master',
			$config['pull-branch']
		);
	}

	private function getOwnerAndRepoFromForgeUrl( string $url ): array {
		// Supports the following formats:
		// * https://example.com/owner/repo.git
		// * https://example.com/owner/repo
		// * git@example.com:owner/repo.git
		// * git@example.com:owner/repo

		$matches = [];
		preg_match( '~[:/](?<owner>[^/]+)/(?<repo>[^/.]+?)(\.git)?$~', $url, $matches );

		return [
			'owner' => $matches['owner'] ?? '',
			'repo' => $matches['repo'] ?? '',
		];
	}

	private function getDomainFromForgeUrl( string $url ): string {
		$matches = [];
		preg_match( '~(?:https://|git@)(?<domain>[^:/]+)~', $url, $matches );

		return $matches['domain'];
	}

	/** Check whether there are changes for a pull request (compared to target branch). */
	private function hasChanges( string $repositoryPath, string $branch ): bool {
		$process = Process::fromShellCommandline( "git log origin/$branch..HEAD" );
		$process->setWorkingDirectory( $repositoryPath );
		$process->setTimeout( 5 );
		$process->run();
		if ( !$process->isSuccessful() ) {
			throw new RuntimeException( "Failed to check for changes" );
		}

		return trim( $process->getOutput() ) !== '';
	}
}
