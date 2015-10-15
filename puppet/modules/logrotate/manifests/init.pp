# = Class: logrotate
#
# Provides logrotate configuration.
#
class logrotate {
  file { '/etc/logrotate.d/twn':
    source  => 'puppet:///modules/logrotate/twn'
  }
}
