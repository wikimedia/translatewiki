# = Class: mwelasticsearch
#
# Provides elasticsearch configuration for MediaWiki.
#
class profile::mwelasticsearch (
  String $memory_limit = '1g',
) {
  include ::apt

  # Do not automatically upgrade these packages
  apt::pin { 'elasticsearch':
    packages => [ 'elasticsearch', 'kibana' ],
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
      "-Xms${memory_limit}",
      "-Xmx${memory_limit}",
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
