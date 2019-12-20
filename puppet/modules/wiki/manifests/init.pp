# = Class: wiki
#
# Configures various wiki stuff. Now mostly crontabs.
#
# == Parameters:
#
# $config:: Where the wiki config is stored.
#
# $user:: What user owns the wiki stuff.
#
#
# == Sample Usage:
#
#   class { 'wiki':
#     config => '/home/betawiki/config',
#     user   => 'betawiki',
#   }
#
class wiki ($config, $user) {
  file { '/etc/cron.d/wikimaintenance':
    content => template('wiki/wikimaintenance.erb'),
  }

  file { '/etc/cron.d/wikistats':
    content => template('wiki/wikistats.erb'),
  }

  file { '/etc/systemd/system/mw-jobrunner.service':
    source  => 'puppet:///modules/wiki/mw-jobrunner.service',
  }

  service { 'mw-jobrunner':
    ensure  => running,
    enable  => true,
    require => File['/etc/systemd/system/mw-jobrunner.service'],
  }

  service { 'poolcounter':
    ensure  => running,
    enable  => true,
    require => Package['poolcounter'],
  }

  $packages = [
    # needed for svg images
    'librsvg2-bin',
    # poolcounter
    'poolcounter',
  ]
  ensure_packages($packages, {
    ensure => 'present',
  })
}
