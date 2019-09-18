# == Class: php
#
# Configures php for MediaWiki to run in cli and fastcgi modes.
#
class php {
  include ::apt

  apt::ppa { 'ppa:ondrej/php': }

  package { [
    'php7.3-cli',
    'php7.3-curl',
    'php7.3-dba',
    'php7.3-fpm',
    'php7.3-gd',
    'php7.3-intl',
    'php7.3-mbstring',
    'php7.3-mysql',
    'php7.3-opcache',
    'php7.3-xml',
    'php-memcached',
    'php-yaml',
  ]:
    ensure  => present,
    require => Apt::Ppa['ppa:ondrej/php'],
  }

  file { '/etc/php/7.3/fpm/pool.d/www.conf':
    source  => 'puppet:///modules/php/www.conf',
    require => Package['php7.3-fpm'],
    notify  => Service['php-fpm'],
  }

  service { 'php-fpm':
    ensure => running,
    enable => true,
    name   => 'php7.3-fpm.service',
  }
}
