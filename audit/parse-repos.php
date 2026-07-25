<?php
/**
 * Parse repoconfig.yaml and generated extension/skin lists to produce
 * a complete list of repos for the activity audit.
 * Output: JSON with project, repo_key, url, type
 */

$basedir = '/resources/projects';
$configdir = '/home/betawiki/config';
$yaml = file_get_contents( "$configdir/repoconfig.yaml" );

$lines = explode( "\n", $yaml );
$projects = [];
$currentProject = null;
$currentRepo = null;
$inRepos = false;

foreach ( $lines as $line ) {
	// Skip @meta
	if ( preg_match( "/^'@meta':/", $line ) ) {
		$currentProject = '@meta';
		continue;
	}

	// Top-level project (not indented, key: value pattern)
	if ( preg_match( '/^([a-zA-Z0-9][a-zA-Z0-9_.\/-]*):/', $line, $m ) && !preg_match( '/^    /', $line ) ) {
		$currentProject = $m[1];
		$currentRepo = null;
		$inRepos = false;
		if ( !isset( $projects[$currentProject] ) ) {
			$projects[$currentProject] = [ 'repos' => [] ];
		}
		continue;
	}

	if ( $currentProject === '@meta' || $currentProject === null ) {
		continue;
	}

	// repos: section
	if ( preg_match( '/^  repos:$/', $line ) ) {
		$inRepos = true;
		$currentRepo = null;
		continue;
	}

	// Other top-level properties (not repos)
	if ( preg_match( '/^  [a-z]/', $line ) && !$inRepos ) {
		continue;
	}

	// Generator line
	if ( $inRepos && preg_match( "/^    '@generator':\\s*(.+)$/", $line, $m ) ) {
		$projects[$currentProject]['generator'] = trim( $m[1] );
		$currentRepo = null;
		continue;
	}

	// Repo entry (4 spaces indent, key with colon at end)
	if ( $inRepos && preg_match( '/^    ([^\s].+):$/', $line, $m ) ) {
		$repoKey = trim( $m[1], "' \"" );
		$currentRepo = $repoKey;
		$projects[$currentProject]['repos'][$currentRepo] = [];
		continue;
	}

	// Properties of a repo (6 spaces)
	if ( $currentRepo !== null && $inRepos ) {
		if ( preg_match( '/^      url:\s*(.+)$/', $line, $m ) ) {
			$url = trim( $m[1] );
			$projects[$currentProject]['repos'][$currentRepo]['url'] = $url;
		}
		if ( preg_match( '/^      type:\s*(.+)$/', $line, $m ) ) {
			$projects[$currentProject]['repos'][$currentRepo]['type'] = trim( $m[1] );
		}
	}
}

// Now handle generators: mediawiki-extensions and mediawiki-skins

/**
 * @param string $type 'extensions' or 'skins'
 * @param string $configdir Path to config directory
 * @return string[] List of repo names
 */
function wfGetGeneratedRepos( $type, $configdir ) {
	$data = file_get_contents( "$configdir/groups/MediaWiki/mediawiki-{$type}.txt" );
	$data = preg_replace( '/#.*\n/', '', $data );
	$sections = array_map( 'trim', preg_split( '/\n{2,}/', $data, -1, PREG_SPLIT_NO_EMPTY ) );

	$repos = [];
	foreach ( $sections as $section ) {
		$lines = preg_split( '/\n/', $section );
		$repo = str_replace( ' ', '', $lines[0] );
		foreach ( $lines as $line ) {
			$parts = preg_split( '/\s*=\s*/', $line, 2 );
			if ( count( $parts ) === 2 && $parts[0] === 'file' ) {
				$repo = explode( '/', $parts[1] )[0];
			}
		}
		$repos[] = $repo;
	}

	sort( $repos );
	return $repos;
}

// Add generated extension repos
$extRepos = wfGetGeneratedRepos( 'extensions', $configdir );
foreach ( $extRepos as $name ) {
	$projects['mediawiki-extensions']['repos']["mediawiki-extensions/$name"] = [
		'type' => 'wmgerrit',
		'url' => "https://gerrit.wikimedia.org/r/mediawiki/extensions/$name",
	];
}

// Add generated skin repos
$skinRepos = wfGetGeneratedRepos( 'skins', $configdir );
foreach ( $skinRepos as $name ) {
	$projects['mediawiki-skins']['repos']["mediawiki-skins/$name"] = [
		'type' => 'wmgerrit',
		'url' => "https://gerrit.wikimedia.org/r/mediawiki/skins/$name",
	];
}

// Also expand mwgitlab repos which are already explicit in repoconfig

// Remove @meta if present
unset( $projects['@meta'] );

// Output as JSON
echo json_encode( $projects, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
