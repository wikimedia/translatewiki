# = Class: eximconf::es
#
# Provides exim configuration for server es.
#
class eximconf::es {
  file { '/etc/exim4/update-exim4.conf.conf':
    source => 'puppet:///modules/eximconf/exim4.conf-es',
    notify => Service['exim4'],
  }
}
