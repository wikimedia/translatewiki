# = Class: awstats
#
# Provides some stats for us.
#
class awstats (
  Optional[String] $webauth = undef,
) {
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

  # Basic auth accounts for stats.translatewiki.net, read by the nginx workers
  if $webauth {
    file { '/etc/webauth':
      content => Sensitive($webauth),
      owner   => 'root',
      group   => 'www-data',
      mode    => '0640',
    }
  }

  file { '/etc/nginx/sites/stats.translatewiki.net':
    source  => 'puppet:///modules/awstats/stats.translatewiki.net',
    require => [Package['nginx'], Service['fcgiwrap']],
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
