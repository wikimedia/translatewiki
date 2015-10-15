# = Class: base::es
#
# Provides base configuration for server es.
#
class base::es {
  file { '/etc/network/interfaces':
    source => 'puppet:///modules/base/interfaces-es'
  }
}
