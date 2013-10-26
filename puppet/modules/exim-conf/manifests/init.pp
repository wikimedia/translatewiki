class exim-conf {
  file { '/etc/mailname':
    content  => 'translatewiki.net'
  }

  class { 'exim':
    source  => 'puppet:///modules/exim-conf/exim4.conf'
  }
}
