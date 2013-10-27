File {
  owner => 'root',
  group => 'root',
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
  include sudo
  include users

  class { 'backup':
    databases => ['mediawiki'],
  }

  class { 'wiki':
    config => '/home/betawiki/config',
    user   => 'betawiki',
  }

  package { 'elasticsearch':
    provider => dpkg,
    ensure => latest,
    source => '/root/packages/elasticsearch-0.90.3.deb'
  }
}
