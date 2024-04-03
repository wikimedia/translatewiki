# = Class: ssh
#
# Provides sshd configuration for servers.
#
class sshd {
  $packages = [
    'mosh', # ssh for bad connections
  ]
  stdlib::ensure_packages($packages, {
    ensure => 'present',
  })

  file { '/etc/ssh/sshd_config':
    source => 'puppet:///modules/sshd/sshd_config',
    notify => Service['sshd'],
  }

  service { 'sshd':
    ensure => running,
    enable => true,
  }

  # set up the default parameters for all firewall rules
  Firewall {
    before  => Class['base::firewall::post'],
    require => Class['base::firewall::pre'],
  }

  firewall { '009 Allow inbound mosh (v4)':
    dport    => '60000:61000',
    proto    => udp,
    jump     => accept,
    protocol => 'IPv4',
  }

  firewall { '009 Allow inbound mosh (v6)':
    dport    => '60000:61000',
    proto    => udp,
    jump     => accept,
    protocol => 'IPv6',
  }
}
