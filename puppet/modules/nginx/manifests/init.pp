# = Class: nginx
#
# Provides installation and configuration information for the nginx package.
#
class nginx {
  include apt
  apt::ppa { 'ppa:nginx/stable': }

  package { 'nginx':
    ensure => present,
    name   => 'nginx-light',
  }

  service { 'nginx':
    ensure  => running,
    enable  => true,
    require => Package['nginx'],
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
}
