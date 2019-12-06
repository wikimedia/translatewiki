# = Class: eximconf::new
#
# Provides exim configuration for server new.
#
class eximconf::new {
  file { '/etc/exim4/update-exim4.conf.conf':
    source => 'puppet:///modules/eximconf/exim4.conf-new',
    notify => Service['exim4'],
  }
}
