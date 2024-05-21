# = Class: awstats
#
# Provides some stats for us.
#
class awstats {
  include fcgiwrap

  package { 'awstats':
    ensure => present,
  }

  package { [
    'geoip-database',
    'libgeo-ip-perl',
  ]:
    ensure => present,
  }

  File {
    require => Package['awstats']
  }

  file { '/etc/awstats/awstats.conf.local':
    source => 'puppet:///modules/awstats/awstats.conf.local',
  }

  file { '/etc/nginx/sites/stats.translatewiki.net':
    source  => 'puppet:///modules/awstats/stats.translatewiki.net',
    require => [Package['nginx-full'], Service['fcgiwrap']],
    notify  => Service['nginx'],
  }

  systemd::timer { 'awstats.timer':
    timer_content   => template('awstats/awstats.timer.erb'),
    service_content => template('awstats/awstats.service.erb'),
    active          => true,
    enable          => true,
  }

  systemd::timer { 'mlebstats.timer':
    timer_content   => template('awstats/mlebstats.timer.erb'),
    service_content => template('awstats/mlebstats.service.erb'),
    active          => true,
    enable          => true,
  }
}
