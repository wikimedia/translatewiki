# == Class: php
#
# Configures php for MediaWiki to run in cli and fpm modes.
#
class php {
  $packages = [
    'php-memcached',
    'php-wikidiff2',
    'php-wmerrors',
    'php-yaml',
    'php7.4-cli',
    'php7.4-curl',
    'php7.4-dba',
    'php7.4-fpm',
    'php7.4-gd',
    'php7.4-intl',
    'php7.4-mbstring',
    'php7.4-mysql',
    'php7.4-opcache',
    'php7.4-xml',
  ]
  stdlib::ensure_packages($packages, {
    ensure => 'present',
  })

  file { '/etc/php/7.4/fpm/pool.d/www.conf':
    source  => 'puppet:///modules/php/www.conf',
    require => Package['php7.4-fpm'],
    notify  => Service['php-fpm'],
  }

  service { 'php-fpm':
    ensure => running,
    enable => true,
    name   => 'php7.4-fpm.service',
  }

  file { '/etc/php/7.4/fpm/conf.d/50-wmerrors.ini':
    source  => 'puppet:///modules/php/wmerrors.ini',
    require => Package['php7.4-fpm'],
    notify  => Service['php-fpm'],
  }
}
