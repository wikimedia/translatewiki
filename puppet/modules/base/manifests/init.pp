# = Class: base
#
# Provides base configuration for servers.
#
class base (
  String $maintenance_user,
  String $bin_dir,
  String $domain,
) {
  $packages = [
    # Basic packages
    'ack',
    'acl',
    'apticron', # Daily message about available updates to root
    'bash-completion',
    'colorized-logs', # For execute-and-notify script
    'curl',
    'git',
    'git-review',
    'htop',
    'iftop',
    'iotop', # IO view
    'iperf', # Network performance
    'jnettop', # Network view
    'make',
    'nano',
    'netcat-openbsd',
    'parallel', # Used for e.g. forceSearchIndex.php
    'screen',
    'unattended-upgrades',
    'unzip',
    'wget',
  ]
  stdlib::ensure_packages($packages, {
    ensure => 'present',
  })

  file { '/etc/profile.d/translatewiki.sh':
    content => template('base/translatewiki.sh.erb'),
  }

  file { '/etc/motd':
    ensure  => file,
    owner   => 'root',
    group   => 'root',
    mode    => '0644',
    content => template('base/motd.erb'),
  }

  file { '/scratch':
    ensure => directory,
  }

  file { '/etc/default/locale':
    content => 'LANG="en_US.UTF-8"',
  }

  file { '/etc/systemd/journald.conf.d/':
    ensure => 'directory',
  }

  file { '/etc/systemd/journald.conf.d/50-override.conf':
    content => "[Journal]\nStorage=persistent\n",
  }
}
