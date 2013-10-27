File {
  owner => 'root',
  group => 'root',
}

node default {
  include users
  include base
  include nginx
  include php
  include puppet
  include sudo
  include memcached
  include mariadb
  include exim-conf
  include logrotate
  include mailman-conf

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
