# = Class: memcached
#
# Provides memcached installation and configuration.
#
class memcached {
  $packages = [
    'memcached',
  ]
  stdlib::ensure_packages($packages, {
    ensure => 'present',
  })

  service { 'memcached':
    ensure     => running,
    enable     => true,
    hasstatus  => true,
    hasrestart => true,
    require    => Package['memcached'],
  }

  file { '/etc/memcached.conf':
    source  => 'puppet:///modules/memcached/memcached.conf',
    backup  => '.puppet.bak',
    require => Package['memcached'],
    notify  => Service['memcached'];
  }
}
