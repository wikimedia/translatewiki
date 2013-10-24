File {
  owner   => 'root',
  group   => 'root',
}

node default {
  include base
  include nginx
  include php
  include puppet
  include memcached
  include mariadb

  package { 'elasticsearch':
    provider => dpkg,
    ensure => latest,
    source => '/root/packages/elasticsearch-0.90.3.deb'
  }
}
