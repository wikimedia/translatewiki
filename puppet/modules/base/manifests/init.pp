# = Class: base
#
# Provides base configuration for servers.
#
class base {
  package { [
    # Basic packages
    'ack',
    'acl',
    'apticron', # Daily message about available updates to root
    'bash-completion',
    'git',
    'htop',
    'iftop',
    'iotop', # IO view
    'iperf', # Network performance
    'jnettop', # Network view
    'make',
    'mlocate',
    'mosh', # ssh for bad connections
    'nano',
    'netcat-openbsd',
    'screen',
    'unattended-upgrades',
    'unzip',
    'wget',
    ]: ensure => present,
  }

  # Download git-review from pip, the version in current LTS has annoying bugs
  package { 'git-review':
    ensure   => present,
    provider => pip3,
  }

  file { '/etc/environment':
    content => 'PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/home/betawiki/config/bin"',
  }

  file { '/etc/default/locale':
    content => 'LANG="en_US.UTF-8"',
  }
}
