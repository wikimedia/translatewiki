# = Class: base::firewall::pre
#
# Provides installation and configuration for translatewiki.net firewall
#
class base::firewall::pre {
  Firewall {
    require => undef,
  }

  # Default firewall rules
  firewall { '000 accept all icmp':
    proto  => 'icmp',
    action => 'accept',
  }
  -> firewall { '000 accept all icmpv6':
    proto  => 'ipv6-icmp',
    action => 'accept',
  }
  -> firewall { '001 accept all to lo interface':
    proto   => 'all',
    iniface => 'lo',
    action  => 'accept',
  }
  -> firewall { '002 reject local traffic not on loopback interface':
    iniface     => '! lo',
    proto       => 'all',
    destination => '127.0.0.1/8',
    action      => 'reject',
  }
  -> firewall { '003 accept related established rules':
    proto  => 'all',
    state  => ['RELATED', 'ESTABLISHED'],
    action => 'accept',
  }
  -> firewall { '006 Allow inbound SSH (v4)':
    dport    => 22,
    proto    => tcp,
    action   => accept,
    provider => 'iptables',
  }
  -> firewall { '006 Allow inbound SSH (v6)':
    dport    => 22,
    proto    => tcp,
    action   => accept,
    provider => 'ip6tables',
  }
}
