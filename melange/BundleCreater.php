<?php
/**
 * Wizard to create extension bundle releases.
 * @author Niklas Laxström
 * @license GPL-2.0-or-later
 * @file
 */

class BundleCreater {
	protected $conf;
	protected $dir;

	/**
	 * @param array $conf Array of configuration options. See provided
	 *   config.ini for list of available options.
	 * @param string $dir The current working directory.
	 */
	public function __construct( $conf, $dir ) {
		$this->conf = $conf;
		$this->dir = $dir;
	}

	public function reset() {
		exec( 'rm -rf extensions/' );
	}

	public function make_release() {
		$this->clone_extensions();
		$this->update_extensions();
		$this->checkout_release();
		$this->prepare_notes();
		$this->create_tag();
		$this->push_tag();
		$this->create_archive();
		$this->prepare_announcement();
	}

	public function clone_extensions() {
		chdir( $this->dir );

		if ( !file_exists( "extensions" ) ) {
			mkdir( "extensions/" );
		}

		foreach ( $this->conf['extensions'] as $ext => $checkout ) {
			$target = "extensions/$ext";
			if ( !file_exists( "$target" ) ) {
				$repo = escapeshellarg( $this->conf['common']['extensionrepo'] . $ext . ".git" );
				$target = escapeshellarg( $target );
				exec( "git clone $repo $target" );
			}
		}
	}

	public function update_extensions() {
		foreach ( $this->conf['extensions'] as $ext => $checkout ) {
			$target = "{$this->dir}/extensions/$ext";
			chdir( $target );
			exec( "git fetch --all --quiet --tags" );
		}
	}

	public function checkout_release() {
		foreach ( $this->conf['extensions'] as $ext => $checkout ) {
			chdir( $this->dir );
			chdir( "extensions/$ext" );

			$checkout = escapeshellarg( $checkout );
			exec( "git checkout $checkout --quiet" );
		}
	}

	public function prepare_notes() {
		$version = $this->conf['common']['releasever'];

		$old = 'HEAD~100';
		if ( isset( $this->conf['common']['releasever-prev'] ) ) {
			$old = $this->conf['common']['releasever-prev'];
		}

		if ( !file_exists( 'notes' ) ) {
			mkdir( 'notes' );
		}

		foreach ( $this->conf['extensions'] as $ext => $checkout ) {
			chdir( $this->dir );
			chdir( "extensions/$ext" );

			$diff = escapeshellarg( "$old...$checkout" );
			$target = "{$this->dir}/notes/$version-$ext";
			$file = escapeshellarg( $target );

			exec( "git shortlog $diff > $file" );
			exec( "git log $diff >> $file" );
			exec( "git diff $diff >> $file" );
			$content = file_get_contents( $target );
			$header = <<<TEXT
# Please write release notes for extension $ext
# Can also be left empty, but keep the #--- lines
#---

=== Highlights ===
...

=== Noteworthy changes ===
* ...

#---
# Below are logs and changes from previous release
# (or latest 100 changes if not available) for help


TEXT;

			$content = $header . $content;
			file_put_contents( $target, $content );
			passthru( "\$EDITOR $file" );
		}
	}

	public function create_tag() {
		$name = $this->conf['common']['bundlename'];
		$version = $this->conf['common']['releasever'];
		$branch = str_replace( '$1', $version, $this->conf['common']['branchname'] );
		$tag = str_replace( '$1', $version, $this->conf['common']['tagname'] );
		$date = date( 'Y-m-d' );
		$extra = str_replace( '$1', $version, $this->conf['common']['versionextra'] );

		foreach ( $this->conf['extensions'] as $ext => $checkout ) {
			chdir( "{$this->dir}/extensions/$ext" );
			$cBranch = escapeshellarg( $branch );
			$cCheckout = escapeshellarg( $checkout );
			exec( "git checkout -b $cBranch $cCheckout --force --quiet" );
			exec( "git reset --hard $cCheckout --quiet" );
			exec( "git rm .gitreview --quiet --ignore-unmatch" );

			$notefile = $this->dir . "/notes/$version-$ext";

			// Sometimes these files pass through other editors,
			// which save them with wrong file endings.
			// Convert all endings to \n.
			$contents = str_replace( [ "\r\n", "\r" ], "\n", file_get_contents( $notefile ) );
			preg_match( '/^#---$(.*)^#---$/msU', $contents, $matches );
			$notes = trim( $matches[1] );
			$notes = "== $ext $version ==\nReleased at $date.\n\n$notes\n";
			$notes = trim( $notes ) . "\n";

			$relnotefile = "{$this->dir}/extensions/$ext/RELEASE-NOTES";
			file_put_contents( $relnotefile, $notes );

			// Patch version
			$setupfile = "{$this->dir}/extensions/$ext/extension.json";
			if ( !file_exists( $setupfile ) ) {
				echo "$setupfile does not exist - skipping\n";
			} else {
				$json = file_get_contents( $setupfile );
				$contents = json_decode( $json, true );
				$contents['version'] ??= '';
				$contents['version'] = trim( $contents['version'] . " $extra" );
				# This will cause some dirty diffs (mainly tabs to spaces, formatting)
				$json = json_encode( $contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
				file_put_contents( $setupfile, $json );
			}

			$msg = escapeshellarg( "$name $version" );

			exec( 'git add .' );
			exec( "git commit -v -m $msg --quiet" );

			$cTag = escapeshellarg( $tag );
			exec( "git tag -a $cTag -m $msg" );
		}
	}

	public function sign_tag() {
		$version = $this->conf['common']['releasever'];
		$tag = str_replace( '$1', $version, $this->conf['common']['tagname'] );
		$name = $this->conf['common']['bundlename'];

		foreach ( $this->conf['extensions'] as $ext => $checkout ) {
			chdir( "{$this->dir}/extensions/$ext" );
			$msg = escapeshellarg( "$name $version" );
			$cTag = escapeshellarg( $tag );
			exec( "git tag -s $cTag -m $msg -f" );
		}
	}

	public function sign_release() {
		$filename = $this->getReleaseFileName();
		exec( "gpg --armor --detach-sign releases/$filename" );
	}

	public function push_tag() {
		$version = $this->conf['common']['releasever'];
		$tag = str_replace( '$1', $version, $this->conf['common']['tagname'] );

		foreach ( $this->conf['extensions'] as $ext => $checkout ) {
			chdir( "{$this->dir}/extensions/$ext" );
			$cTag = escapeshellarg( $tag );
			exec( "git push origin $cTag" );
		}
	}

	protected function getReleaseFileName() {
		$version = $this->conf['common']['releasever'];
		$name = $this->conf['common']['bundlename'];
		$filename = preg_replace_callback( '/\s+(.)/', static function ( $matches ) {
			return strtoupper( $matches[1] );
		}, $name );
		return "$filename-$version.tar.bz2";
	}

	public function create_archive() {
		chdir( $this->dir );

		$piggyurl = $this->conf['common']['piggyurl'];
		$piggyfile = $this->conf['common']['piggyfile'];
		$hasher = $this->conf['common']['hasher'];

		if ( !file_exists( 'releases' ) ) {
			mkdir( 'releases' );
		}

		// XXX: generalize
		if ( $piggyurl ) {
			$json = file_get_contents( $piggyurl );
			$data = json_decode( $json, true );
			$data = $data['query']['pages'][0]['revisions'][0]['content'];
			$data = preg_replace( '~<noinclude>.*?</noinclude>\s*~s', '', $data );
			$data = str_replace(
				'{{:MediaWiki Language Extension Bundle/latest}}',
				$this->conf['common']['releasever'],
				$data
			);
			file_put_contents( $piggyfile, $data );
		}

		$filename = $this->getReleaseFileName();

		$tarname = escapeshellarg( "releases/$filename" );
		$hashname = escapeshellarg( "$filename.$hasher" );
		$cPiggyfile = escapeshellarg( $piggyfile );
		exec( "tar cjf $tarname --exclude-vcs extensions $cPiggyfile" );
		chdir( 'releases' );
		exec( "$hasher $filename > $hashname" );
	}

	public function prepare_announcement() {
		chdir( $this->dir );

		$version = $this->conf['common']['releasever'];
		$name = $this->conf['common']['bundlename'];
		$url = $this->conf['common']['downloadurl'];
		$hasher = $this->conf['common']['hasher'];

		$filename = $this->getReleaseFileName();
		$contents = file_get_contents( "releases/$filename.$hasher" );
		[ $hash, /*unused*/ ] = explode( ' ', $contents, 2 );

		$parts = [];
		$parts[] = "I would like to announce the release of $name $version";
		$parts[] = "* $url/$filename\n* $hasher: $hash";
		$parts[] = <<<WIKITEXT
Quick links:
* Installation instructions are at https://www.mediawiki.org/wiki/MLEB
* Announcements of new releases will be posted to a mailing list:
  https://lists.wikimedia.org/mailman/listinfo/mediawiki-i18n
* Report bugs to https://phabricator.wikimedia.org
WIKITEXT;
		$parts[] = 'Release notes for each extension are below.';
		$parts[] = '    YOUR NAME';

		if ( !file_exists( 'notes' ) ) {
			mkdir( 'notes' );
		}

		foreach ( $this->conf['extensions'] as $ext => $checkout ) {
			$notefile = $this->dir . "/notes/$version-$ext";
			$contents = str_replace( [ "\r\n", "\r" ], "\n", file_get_contents( $notefile ) );
			preg_match( '/^#---$(.*)^#---$/msU', $contents, $matches );
			if ( !isset( $matches[1] ) ) {
				echo "Could not parse notes for extension $ext\n";
				continue;
			}
			$notes = trim( $matches[1] );
			$parts[] = "== $ext ==\n$notes";
		}

		$announcement = $this->dir . "/notes/$version";

		file_put_contents( $announcement, implode( "\n\n", $parts ) );
		passthru( "\$EDITOR $announcement" );

		echo "Please complete the following steps to finish the release process:\n";
		echo "* Update release pages on https://www.mediawiki.org/wiki/MLEB and its subpage /latest.\n";
		echo "* Move files releases/$filename* so that they can be downloaded from $url.\n";
		echo "* Send the release announcement from $announcement to mediawiki-i18n.\n";
		echo "* Consider also blogging/twitter etc.\n";
	}

	// In case you mess up
	public function undo_tag() {
		$version = $this->conf['common']['releasever'];
		$tag = str_replace( '$1', $version, $this->conf['common']['tagname'] );

		foreach ( $this->conf['extensions'] as $ext => $checkout ) {
			chdir( "{$this->dir}/extensions/$ext" );
			$cTag = escapeshellarg( $tag );
			exec( "git checkout master" );
			exec( "git tag -d $cTag" );
			$cTag = escapeshellarg( ":refs/tags/$tag" );
			exec( "git push origin $cTag" );
		}
	}
}
