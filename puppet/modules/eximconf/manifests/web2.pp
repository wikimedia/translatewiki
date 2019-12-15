# = Class: eximconf::web2
#
# Provides exim configuration for server web2.
#
class eximconf::web2 {
  file { '/etc/exim4/update-exim4.conf.conf':
    source => 'puppet:///modules/eximconf/exim4.conf-web2',
    notify => Service['exim4'],
  }
}
