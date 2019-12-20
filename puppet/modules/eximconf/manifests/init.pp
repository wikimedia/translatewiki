# = Class: eximconf
#
# Provides basic exim configuration for all servers.
#
class eximconf (
  String $domain          = 'translatewiki.net',
  Array $local_interfaces = [],
  Array $relay_nets       = $local_interfaces,
  String $smarthost       = 'nospam.nichework.com',
) {
  file { '/etc/mailname':
    content => "${domain}\n"
  }

  file { '/etc/exim4/update-exim4.conf.conf':
    content => template('eximconf/exim4.conf.erb'),
    notify  => Service['exim4'],
  }

  package { 'exim4':
    ensure => present,
  }

  service { 'exim4':
    ensure => running,
    enable => true,
  }
}
