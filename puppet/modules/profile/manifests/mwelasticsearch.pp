# = Class: mwelasticsearch
#
# Provides elasticsearch configuration for MediaWiki.
#
class profile::mwelasticsearch {
  include ::apt

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
      '-Xms12g',
      '-Xmx12g',
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

  # Do not upgrade the kibana package
  exec { 'hold kibana':
    command => '/usr/bin/apt-mark hold kibana',
    unless  => '/usr/bin/test "$(/usr/bin/apt-mark showhold "^kibana$" |wc -l)" == "1"'
  }
}
