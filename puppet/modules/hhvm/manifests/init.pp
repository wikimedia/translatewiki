# == Class: hhvm
#
# Configures hhvm for MediaWiki to run in fastcgi mode.
#
class hhvm {
  include apt

  file { '/tmp/wikimedia.key':
    source => 'puppet:///modules/hhvm/wikimedia.key',
  }

  exec { 'apt-key-wikimedia':
    command => '/usr/bin/apt-key add /tmp/wikimedia.key',
    require => File['/tmp/wikimedia.key'],
  }

  apt::source { 'wikimedia':
    location => 'http://apt.wikimedia.org/wikimedia',
    release  => "${::lsbdistcodename}-wikimedia",
    repos    => 'main universe',
    require  => Exec['apt-key-wikimedia'],
  }

  package { [ 'hhvm', 'hhvm-dev', 'hhvm-fss', 'hhvm-luasandbox', 'hhvm-wikidiff2' ]:
    ensure  => latest,
    before  => Service['hhvm'],
    require => Apt::Source['wikimedia'],
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
    enable   => true,
    ensure   => running,
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
    enable   => true,
    ensure   => running,
    provider => upstart,
    require  => File['/etc/init/hhvm-development.conf'],
  }
}
