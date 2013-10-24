class users {
  group { "betawiki":
    ensure => present,
    gid => 1002
  }

  user {
    'siebrand':
      ensure     => present,
      uid        => 1000,
      gid        => 'users',
      groups     => ['betawiki'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/siebrand',
      comment    => 'Siebrand Mazeland';
    'nike':
      ensure     => present,
      uid        => 1001,
      gid        => 'users',
      groups     => ['betawiki'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/nike',
      comment    => 'Niklas Laxstrom';
    'betawiki':
      ensure     => present,
      uid        => 1002,
      gid        => 'users',
      groups     => ['betawiki'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/betawiki',
      comment    => 'Ur Wiki';
    'minuteelectron': # Last login 2011-09-11
      ensure     => absent,
      uid        => 1003,
      gid        => 'users',
      groups     => ['betawiki'],
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
      groups     => ['betawiki'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/raymond',
      comment    => 'Raimond Spekking';
    'mah':
      ensure     => present,
      uid        => 1006,
      gid        => 'users',
      groups     => [''],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/mah',
      comment    => 'Mark Hershberger';
    'ialex':
      ensure     => present,
      uid        => 1007,
      gid        => 'users',
      groups     => ['betawiki'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/ialex',
      comment    => 'Alexandre Emsenhuber';
    'robin': # Last login 2012-11-12
      ensure     => absent,
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
      groups     => ['betawiki'],
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
      groups     => ['betawiki'],
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
      groups     => ['betawiki'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/chad',
      comment    => 'Chad Horohoe';
    'valhallasw': # Was for rbot account, no longer in use.
      ensure     => absent,
      uid        => 1014,
      gid        => 'users',
      groups     => [''],
      shell      => '/usr/sbin/nologin',
      password   => '',
      managehome => true,
      home       => '/home/valhallasw',
      comment    => 'Merlijn van Deen';
    'antoine': # Last login 2012-04-04
      ensure     => absent,
      uid        => 1015,
      gid        => 'users',
      groups     => ['betawiki'],
      shell      => '/bin/bash',
      password   => '',
      managehome => true,
      home       => '/home/antoine',
      comment    => 'Antoine Musso';
    'rbot': # No longer in use.
      ensure     => absent,
      uid        => 1016,
      gid        => 'users',
      groups     => [''],
      shell      => '/usr/sbin/nologin',
      password   => '',
      managehome => true,
      home       => '/home/rbot',
      comment    => 'Reviewer Bot';
    'tor':
      ensure     => present,
      uid        => 1017,
      gid        => 'users',
      groups     => ['betawiki'],
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
  }
}
