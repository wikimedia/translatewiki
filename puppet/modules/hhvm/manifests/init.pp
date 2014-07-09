# == Class: hhvm
#
# Configures hhvm for MediaWiki to run in fastcgi mode.
#
class hhvm {
  file { '/tmp/wikimedia.key':
    source => 'puppet:///modules/hhvm/wikimedia.key',
  }

  apt::key { 'wikimedia':
    key        => '09DBD9F93F6CD44A',
    key_source => '/tmp/wikimedia.key',
    require    => File['/tmp/wikimedia.key'],
  }

  apt::source { 'wikimedia':
    location => 'http://apt.wikimedia.org/wikimedia',
    release  => "${::lsbdistcodename}-wikimedia",
    repos    => 'main universe',
    require  => Apt::Key['wikimedia'],
  }

  package { [ 'hhvm', 'hhvm-dev', 'hhvm-fss', 'hhvm-luasandbox', 'hhvm-wikidiff2' ]:
    before  => Service['hhvm'],
    require => Apt::Source['wikimedia'],
  }

  package { 'hhvm-nightly': ensure => absent, }

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

  service { 'hhvm':
    ensure   => running,
    provider => upstart,
    require  => File['/etc/init/hhvm.conf'],
  }
}
