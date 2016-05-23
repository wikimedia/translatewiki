<?php

use Symfony\Component\Process\Process;

require_once __DIR__ . '/vendor/autoload.php';

class RepoNg {
	protected $bindir;
	protected $meta;
	protected $config;
	protected $usernameConversion = [
		'nike' => 'nikerabbit',
	];

	public function __construct( array $meta, array $config ) {
		$this->bindir = realpath( __DIR__ . '/../bin' );

		if ( $this->bindir === false ) {
			throw new RuntimeException( __DIR__ . '/../bin/ does not exist' );
		}

		$this->meta = $meta;
		$this->config = $config;
	}

	public function update() {
		$base = $this->meta['basepath'];

		foreach ( $this->config['repos'] as $name => $repo ) {
			if ( $repo['type'] === 'git' ) {
				$branch = isset( $repo['branch'] ) ? $repo['branch'] : 'master';

				$userName = get_current_user();
				if ( isset( $this->usernameConversion[$userName] ) ) {
					$userName = $this->usernameConversion[$userName];
				}

				$repoUrl = $repo['url'];
				$repoUrl = str_replace( 'USERNAME', $userName, $repoUrl );

				$command = $this->bindir . "/clupdate-git-repo '$repoUrl' '$base/$name' '$branch'";
			} elseif ( $repo['type'] === 'wmgerrit' ) {
				$branch = isset( $repo['branch'] ) ? $repo['branch'] : 'master';
				$command = $this->bindir . "/clupdate-gerrit-repo '{$repo['url']}' '$base/$name' '$branch'";
			} else {
				throw new RuntimeException( 'Unknown repo rype' );
			}

			$process = new Process( $command );
			$process->mustRun();
			$process->setTimeout( 300 );
			print $process->getOutput();
		}
	}

	public function export() {
		$exporter = $this->meta['export'];
		$hours = '';
		$group = " --group='" . $this->config['group'] . "'";
		$lang = " --lang='*'";
		$skip = " --skip='en,qqq'";
		$threshold = ' --threshold=35';
		$base = " --target='" . $this->meta['basepath'] . "'";

		if ( isset( $this->config['export-hours'] ) ) {
			$hours = (int)$this->config['export-hours'];
			$hours = " --hours='$hours'";
		}

		if ( isset( $this->config['no-export-languages'] ) ) {
			$skip = $this->config['no-export-languages'];
			$skip = " --skip='$skip'";
		}

		if ( isset( $this->config['export-threshold'] ) ) {
			$threshold = (int)$this->config['export-threshold'];
			$threshold = " --threshold='$threshold'";
		}

		// First normal export
		$command = "$exporter$hours$group$lang$skip$threshold$base";
		echo "$command\n";

		$process = new Process( $command );
		$process->mustRun();
		$process->setTimeout( 120 );
		print $process->getOutput();

		// Then message documentation
		$lang = " --lang='qqq'";
		$command = "$exporter$group$lang$base";
		echo "$command\n";

		$process = new Process( $command );
		$process->mustRun();
		$process->setTimeout( 30 );
		print $process->getOutput();

		// Last languages that have a forced export
		if ( isset( $this->config['always-export-languages'] ) ) {
			$lang = $this->config['always-export-languages'];
			$lang = " --lang='$lang'";
			$command = "$exporter$hours$group$lang$base";
			echo "$command\n";

			$process = new Process( $command );
			$process->mustRun();
			$process->setTimeout( 120 );
			print $process->getOutput();
		}
	}

	public function commit() {
		$message = 'Localisation updates from https://translatewiki.net.';
		$base = $this->meta['basepath'];
		$gerritCommitted = false;

		foreach ( $this->config['repos'] as $name => $repo ) {
			if ( $repo['type'] === 'git' ) {
				$dir = "$base/$name";
				$branch = isset( $repo['branch'] ) ? $repo['branch'] : 'master';
				$command = "cd $dir; git add .; " .
					"git commit -m '$message' || :; " .
					"git push origin '$branch'";
			} elseif ( $repo['type'] === 'wmgerrit' ) {
				$dir = "$base/$name";
				$command = "cd $dir; git add .; " .
					"git commit -m '$message' || :; " .
					'git review -r origin -t L10n';

				$gerritCommitted = true;
			} else {
				throw new RuntimeException( 'Unknown repo type' );
			}
			echo "$command\n";

			$process = new Process( $command );
			$process->mustRun();
			$process->setTimeout( 120 );
			print $process->getOutput();
		}

		// Merge patch sets submitted to Wikimedia's Gerrit.
		if ( $gerritCommitted ) {
			$process = new Process( $this->bindir . '/merge-wmgerrit-patches' );
			$process->mustRun();
			$process->setTimeout( 120 );
			print $process->getOutput();
		}
	}
}

function findBase() {
	$path = getcwd();
	if ( $path === false ) {
		return null;
	}

	$path;
	while ( true ) {
		if ( file_exists( "$path/repoconfig.json" ) ) {
			return $path;
		}

		if ( $path === realpath( "$path/.." ) ) {
			return null;
		}

		$path = realpath( "$path/.." );
	}
}

$command = isset( $argv[1] ) ? $argv[1] : '';
$project = isset( $argv[2] ) ? $argv[2] : '';
$base = findBase();
$json = file_get_contents( "$base/repoconfig.json" );
$config = json_decode( $json, true );

if ( !isset( $config[$project] ) ) {
	echo "Unknown project $project\n";
	die();
}

$meta = $config['@meta'];
$meta['basepath'] = $base;

$ng = new RepoNG( $meta, $config[$project] );

if ( $command === 'update' ) {
	$ng->update();
} elseif ( $command === 'export' ) {
	$ng->export();
} elseif ( $command === 'commit' ) {
	$ng->commit();
} else {
	echo "Insufficient mana\n";
}
