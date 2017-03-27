# = Class: mwelasticsearch
#
# Provides elasticsearch configuration for MediaWiki.
#
class profile::mwelasticsearch {
  include ::nginx

  file { '/etc/nginx/sites/es.translatewiki.net':
    source  => 'puppet:///modules/profile/es.translatewiki.net',
    require => Package['nginx'],
    notify  => Service['nginx'],
  }

  class { 'elasticsearch':
    manage_repo  => true,
    repo_version => '5.x',
    java_install => true,
    version      => '5.1.2',
    jvm_options  => [
      '-Xms8g',
      '-Xmx10g'
    ],
  }

  elasticsearch::instance { 'es-01': }

  elasticsearch::plugin { 'org.wikimedia.search:extra:5.1.2':
    instances  => 'es-01',
    module_dir => 'extra'
  }
}
