class nginx {
  include apt
  apt::ppa { 'ppa:nginx/stable': }

  package { 'nginx':
    ensure => present,
    name   => 'nginx-extras',
  }

  service { 'nginx':
    ensure     => running,
    enable     => true,
    hasstatus  => true,
    hasrestart => true,
    require    => Package['nginx'],
  }

  File {
    require => Package['nginx'],
    notify  => Service['nginx'],
  }

  file { '/etc/nginx/nginx.conf':
    source  => 'puppet:///modules/nginx/nginx.conf',
  }

  file { '/etc/nginx/mime.types':
    source  => 'puppet:///modules/nginx/mime.types',
  }

  file { '/etc/nginx/sites':
    ensure => 'directory',
  }

  file { '/etc/logrotate.d/nginx':
    source  => 'puppet:///modules/nginx/logrotate'
  }

#   file { '/etc/nginx/sites-available/translatewiki.net':
#     source  => 'puppet:///modules/nginx/translatewiki.net',
#   }
#
#
#   file { '/etc/nginx/sites-available/offline':
#     source  => 'puppet:///modules/nginx/offline',
#   }
#
#   file { '/etc/ssl/private/rapidssl.pem':
#     source  => 'puppet:///modules/nginx/rapidssl.pem',
#   }
#
#   exec { 'dhparam':
#     command => '/usr/bin/openssl dhparam -out /etc/ssl/certs/dhparam.pem 2048',
#     creates => '/etc/ssl/certs/dhparam.pem'
#   }
}
