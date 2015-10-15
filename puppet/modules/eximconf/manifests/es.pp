class eximconf::es {
  class { 'exim':
    source  => 'puppet:///modules/eximconf/exim4.conf-es'
  }
}
