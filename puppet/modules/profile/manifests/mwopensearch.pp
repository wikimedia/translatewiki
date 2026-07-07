# = Class: mwopensearch
#
# Provides OpenSearch configuration for MediaWiki.
#
class profile::mwopensearch (
  String $memory_limit = '1g',
) {
  # The plugin versions below must match this version exactly
  $version = '2.19.5'

  include ::apt

  # The OpenSearch repo key is bound with SHA1, which Debian 13's apt
  # verifier (sqv) rejects since 2026-02-01. Allow SHA1 second-preimage
  # resistance until upstream re-signs the key:
  # https://github.com/opensearch-project/OpenSearch/issues/21588
  file { ['/etc/crypto-policies', '/etc/crypto-policies/back-ends']:
    ensure => directory,
  }
  file { '/etc/crypto-policies/back-ends/sequoia.config':
    content => "[hash_algorithms]\nsha1.second_preimage_resistance = \"always\"\n",
  }
  File['/etc/crypto-policies/back-ends/sequoia.config'] -> Exec['apt_update']

  # OpenSearch bundles its own JDK, so no java module is needed.
  # The module also pins the package to $version via apt::pin.
  class { 'opensearch':
    version   => $version,
    heap_size => $memory_limit,
    settings  => {
      # Plain http on localhost, like the previous elasticsearch-oss setup
      'plugins.security.disabled' => true,
    },
  }

  # The deb postinst aborts without an initial admin password (OpenSearch
  # >= 2.12), so install with a throwaway one before the module's package
  # resource runs. The password is never used: security is disabled above.
  exec { 'install-opensearch':
    command  => "OPENSEARCH_INITIAL_ADMIN_PASSWORD=\"Aa1!\$(openssl rand -base64 24)\" apt-get install -y opensearch=${version}",
    path     => [ '/usr/bin', '/usr/sbin', '/bin' ],
    provider => 'shell',
    unless   => 'dpkg-query -W opensearch',
    timeout  => 1800,
  }
  Exec['apt_update'] -> Exec['install-opensearch'] -> Package['opensearch']

  # OpenSearch builds of the Wikimedia search plugins
  $plugins = {
    'opensearch-extra'   => 'org.wikimedia.search:opensearch-extra:2.19.5-wmf2',
    'cirrus-highlighter' => 'org.wikimedia.search.highlighter:cirrus-highlighter-opensearch-plugin:2.19.5-wmf1',
  }

  $plugins.each |String $name, String $coordinates| {
    exec { "opensearch-plugin-${name}":
      command => "/usr/share/opensearch/bin/opensearch-plugin install --batch ${coordinates}",
      creates => "/usr/share/opensearch/plugins/${name}",
      require => Package['opensearch'],
      notify  => Service['opensearch'],
    }
  }
}
