# = Class: wiki
#
# Configures various wiki services
#
class wiki (
  String $config_dir,
  String $production_dir,
  String $maintenance_user,
  String $repo_user,
  String $work_dir,
  String $domain,
) {
  file { '/etc/wikisettings':
    content => template('wiki/wikisettings.erb'),
  }

  file { '/etc/cron.d/wikimaintenance':
    content => template('wiki/wikimaintenance.erb'),
  }

  file { '/etc/systemd/system/mw-jobrunner.service':
    content => template('wiki/mw-jobrunner.service.erb'),
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
    'composer',
  ]
  ensure_packages($packages, {
    ensure => 'present',
  })
}
