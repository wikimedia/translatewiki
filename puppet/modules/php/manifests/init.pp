# == Class: php
#
# Configures php for MediaWiki to run in cli and fpm modes.
#
class php {
  $packages = [
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

  # These depend on the php meta package; install a SAPI first so apt does
  # not pick libapache2-mod-php (and apache2) to satisfy it
  $extension_packages = [
    'php-luasandbox',
    'php-memcached',
    'php-wikidiff2',
    'php-wmerrors',
    'php-yaml',
  ]
  stdlib::ensure_packages($extension_packages, {
    ensure  => 'present',
    require => [ Package['php8.4-fpm'], Package['php8.4-cli'] ],
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

  file { '/etc/php/8.4/fpm/conf.d/50-opcache.ini':
    source  => 'puppet:///modules/php/opcache.ini',
    require => [Package['php8.4-fpm'], Package['php8.4-opcache']],
    notify  => Service['php-fpm'],
  }

  file { '/etc/php/8.4/cli/conf.d/50-opcache.ini':
    source  => 'puppet:///modules/php/opcache.ini',
    require => [Package['php8.4-cli'], Package['php8.4-opcache']],
  }
}
