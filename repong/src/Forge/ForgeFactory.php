<?php
declare( strict_types=1 );

namespace Translatewiki\RepoNg\Forge;

use RuntimeException;

class ForgeFactory {
	/** @var ForgeClient[] */
	private $cache = [];

	public function getForgeClient( string $type, string $domain ): ForgeClient {
		if ( !isset( $this->cache[$domain] ) ) {
			$this->cache[$domain] = $this->constructForgeClient( $type, $domain );
		}

		return $this->cache[$domain];
	}

	private function constructForgeClient( string $type, string $domain ): ForgeClient {
		// In the future, there may be other authentication methods
		$token = $this->getToken( $domain );
		if ( $type === 'github' ) {
			$client = new GithubClient( $token );
		} elseif ( $type === 'gitlab' ) {
			$client = new GitlabClient( $token, "https://$domain" );
		} else {
			throw new RuntimeException( "No forge client for repository type $type" );
		}

		return $client;
	}

	private function getToken( $domain ): string {
		$domainForToken = preg_replace( '~[^a-zA-Z0-9_]~', '_', $domain );
		$environmentVariableName = 'L10NBOT_TOKEN_' . $domainForToken;
		$token = getenv( $environmentVariableName );

		if ( $token === false && $environmentVariableName === 'L10NBOT_TOKEN_github_com' ) {
			// Backwards compatibility
			$token = getenv( 'L10NBOT_GITHUB_TOKEN' );
		}

		if ( $token === false ) {
			throw new RuntimeException(
				"Environment variable $environmentVariableName is not defined"
			);
		}

		return $token;
	}
}
