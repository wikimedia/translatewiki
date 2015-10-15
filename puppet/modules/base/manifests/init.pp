# = Class: base
#
# Provides base configuration for servers.
#
class base {
  package { [
    # Basic packages
    'acl',
    'ack-grep',
    'apticron', # Daily message about available updates to root
    'bash-completion',
    'git-core',
    'htop',
    'iotop', # IO view
    'iperf', # Network performance
    'jnettop', # Network view
    'make',
    'mosh', # ssh for bad connections
    'nano',
    'netcat-openbsd',
    'screen',
    'unzip',
    'unattended-upgrades',
    'wget',
    ]: ensure => present,
  }

  # Download git-review from pip, the version in current LTS has annoying bugs
  package { 'git-review':
    ensure   => present,
    provider => pip,
  }

  file { '/etc/environment':
    content => 'PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/home/betawiki/config/bin"',
  }

  file { '/etc/default/locale':
    content => 'LANG="en_US.UTF-8"',
  }

  # Puppet, please stfu
  file { '/etc/puppet/hiera.yaml':
    content => '',
  }
}
