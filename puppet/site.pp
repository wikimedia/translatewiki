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
  include translationmemory
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
    manage_repo  => true,
    repo_version => '1.0',
    config       => {},
  }

  elasticsearch::plugin { 'mobz/elasticsearch-head':
    module_dir => 'head'
  }
}
