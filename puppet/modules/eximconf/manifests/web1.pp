# = Class: eximconf::web1
#
# Provides exim configuration for server web1.
#
class eximconf::web1 {
  file { '/etc/exim4/update-exim4.conf.conf':
    source => 'puppet:///modules/eximconf/exim4.conf-web1',
    notify => Service['exim4'],
  }
}
