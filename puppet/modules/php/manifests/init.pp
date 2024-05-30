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
    'php8.2-cli',
    'php8.2-curl',
    'php8.2-dba',
    'php8.2-fpm',
    'php8.2-gd',
    'php8.2-gmp', # For WebAuthn
    'php8.2-intl',
    'php8.2-mbstring',
    'php8.2-mysql',
    'php8.2-opcache',
    'php8.2-xml',
  ]
  stdlib::ensure_packages($packages, {
    ensure => 'present',
  })

  file { '/etc/php/8.2/fpm/pool.d/www.conf':
    source  => 'puppet:///modules/php/www.conf',
    require => Package['php8.2-fpm'],
    notify  => Service['php-fpm'],
  }

  service { 'php-fpm':
    ensure => running,
    enable => true,
    name   => 'php8.2-fpm.service',
  }

  file { '/etc/php/8.2/fpm/conf.d/50-wmerrors.ini':
    source  => 'puppet:///modules/php/wmerrors.ini',
    require => Package['php8.2-fpm'],
    notify  => Service['php-fpm'],
  }
}
