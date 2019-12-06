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

  file { '/etc/cron.d/awstats':
    source  => 'puppet:///modules/awstats/awstats.cron',
  }

  file { '/etc/awstats/awstats.conf.local':
    source  => 'puppet:///modules/awstats/awstats.conf.local',
  }

  file { '/etc/nginx/sites/stats.translatewiki.net':
    source  => 'puppet:///modules/awstats/stats.translatewiki.net',
    require => [Package['nginx-light'], Service['fcgiwrap']],
    notify  => Service['nginx'],
  }
}
