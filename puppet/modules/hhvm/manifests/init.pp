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

  package { 'hhvm-nightly':
    ensure => present,
    require => Apt::Source['hhvm'],
  }

  # Upstart configuration
  file { '/etc/init/hhvm-fastcgi.conf':
    source  => 'puppet:///modules/hhvm/upstart',
  }

  file { '/etc/hhvm/server.ini':
    source  => 'puppet:///modules/hhvm/server.ini',
  }
}
