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
    repo_version => '2.x',
    java_install => true,
  }

  elasticsearch::instance { 'es-01':
    config        => {
      'network.host' => '::1',
    },
    init_defaults => {
      'ES_HEAP_SIZE' => '12g',
    },
  }

  elasticsearch::plugin { 'mobz/elasticsearch-head/latest':
    instances => 'es-01',
  }

  elasticsearch::plugin { 'org.wikimedia.search/extra/2.3.3':
    instances => 'es-01',
  }
}
