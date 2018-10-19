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

    # Inactive accounts
    'robin': # Last login 2012-11-12
      ensure         => absent,
      uid            => 1008,
      gid            => 'users',
      groups         => [],
      shell          => '/bin/bash',
      password       => '!',
      managehome     => true,
      purge_ssh_keys => true,
      home           => '/home/robin',
      comment        => 'Robin Pepermans';

    'lcawte':
      ensure         => absent,
      uid            => 1010,
      gid            => 'users',
      groups         => [],
      shell          => '/bin/bash',
      password       => '!',
      managehome     => true,
      purge_ssh_keys => true,
      home           => '/home/lcawte',
      comment        => 'Lewis Cawte';

    'santhosh':
      ensure         => absent,
      uid            => 1012,
      gid            => 'users',
      groups         => [],
      shell          => '/bin/bash',
      password       => '!',
      managehome     => true,
      purge_ssh_keys => true,
      home           => '/home/santhosh',
      comment        => 'Santhosh Thottingal';

    'niedzielski':
      ensure         => absent,
      uid            => 1021,
      gid            => 'users',
      groups         => [],
      shell          => '/bin/bash',
      password       => '!',
      managehome     => true,
      purge_ssh_keys => true,
      home           => '/home/niedzielski',
      comment        => 'Stephen Niedzielski';

    'fjalapeno':
      ensure         => absent,
      uid            => 1023,
      gid            => 'users',
      groups         => [],
      shell          => '/bin/bash',
      password       => '!',
      managehome     => true,
      purge_ssh_keys => true,
      home           => '/home/fjalapeno',
      comment        => 'Corey Floyd (WMF)';
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
  }
}
