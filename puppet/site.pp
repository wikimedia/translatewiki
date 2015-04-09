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
  include sshd
  include sudo
  include users
# Always fails currently
#  include composer
  include hhvm
  include kitano-nl
  include profile::mwelasticsearch

  class { 'backup':
    databases => ['translatewiki_net'],
  }

  class { 'wiki':
    config => '/home/betawiki/config',
    user   => 'betawiki',
  }

  sysctl { 'net.ipv4.tcp_window_scaling': value => '1' }
  sysctl { 'net.ipv4.tcp_slow_start_after_idle': value => '0' }
  sysctl { 'net.ipv4.tcp_fastopen': value => '3' }
}
