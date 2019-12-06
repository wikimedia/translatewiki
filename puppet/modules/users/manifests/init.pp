# = Class: users
#
# Provides user configuration for servers.
#
class users {
  # Pull down the main aliases file
  file { '/etc/aliases':
    source => 'puppet:///modules/users/aliases'
  }

  # Rebuild the database, but only when the file changes
  exec { 'newaliases':
    path        => ['/usr/bin', '/usr/sbin'],
    subscribe   => File['/etc/aliases'],
    refreshonly => true
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
  }

  user {
    # Active accounts
    'siebrand':
      ensure         => present,
      uid            => 1000,
      gid            => 'users',
      groups         => ['betawiki', 'www-shared', 'sysadmin'],
      shell          => '/bin/bash',
      password       => '!',
      managehome     => true,
      purge_ssh_keys => true,
      home           => '/home/siebrand',
      comment        => 'Siebrand Mazeland';

    'nike':
      ensure         => present,
      uid            => 1001,
      gid            => 'users',
      groups         => ['betawiki', 'www-shared', 'l10n-bot', 'sysadmin'],
      shell          => '/bin/bash',
      password       => '!',
      managehome     => true,
      purge_ssh_keys => true,
      home           => '/home/nike',
      comment        => 'Niklas Laxström';

    'betawiki':
      ensure     => present,
      uid        => 1002,
      gid        => 'users',
      groups     => ['betawiki', 'www-shared'],
      shell      => '/bin/bash',
      password   => '!',
      managehome => true,
      home       => '/home/betawiki',
      comment    => 'Ur Wiki';

    'reedy':
      ensure     => present,
      uid        => 1004,
      gid        => 'users',
      groups     => ['betawiki'],
      shell      => '/bin/bash',
      password   => '!',
      managehome => true,
      home       => '/home/reedy',
      comment    => 'Sam Reed';

    'raymond':
      ensure     => present,
      uid        => 1005,
      gid        => 'users',
      groups     => ['betawiki', 'www-shared', 'l10n-bot'],
      shell      => '/bin/bash',
      password   => '!',
      managehome => true,
      home       => '/home/raymond',
      comment    => 'Raimond Spekking';

    'mah':
      ensure     => present,
      uid        => 1006,
      gid        => 'users',
      groups     => [],
      shell      => '/bin/bash',
      password   => '!',
      managehome => true,
      home       => '/home/mah',
      comment    => 'Mark Hershberger';

    'amir':
      ensure     => present,
      uid        => 1011,
      gid        => 'users',
      groups     => ['betawiki', 'www-shared', 'l10n-bot'],
      shell      => '/bin/bash',
      password   => '!',
      managehome => true,
      home       => '/home/amir',
      comment    => 'Amir Aharoni';

    'kartik':
      ensure     => present,
      uid        => 1018,
      gid        => 'users',
      groups     => ['betawiki'],
      shell      => '/bin/bash',
      password   => '!',
      managehome => true,
      home       => '/home/kartik',
      comment    => 'Kartik Mistry';

    'abi':
      ensure         => present,
      uid            => 1024,
      gid            => 'users',
      groups         => ['betawiki', 'www-shared', 'l10n-bot'],
      shell          => '/bin/bash',
      password       => '!',
      managehome     => true,
      purge_ssh_keys => true,
      home           => '/home/abi',
      comment        => 'Abijeet Patro';
  }

  ssh_authorized_key {
    'ed25519-key-20171129 siebrand@kitano.nl':
      ensure => present,
      user   => 'siebrand',
      type   => 'ssh-ed25519',
      key    => 'AAAAC3NzaC1lZDI1NTE5AAAAIHxdP9KdKSwuVpRaaevBbuCgPTV+2rvIe6Y57iOP57I6';

    'nike@jadekukka':
      ensure => present,
      user   => 'nike',
      type   => 'ssh-ed25519',
      key    => 'AAAAC3NzaC1lZDI1NTE5AAAAIJPypk/NLqKJPMj5prKlHOJLjhiXpKxIEaEM2P5mZrZf';

    'abijeetpatro@gmail.com':
      ensure => present,
      user   => 'abi',
      type   => 'ssh-rsa',
      # lint:ignore:140chars
      key    => 'AAAAB3NzaC1yc2EAAAADAQABAAACAQCmcWeE++IqwY9yWYj6Rmir18jckPcSvp/K83/nds69gJVraPUQ0r5qJVwQ69m0tUaA0sypTd8L1rmMzHracxiiP+5Hp0h/A2xITr/QQ7/f6IsP/ke5wlSX0M6yCrnx2yJeZASgkDpEONT8mtzqd8QA3m/dP2l9Sn5PSsKOg2BEQe8Lw8cOaKGob6yQgS8n+vzgfP+LwSZSIp1dClK9CDwHbs9RhebUmlSPpsws+0JrBfgpsGiu/LDUme+25KAjpn1H2s/tNck6EWC3BggYNyeQPQPyB1EkfoTuHfoQEm+WP1hUzpb3JmzOzNTduvgGta0tOlew+hjleO8z88jzgPM5EtpP6lep5IAi8q5rrV3ibRXHN1fkmKyPHBa32n35dv7It196ECtfpSYwcaqOSIllCJTuKNN7zbPBbCODNrqQOX7zon62JgVcCQ4gPbYoh0BY1R/fz0b+DQymwW0A+OhOlHzZk/B8I7f7e8T0b2JGMwdvcs7hV1Ny0Ohf+Gea2cGeqt8F4lNyI4q10pUrWmw3iXhFn9MRt16myifLPxzW1kGoLmUBGsdQLfhBaNYXI2W/gV+2IGnWY1QHlMA4R0Ki4CcyDOF/FSvR3eT/DGxflI4S9hA3f6AJ6HOvvHGl9kJP0RACM4pS6wNjuEcS/u7t98xCowGF5Kih1MLnKFH6+Q==';
      # lint:endignore
  }
}
