# = Class: eximconf
#
# Provides basic exim configuration for all servers.
#
class eximconf {
  file { '/etc/mailname':
    content  => "translatewiki.net\n"
  }

  package { 'exim4':
    ensure => present,
  }

  service { 'exim4':
    ensure => running,
    enable => true,
  }
}
