# == Class: php
#
# Configures php for MediaWiki to run in cli and fastcgi modes.
#
class php {
  package { [
    'php-cli',
    'php-curl',
    'php-dba',
    'php-fpm',
    'php-gd',
    'php-intl',
    'php-mbstring',
    'php-mysql',
    'php-opcache',
    'php-xml',
    'php-memcached',
    'php-yaml',
  ]:
    ensure => present,
  }

  file { '/etc/php/7.3/fpm/pool.d/www.conf':
    source  => 'puppet:///modules/php/www.conf',
    require => Package['php-fpm'],
    notify  => Service['php-fpm'],
  }

  service { 'php-fpm':
    ensure => running,
    enable => true,
    name   => 'php-fpm.service',
  }
}
