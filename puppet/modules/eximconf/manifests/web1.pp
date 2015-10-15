# = Class: eximconf::web1
#
# Provides exim configuration for server web1.
#
class eximconf::web1 {
  class { 'exim':
    source  => 'puppet:///modules/eximconf/exim4.conf-web1'
  }
}
