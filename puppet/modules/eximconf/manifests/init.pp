# = Class: eximconf
#
# Provides basic exim configuration for all servers.
#
class eximconf (
  String $mail_domain     = 'translatewiki.net',
  Array $local_interfaces = [],
  Array $relay_nets       = $local_interfaces,
  String $smarthost       = 'nospam.nichework.com',
) {
  file { '/etc/mailname':
    content => "${mail_domain}\n"
  }

  file { '/etc/exim4/update-exim4.conf.conf':
    content => template('eximconf/exim4.conf.erb'),
    notify  => Service['exim4'],
  }

  $packages = [
    'exim4',
  ]
  ensure_packages($packages, {
    ensure => 'present',
  })

  service { 'exim4':
    ensure => running,
    enable => true,
  }

  # set up the default parameters for all firewall rules
  Firewall {
    before  => Class['base::firewall::post'],
    require => Class['base::firewall::pre'],
  }

  firewall { '030 Allow inbound SMTP (v4)':
    dport    => 25,
    proto    => tcp,
    action   => accept,
    provider => 'iptables',
  }

  firewall { '030 Allow inbound SMTP (v6)':
    dport    => 25,
    proto    => tcp,
    action   => accept,
    provider => 'ip6tables',
  }
}
