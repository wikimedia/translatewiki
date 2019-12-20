# = Class: ssh
#
# Provides sshd configuration for servers.
#
class sshd {
  file { '/etc/ssh/sshd_config':
    source => 'puppet:///modules/sshd/sshd_config',
    notify => Servicep['sshd'],
  }

  service { 'sshd':
    ensure => running,
    enable => true,
  }
}
