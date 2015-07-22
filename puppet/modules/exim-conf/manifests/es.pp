class exim-conf::es {
  class { 'exim':
    source  => 'puppet:///modules/exim-conf/exim4.conf-es'
  }
}
