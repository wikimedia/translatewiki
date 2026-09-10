# = Class: profile::mydumper
#
# Installs mydumper, used by the restic database backup pre-command.
# Not in Debian 13, so it comes from the upstream repository:
# https://mydumper.github.io/mydumper/docs/html/installing.html
#
# The signing key is kept here rather than fetched from the keyserver, which
# returns the armor headers in a different order on every request and so made
# puppet rewrite the file and refresh apt on every run.
# Fingerprint: 1D357EA7D10C9320371BDD0279EA15C0E82E34BA
#
class profile::mydumper {
  include ::apt

  apt::keyring { 'mydumper.asc':
    source => 'puppet:///modules/profile/mydumper.asc',
  }

  apt::source { 'mydumper':
    location => 'https://mydumper.github.io/mydumper/repo/apt/debian',
    release  => $facts['os']['distro']['codename'],
    repos    => 'main',
    keyring  => '/etc/apt/keyrings/mydumper.asc',
    require  => Apt::Keyring['mydumper.asc'],
  }

  package { 'mydumper':
    ensure  => present,
    require => [ Apt::Source['mydumper'], Class['apt::update'] ],
  }
}
