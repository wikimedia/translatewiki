class nginx {
  include apt
  apt::ppa { 'ppa:nginx/stable': }

  package { 'nginx':
    ensure  => present,
    name    => 'nginx-extras',
  }

  service { 'nginx':
    ensure     => running,
    enable     => true,
    hasstatus  => true,
    hasrestart => true,
    require    => [ Package['nginx'], Service['php5-fpm'] ],
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

  file { '/etc/nginx/sites-available/translatewiki.net':
    source  => 'puppet:///modules/nginx/translatewiki.net',
  }

  file { '/etc/nginx/sites-enabled/translatewiki.net':
    ensure => 'link',
    target => '../sites-available/translatewiki.net',
  }

  file { '/etc/nginx/sites-available/translatewiki.org':
    source  => 'puppet:///modules/nginx/translatewiki.org',
  }

  file { '/etc/nginx/sites-enabled/translatewiki.org':
    ensure => 'link',
    target => '../sites-available/translatewiki.org',
  }

  file { '/etc/nginx/sites-enabled/default':
    ensure => 'absent',
  }

  file { '/etc/nginx/sites-available/offline':
    source  => 'puppet:///modules/nginx/offline',
  }

}
