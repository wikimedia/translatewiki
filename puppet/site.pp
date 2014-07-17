File {
  owner => 'root',
  group => 'root',
  mode  => '0644',
}

node default {
  include awstats
  include base
  include exim-conf
  include logrotate
  include mailman-conf
  include mariadb
  include memcached
  include nginx
  include php
  include puppet
  include ssh-conf
  include sudo
  include users
  include composer
  include hhvm
  include kitano-nl

  class { 'backup':
    databases => ['translatewiki_net'],
  }

  class { 'wiki':
    config => '/home/betawiki/config',
    user   => 'betawiki',
  }

  class { 'elasticsearch':
    manage_repo   => true,
    repo_version  => '1.2',
    config        => {
        'script.disable_dynamic' => false,
        'network.bind_host' => "::1",
    },
    java_install  => true,
    init_defaults => {
        'ES_HEAP_SIZE' => '3g',
    },
  }

  elasticsearch::plugin { 'mobz/elasticsearch-head':
    module_dir => 'head'
  }

  sysctl { 'net.ipv4.tcp_window_scaling': value => '1' }
  sysctl { 'net.ipv4.tcp_slow_start_after_idle': value => '0' }
  sysctl { 'net.ipv4.tcp_fastopen': value => '3' }
}
