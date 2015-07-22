File {
  owner => 'root',
  group => 'root',
  mode  => '0644',
}

node default {
  include kitano-nl

  class { 'backup':
    databases => ['translatewiki_net'],
  }

  class { 'wiki':
    config => '/home/betawiki/config',
    user   => 'betawiki',
  }
}

# web1 / Primary web server
node 'translatewiki.net' {
  include base
  include puppet
  include sshd
  include sudo
  include users

  include awstats
  include base::web1
  include hhvm
  include logrotate
  include mariadb
  include memcached
  include nginx::sites

  include exim-conf
  include exim-conf::web1
  include mailman-conf

  sysctl { 'net.ipv4.tcp_window_scaling': value => '1' }
  sysctl { 'net.ipv4.tcp_slow_start_after_idle': value => '0' }
  sysctl { 'net.ipv4.tcp_fastopen': value => '3' }
}

# es / Elastic Search
node 'v220150764426371.yourvserver.net' {
  include base
  include puppet
  include sshd
  include sudo
  include users

  include base::es
  include exim-conf
  include exim-conf::web1
  include profile::mwelasticsearch
}
