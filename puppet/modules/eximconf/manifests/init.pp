# = Class: eximconf
#
# Provides basic exim configuration for all servers.
#
class eximconf {
  file { '/etc/mailname':
    content  => "translatewiki.net\n"
  }
}
