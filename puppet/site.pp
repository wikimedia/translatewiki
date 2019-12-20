File {
  owner => 'root',
  group => 'root',
  mode  => '0644',
}

node 'web2.translatewiki.net' {
  include base
  include base::vcs
  include sshd
  include users
  include hostname

  include awstats
  include php
  include logrotate
  include mariadb
  include memcached
  include nginx::sites

  class { 'eximconf':
    local_interfaces => [
    '152.89.106.205',
    '2a03:4000:39:55d:5400:a2ff:fe21:b3ea',
    ]
  }

  include profile::mwelasticsearch

  include kitanonl

  mount { '/scratch':
    ensure  => 'mounted',
    device  => '46.38.248.210:/voln264518a1',
    fstype  => 'nfs',
    options => 'rw',
    require => File['/scratch'],
  }

  class { 'backup':
    databases => ['translatewiki_net'],
  }

  class { 'wiki':
    config => '/home/betawiki/config',
    user   => 'betawiki',
  }
  include wiki::irc

  ::keyholder::agent { 'l10n-bot':
    trusted_groups => ['l10n-bot'],
    priv_key_path  => '/root/keyholder/l10n-bot',
  }

  sysctl { 'net.ipv4.tcp_window_scaling': value => '1' }
  sysctl { 'net.ipv4.tcp_slow_start_after_idle': value => '0' }
  sysctl { 'net.ipv4.tcp_fastopen': value => '3' }
}
