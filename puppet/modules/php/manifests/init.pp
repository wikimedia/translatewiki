# == Class: php
#
# Configures php for MediaWiki to run in cli and fastcgi modes.
#
class php {
  $packages = [
    'php-memcached',
    'php-wikidiff2',
    'php-yaml',
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
  ]
  ensure_packages($packages, {
    ensure => 'present',
  })

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
