class exim-conf::web1 {
  class { 'exim':
    source  => 'puppet:///modules/exim-conf/exim4.conf-web1'
  }
}
