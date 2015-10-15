# = Class: memcached
#
# Provides memcached installation and configuration.
#
class memcached {
  package { 'memcached':
    ensure => present,
  }

  service { 'memcached':
    ensure     => running,
    enable     => true,
    hasstatus  => true,
    hasrestart => true,
    require    => Package['memcached'],
  }

  file { '/etc/memcached.conf':
    source  => 'puppet:///modules/memcached/memcached.conf',
    require => Package['memcached'],
    notify  => Service['memcached'];
  }
}
