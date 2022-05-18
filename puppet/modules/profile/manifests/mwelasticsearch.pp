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
    version  => '6.8.23',
  }

  class { 'java': distribution => 'jre' }

  class { 'elastic_stack::repo':
    version => 6,
    oss     => true,
  }

  class { 'elasticsearch':
    version     => '6.8.23',
    oss         => true,
    jvm_options => [
      "-Xms${memory_limit}",
      "-Xmx${memory_limit}",
    ],
  }

  elasticsearch::plugin { 'org.wikimedia.search:extra:6.8.23-wmf1':
  }

  elasticsearch::plugin { 'org.wikimedia.search.highlighter:experimental-highlighter-elasticsearch-plugin:6.8.23':
    module_dir => 'experimental-highlighter'
  }

  class { 'kibana':
    ensure => '6.8.23',
    oss    => true,
  }
}
