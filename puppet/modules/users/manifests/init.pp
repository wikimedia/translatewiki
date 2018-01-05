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

  group { 'betawiki':
    ensure => present,
    gid    => 1002
  }

  group { 'www-shared':
    ensure => present,
    gid    => 1020
  }

  user {
    'siebrand':
      ensure         => present,
      uid            => 1000,
      gid            => 'users',
      groups         => ['betawiki', 'www-shared'],
      shell          => '/bin/bash',
      password       => '',
      managehome     => true,
      purge_ssh_keys => true,
      home           => '/home/siebrand',
      comment        => 'Siebrand Mazeland';
    'nike':
      ensure     => present,
      uid        => 1001,
      gid        => 'users',
      groups     => ['betawiki', 'www-shared'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/nike',
      comment    => 'Niklas Laxstrom';
    'betawiki':
      ensure     => present,
      uid        => 1002,
      gid        => 'users',
      groups     => ['betawiki', 'www-shared'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/betawiki',
      comment    => 'Ur Wiki';
    'minuteelectron': # Last login 2011-09-11
      ensure     => absent,
      uid        => 1003,
      gid        => 'users',
      groups     => [],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/minuteelectron',
      comment    => 'Robert Leverington';
    'reedy':
      ensure     => present,
      uid        => 1004,
      gid        => 'users',
      groups     => ['betawiki'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/reedy',
      comment    => 'Sam Reed';
    'raymond':
      ensure     => present,
      uid        => 1005,
      gid        => 'users',
      groups     => ['betawiki', 'www-shared'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/raymond',
      comment    => 'Raimond Spekking';
    'mah':
      ensure     => present,
      uid        => 1006,
      gid        => 'users',
      groups     => [],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/mah',
      comment    => 'Mark Hershberger';
    'ialex':
      ensure     => absent,
      uid        => 1007,
      gid        => 'users',
      groups     => [],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/ialex',
      comment    => 'Alexandre Emsenhuber';
    'robin': # Last login 2012-11-12
      ensure     => present,
      uid        => 1008,
      gid        => 'users',
      groups     => ['betawiki'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/robin',
      comment    => 'Robin Pepermans';
    'huji': # Last login 2011-09-23
      ensure     => absent,
      uid        => 1009,
      gid        => 'users',
      groups     => [],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/huji',
      comment    => 'Huji Lee';
    'lcawte':
      ensure     => present,
      uid        => 1010,
      gid        => 'users',
      groups     => ['betawiki'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/lcawte',
      comment    => 'Lewis Cawte';
    'amir':
      ensure     => present,
      uid        => 1011,
      gid        => 'users',
      groups     => ['betawiki', 'www-shared'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/amir',
      comment    => 'Amir Aharoni';
    'santhosh':
      ensure     => present,
      uid        => 1012,
      gid        => 'users',
      groups     => ['betawiki'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/santhosh',
      comment    => 'Santhosh Thottingal';
    'chad': # Last login 2012-12-18
      ensure     => absent,
      uid        => 1013,
      gid        => 'users',
      groups     => [],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/chad',
      comment    => 'Chad Horohoe';
    'valhallasw': # Was for rbot account, no longer in use.
      ensure     => absent,
      uid        => 1014,
      gid        => 'users',
      groups     => [],
      shell      => '/usr/sbin/nologin',
      password   => '',
      managehome => true,
      home       => '/home/valhallasw',
      comment    => 'Merlijn van Deen';
    'antoine': # Last login 2012-04-04
      ensure     => absent,
      uid        => 1015,
      gid        => 'users',
      groups     => [],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/antoine',
      comment    => 'Antoine Musso';
    'rbot': # No longer in use.
      ensure     => absent,
      uid        => 1016,
      gid        => 'users',
      groups     => [],
      shell      => '/usr/sbin/nologin',
      password   => '',
      managehome => true,
      home       => '/home/rbot',
      comment    => 'Reviewer Bot';
    'tor':
      ensure     => absent,
      uid        => 1017,
      gid        => 'users',
      groups     => [],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/tor',
      comment    => 'Lukasz Garczewski';
    'kartik':
      ensure     => present,
      uid        => 1018,
      gid        => 'users',
      groups     => ['betawiki'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/kartik',
      comment    => 'Kartik Mistry';
    'yuvipanda':
      ensure     => absent,
      uid        => 1019,
      gid        => 'users',
      groups     => ['betawiki', 'www-shared'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/yuvipanda',
      comment    => 'Yuvi Panda';
    'bsitzmann':
      ensure     => absent,
      uid        => 1020,
      gid        => 'users',
      groups     => ['betawiki', 'www-shared'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/bsitzmann',
      comment    => 'Bernd Sitzmann';
    'niedzielski':
      ensure     => present,
      uid        => 1021,
      gid        => 'users',
      groups     => ['betawiki', 'www-shared'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/niedzielski',
      comment    => 'Stephen Niedzielski';
    'bgerstle':
      ensure     => absent,
      uid        => 1022,
      gid        => 'users',
      groups     => ['betawiki', 'www-shared'],
      shell      => '/usr/sbin/nologin',
      password   => '',
      managehome => true,
      home       => '/home/bgerstle',
      comment    => 'Brian Gerstle (WMF)';
    'fjalapeno':
      ensure     => present,
      uid        => 1023,
      gid        => 'users',
      groups     => ['betawiki', 'www-shared'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/fjalapeno',
      comment    => 'Corey Floyd (WMF)';
  }

  # lint:ignore:140chars
  ssh_authorized_key {
    'siebrand_ssh':
      ensure => present,
      name   => 'ed25519-key-20171129 siebrand@kitano.nl',
      user   => 'siebrand',
      type   => 'ssh-ed25519',
      key    => 'AAAAC3NzaC1lZDI1NTE5AAAAIHxdP9KdKSwuVpRaaevBbuCgPTV+2rvIe6Y57iOP57I6';
    'fjalapeno_ssh':
      ensure => present,
      user   => 'fjalapeno',
      type   => 'rsa',
      key    => 'AAAAB3NzaC1yc2EAAAABIwAAAQEA0FskuPOIjhNL4BGfI9p9FyWLt6DM5hjVMqPSz/puQ96Q1/Q92CTHT5gfthLVla/J/j14wW8G2vvz4ZBC1j123xNM4jbLo2jtbKitSTjr4vVf+Q4KPZ3R2ow/dOoWutKuoNwBBRSBBjjPv/F38aQRba4GRBflKhdOCmn6tUeE/Sk8U7WC7oSIfv2cZ1OC+PP9xFuG7a3FgWflzS+hwDyL3LGRlGlK6k3kI2IyOEwYlaZe/BTGZpC7Af+B1ad7z6PlzpKGDIomvMr8Q6r7GjEnAQnVJtXkMxsT0j4gSbcyUQMoBCzRDe7BR4CkiZXljxrvCGkhATav5gGI2DKT/uijlw==';
  }
  # lint:endignore
}
