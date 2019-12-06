# = Class: mwelasticsearch
#
# Provides elasticsearch configuration for MediaWiki.
#
class profile::mwelasticsearch {
  include ::nginx
  include ::apt

  file { '/etc/nginx/sites/es.translatewiki.net':
    source  => 'puppet:///modules/profile/es.translatewiki.net',
    require => Package['nginx-light'],
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

  elasticsearch::plugin { 'org.wikimedia.search:extra:5.5.2.7':
    instances  => 'es-01',
    module_dir => 'extra'
  }

  elasticsearch::plugin { 'org.wikimedia.search.highlighter:experimental-highlighter-elasticsearch-plugin:5.5.2.2':
    instances  => 'es-01',
    module_dir => 'experimental-highlighter'
  }


  class { 'kibana':
    ensure => '5.5.2',
  }
}
