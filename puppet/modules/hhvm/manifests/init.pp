class hhvm {
  include apt

  apt::source { 'hhvm':
    location    => 'http://dl.hhvm.com/ubuntu',
    repos       => 'main',
    key         => '1BE7A449',
    key_server  => 'pgp.mit.edu',
    include_src => false,
  }

  apt::ppa { 'ppa:mapnik/boost': }

  package { 'hhvm-fastcgi':
    ensure => present,
    require => Apt::Source['hhvm'],
  }

  service { 'hhvm-fastcgi':
    ensure     => running,
    enable     => true,
    hasstatus  => true,
    hasrestart => true,
    require    => [ Package['hhvm-fastcgi'] ],
  }
}
