# = Class: base
#
# Provides base configuration for servers.
#
class base (
  String $maintenance_user,
  String $bin_dir,
  Optional[String] $mount_device = undef,
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
    'htop',
    'iftop',
    'iotop', # IO view
    'iperf', # Network performance
    'jnettop', # Network view
    'make',
    'mlocate',
    'mydumper',
    'nano',
    'netcat-openbsd',
    'nfs-common',
    'parallel', # Used for e.g. forceSearchIndex.php
    'python3-pip', # for git-review
    'screen',
    'unattended-upgrades',
    'unzip',
    'wget',
  ]
  stdlib::ensure_packages($packages, {
    ensure => 'present',
  })

  # Download git-review from pip, the version in current LTS has annoying bugs
  package { 'git-review':
    ensure   => present,
    provider => pip3,
    require  => Package['python3-pip'],
  }

  file { '/etc/profile.d/translatewiki.sh':
    content => template('base/translatewiki.sh.erb'),
  }

  file { '/scratch':
    ensure => directory,
  }

  if $mount_device {
    mount { '/scratch':
      ensure  => 'mounted',
      device  => $mount_device,
      fstype  => 'nfs',
      options => 'rw',
      require => File['/scratch'],
    }
  }

  file { '/etc/default/locale':
    content => 'LANG="en_US.UTF-8"',
  }

  file { '/etc/systemd/journald.conf':
    content => "[Journal]\nStorage=persistent\n",
  }
}
