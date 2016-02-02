<?php

use Symfony\Component\Process\Process;

require_once __DIR__ . '/vendor/autoload.php';

class RepoNg {
	protected $meta;
	protected $config;

	public function __construct( array $meta, array $config ) {
		$this->meta = $meta;
		$this->config = $config;
	}

	public function update() {
		$bindir = realpath( __DIR__ . '/../bin' );
		$base = $this->meta['basepath'];

		foreach ( $this->config['repos'] as $name => $repo ) {
			if ( $repo['type'] === 'git' ) {
				$command = "$bindir/clupdate-git-repo '{$repo['url']}' '$base/$name'";
			} else {
				throw new RuntimeException( 'Unknown repo rype' );
			}
			echo "$command\n";

			$process = new Process( $command );
			$process->mustRun();
			print $process->getOutput();
		}
	}

	public function export() {
		$base = $this->meta['basepath'];
		$exporter = $this->meta['export'];

		$group = $this->config['group'];
		$lang = '*';
		$skip = 'en';
		$threshold = '35';

		$command = "$exporter --group='$group' --lang='$lang' --skip='$skip' " .
			"--threshold='$threshold' --target='$base'";
		echo "$command\n";

		$process = new Process( $command );
		$process->mustRun();
		print $process->getOutput();
	}

	public function commit() {
		$message = 'Localisation updates from https://translatewiki.net.';
		$base = $this->meta['basepath'];

		foreach ( $this->config['repos'] as $name => $repo ) {
			if ( $repo['type'] === 'git' ) {
				$dir = "$base/$name";
				$command = "cd $dir; git add .; git commit -m '$message' || :; git push origin master";
			} else {
				throw new RuntimeException( "Unknown repo type" );
			}
			echo "$command\n";

			$process = new Process( $command );
			$process->mustRun();
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
