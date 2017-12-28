# = Class: mwelasticsearch
#
# Provides elasticsearch configuration for MediaWiki.
#
class profile::mwelasticsearch {
  include ::nginx
  include ::apt

  file { '/etc/nginx/sites/es.translatewiki.net':
    source  => 'puppet:///modules/profile/es.translatewiki.net',
    require => Package['nginx'],
    notify  => Service['nginx'],
  }

  apt::pin { 'elasticsearch':
    packages => 'elasticsearch',
    priority => 1000,
    version  => '5.5.2',
  }

  class { 'java': distribution => 'jre' }

  class { 'elasticsearch':
    repo_version => '5.x',
    version      => '5.5.2',
    jvm_options  => [
      '-Xms8g',
      '-Xmx10g'
    ],
  }

  elasticsearch::instance { 'es-01': }

  elasticsearch::plugin { 'org.wikimedia.search:extra:5.5.2.3':
    instances  => 'es-01',
    module_dir => 'extra'
  }
}
