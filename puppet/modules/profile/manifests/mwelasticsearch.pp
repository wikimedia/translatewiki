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
    version  => '6.5.4',
  }

  class { 'java': distribution => 'jre' }

  class { 'elastic_stack::repo':
    version => 6,
  }

  class { 'elasticsearch':
    version     => '6.5.4',
    jvm_options => [
      '-Xms8g',
      '-Xmx10g',
      '#PrintGCDetails',
      '#PrintGCDateStamps',
      '#PrintTenuringDistribution',
      '#PrintGCApplicationStoppedTime',
      '#Xloggc',
      '#UseGCLogFileRotation',
      '#NumberOfGCLogFiles',
      '#GCLogFileSize',
      '#XX:UseConcMarkSweepGC',
    ],
  }

  elasticsearch::instance { 'es-01': }

  elasticsearch::plugin { 'org.wikimedia.search:extra:6.5.4':
    instances  => 'es-01',
    module_dir => 'extra'
  }

  elasticsearch::plugin { 'org.wikimedia.search.highlighter:experimental-highlighter-elasticsearch-plugin:6.5.4.1':
    instances  => 'es-01',
    module_dir => 'experimental-highlighter'
  }

  class { 'kibana':
    ensure => '6.5.4',
  }
}
