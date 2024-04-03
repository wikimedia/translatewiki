# = Class: base::firewall::post
#
# Provides installation and configuration for translatewiki.net firewall
#
class base::firewall::post {
  firewall { '999 drop all':
    proto  => 'all',
    jump   => 'drop',
    before => undef,
  }
}
