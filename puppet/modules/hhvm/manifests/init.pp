# == Class: hhvm
#
# Configures hhvm for MediaWiki to run in fastcgi mode.
#
class hhvm {
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

  file { '/etc/hhvm/config.hdf':
    source  => 'puppet:///modules/hhvm/config.hdf',
    require => Package['hhvm'],
    notify  => Service['hhvm'],
  }

  file { '/etc/hhvm/php.ini':
    source  => 'puppet:///modules/hhvm/php.ini',
    require => Package['hhvm'],
    notify  => Service['hhvm'],
  }

  service { 'hhvm':
    ensure   => running,
    provider => upstart,
    require  => File['/etc/init/hhvm.conf'],
  }
}
