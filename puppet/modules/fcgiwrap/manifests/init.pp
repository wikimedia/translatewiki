# = Class: fcgiwrap
#
# Provides installation and configuration for the fcgiwrap package.
#
class fcgiwrap {
  package { 'fcgiwrap':
    ensure => present,
  }

  service { 'fcgiwrap':
    ensure     => running,
    enable     => true,
    hasstatus  => true,
    hasrestart => true,
    require    => Package['fcgiwrap'],
  }
}
