# == Class: hhvm
#
# Configures hhvm for MediaWiki to run in fastcgi mode.
#
class hhvm {
  package { [ 'hhvm' ]:
    ensure => present,
    before => Service['hhvm'],
  }

  file { '/etc/hhvm':
    ensure => directory,
  }

  file { '/etc/init/hhvm.conf':
    source  => 'puppet:///modules/hhvm/upstart',
    require => Package['hhvm'],
    notify  => Service['hhvm'],
  }

  file { '/etc/hhvm/php.ini':
    source  => 'puppet:///modules/hhvm/php.ini',
    require => Package['hhvm'],
    notify  => Service['hhvm'],
  }

  file { '/etc/hhvm/server.ini':
    source  => 'puppet:///modules/hhvm/server.ini',
    require => Package['hhvm'],
    notify  => Service['hhvm'],
  }

  service { 'hhvm':
    ensure   => running,
    enable   => true,
    provider => upstart,
    require  => File['/etc/init/hhvm.conf'],
  }

  # Second HHVM instance for development
  file { '/etc/init/hhvm-development.conf':
    source  => 'puppet:///modules/hhvm/upstart-development',
    require => Package['hhvm'],
    notify  => Service['hhvm-development'],
  }

  file { '/etc/hhvm/development.ini':
    source  => 'puppet:///modules/hhvm/development.ini',
    require => Package['hhvm'],
    notify  => Service['hhvm-development'],
  }

  service { 'hhvm-development':
    ensure   => running,
    enable   => true,
    provider => upstart,
    require  => File['/etc/init/hhvm-development.conf'],
  }
}
