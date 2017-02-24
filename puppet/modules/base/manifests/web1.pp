# = Class: base::web1
#
# Provides base configuration for server web1.
#
class base::web1 {
  file { '/etc/network/interfaces':
    source => 'puppet:///modules/base/interfaces-web1'
  }
}
