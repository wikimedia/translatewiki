class profile::mwelasticsearch {
  include ::nginx

  file { '/etc/nginx/sites/es.translatewiki.net':
    source  => 'puppet:///modules/profile/es.translatewiki.net',
    require => Package['nginx'],
    notify  => Service['nginx'],
  }

  class { 'elasticsearch':
    manage_repo   => true,
    repo_version  => '1.7',
    java_install  => true,
  }

  elasticsearch::instance { 'es-01':
    config => {
      'network.host'           => '::1',
      'script.disable_dynamic' => false,
    },
    init_defaults => {
      'ES_HEAP_SIZE' => '12g',
    },
  }

  elasticsearch::plugin { 'mobz/elasticsearch-head':
    instances => 'es-01',
  }
}
