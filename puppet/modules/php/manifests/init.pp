# == Class: php
#
# Configures php for MediaWiki to run in cli and fastcgi modes.
#
class php {
  include ::apt

  apt::ppa { 'ppa:ondrej/php': }

  package { [
    'php7.1-cli',
    'php7.1-curl',
    'php7.1-dba',
    'php7.1-fpm',
    'php7.1-gd',
    'php7.1-intl',
    'php7.1-mbstring',
    'php7.1-mysql',
    'php7.1-opcache',
    'php7.1-xml',
    'php-memcached',
    'php-yaml',
  ]:
    ensure  => present,
    require => Apt::Ppa['ppa:ondrej/php'],
  }

  file { '/etc/php/7.1/fpm/pool.d/www.conf':
    source  => 'puppet:///modules/php/www.conf',
    require => Package['php7.1-fpm'],
    notify  => Service['php-fpm'],
  }

  service { 'php-fpm':
    ensure => running,
    enable => true,
    name   => 'php7.1-fpm.service',
  }
}
