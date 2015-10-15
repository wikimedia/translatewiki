class eximconf::web1 {
  class { 'exim':
    source  => 'puppet:///modules/eximconf/exim4.conf-web1'
  }
}
