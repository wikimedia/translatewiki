# = Class: users
#
# Provides user configuration for servers.
#
class users (
  Hash[String, Data] $users = {},
  Hash[String, Hash[String, String]] $ssh_keys = {},
) {
  package { 'sudo':
    ensure => present,
  }

  file { '/etc/sudoers.d/local':
    source => 'puppet:///modules/users/local',
    mode   => '0440',
  }

  group {
    # Ability to sudo
    'sysadmin':
      ensure => present,
      gid    => 1001;

    # Ability to update and deploy MediaWiki
    'betawiki':
      ensure => present,
      gid    => 1002;

    # Ability to run maintenance scripts and autoimport*
    'www-shared':
      ensure => present,
      gid    => 1020;

    # Ability to commit translation updates
    'l10n-bot':
      ensure => present,
      gid    => 1021;

    # Debian/nginx
    'www-data':
      ensure => present,
      gid    => 33;
  }

  # www-data:x:33:33:www-data:/var/www:/usr/sbin/nologin
  user {
    # nginx
    'www-data':
      ensure     => present,
      uid        => 33,
      gid        => 'www-data',
      groups     => ['www-shared'],
      shell      => '/usr/sbin/nologin',
      managehome => false,
  }

  $users.each |$name, $cfg| {
    user {
      default:
        ensure         => present,
        gid            => 'users',
        groups         => [],
        shell          => '/bin/bash',
        password       => '!',
        managehome     => true,
        purge_ssh_keys => false;
      $name:
        *    => $cfg,
        home => "/home/${name}",
    }
  }

  $ssh_keys.each |$name, $cfg| {
    ssh_authorized_key {
      default:
        ensure => present;
      $name:
        * => $cfg;
    }
  }
}
