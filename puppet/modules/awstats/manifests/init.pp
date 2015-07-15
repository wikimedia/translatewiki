# = Class: awstats
#
# Provides some stats for us.
#
class awstats {
  include fcgiwrap

  package { 'awstats':
    ensure => present,
  }

  File {
    require => Package['awstats']
  }

  file { '/etc/cron.d/awstats':
    source  => 'puppet:///modules/awstats/awstats.cron',
  }

  file { '/etc/awstats/awstats.conf':
    source  => 'puppet:///modules/awstats/awstats.conf',
  }

  file { '/etc/nginx/sites/stats.translatewiki.net':
    source  => 'puppet:///modules/awstats/stats.translatewiki.net',
    require => [Package['nginx'], Service['fcgiwrap']],
    notify  => Service['nginx'],
  }
}
