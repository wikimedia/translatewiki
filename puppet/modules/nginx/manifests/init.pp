class nginx {
  package { 'nginx':
    ensure => present,
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
    source  => 'puppet:///modules/nginx/sites/translatewiki.net',
  }

  file { '/etc/nginx/sites-enabled/translatewiki.net':
    ensure => 'link',
    target => '../sites-available/translatewiki.net',
  }

  file { '/etc/nginx/sites-available/stats.translatewiki.net':
    source  => 'puppet:///modules/nginx/sites/stats.translatewiki.net',
  }

  file { '/etc/nginx/sites-enabled/stats.translatewiki.net':
    ensure => 'link',
    target => '../sites-available/stats.translatewiki.net',
  }

  file { '/etc/nginx/sites-available/lists.translatewiki.net':
    source  => 'puppet:///modules/nginx/sites/lists.translatewiki.net',
  }

  file { '/etc/nginx/sites-enabled/lists.translatewiki.net':
    ensure => 'link',
    target => '../sites-available/lists.translatewiki.net',
  }

  file { '/etc/nginx/sites-enabled/default':
    ensure => 'absent',
  }
}
