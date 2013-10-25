class exim-conf {
  file { '/etc/mailname':
    source  => 'puppet:///modules/exim-conf/mailname'
  }

  class { 'exim':
    source  => 'puppet:///modules/exim-conf/exim4.conf'
  }
}
