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
    packages => [ 'elasticsearch-oss', 'kibana-oss' ],
    priority => 1000,
    version  => '7.10.2',
  }

  class { 'java': distribution => 'jre' }

  class { 'elastic_stack::repo':
    version => 7,
    oss     => true,
  }

  class { 'elasticsearch':
    restart_on_change => true,
    version           => '7.10.2',
    oss               => true,
    jvm_options       => [
      "-Xms${memory_limit}",
      "-Xmx${memory_limit}",
    ],
  }

  elasticsearch::plugin { 'org.wikimedia.search:extra:7.10.2-wmf1':
  }

  elasticsearch::plugin { 'org.wikimedia.search.highlighter:experimental-highlighter-elasticsearch-plugin:7.10.2':
    module_dir => 'experimental-highlighter'
  }

  class { 'kibana':
    ensure => '7.10.2',
    oss    => true,
  }
}
