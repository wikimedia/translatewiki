# == Class: php
#
# Configures php for MediaWiki to run in cli and fpm modes.
#
class php {
  $packages = [
    'php-luasandbox',
    'php-memcached',
    'php-wikidiff2',
    'php-wmerrors',
    'php-yaml',
    'php8.4-cli',
    'php8.4-curl',
    'php8.4-dba',
    'php8.4-fpm',
    'php8.4-gd',
    'php8.4-gmp', # For OATHAuth(?)
    'php8.4-intl',
    'php8.4-mbstring',
    'php8.4-mysql',
    'php8.4-opcache',
    'php8.4-xml',
  ]
  stdlib::ensure_packages($packages, {
    ensure => 'present',
  })

  file { '/etc/php/8.4/fpm/pool.d/www.conf':
    source  => 'puppet:///modules/php/www.conf',
    require => Package['php8.4-fpm'],
    notify  => Service['php-fpm'],
  }

  service { 'php-fpm':
    ensure => running,
    enable => true,
    name   => 'php8.4-fpm.service',
  }

  file { '/etc/php/8.4/fpm/conf.d/50-wmerrors.ini':
    source  => 'puppet:///modules/php/wmerrors.ini',
    require => Package['php8.4-fpm'],
    notify  => Service['php-fpm'],
  }
}
