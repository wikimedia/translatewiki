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

  file { '/etc/systemd/system/hhvm.service':
    source  => 'puppet:///modules/hhvm/hhvm.service',
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
    provider => 'systemd',
    require  => File['/etc/systemd/system/hhvm.service'],
  }

  # Second HHVM instance for development
  file { '/etc/systemd/system/hhvm-development.service':
    source  => 'puppet:///modules/hhvm/hhvm-development.service',
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
    provider => 'systemd',
    require  => File['/etc/systemd/system/hhvm-development.service'],
  }
}
