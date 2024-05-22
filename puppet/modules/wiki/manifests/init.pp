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
  String $l10nbot_user,
  String $autoimport_dir,
  String $autoexport_dir,
  String $lockfile,
  String $bin_dir,
) {
  file { '/etc/wikisettings':
    content => template('wiki/wikisettings.erb'),
  }

  systemd::timer { 'mw-supportedlanguages.timer':
    timer_content   => template('wiki/mw-supportedlanguages.timer.erb'),
    service_content => template('wiki/mw-supportedlanguages.service.erb'),
    active          => true,
    enable          => true,
  }

  systemd::timer { 'mw-completeexternaltranslation.timer':
    timer_content   => template('wiki/mw-completeexternaltranslation.timer.erb'),
    service_content => template('wiki/mw-completeexternaltranslation.service.erb'),
    active          => true,
    enable          => true,
  }

  systemd::timer { 'mw-sitemap.timer':
    timer_content   => template('wiki/mw-sitemap.timer.erb'),
    service_content => template('wiki/mw-sitemap.service.erb'),
    active          => true,
    enable          => true,
  }

  systemd::timer { 'mw-specialpages.timer':
    timer_content   => template('wiki/mw-specialpages.timer.erb'),
    service_content => template('wiki/mw-specialpages.service.erb'),
    active          => true,
    enable          => true,
  }

  systemd::timer { 'mw-translationexports.timer':
    timer_content   => template('wiki/mw-translationexports.timer.erb'),
    service_content => template('wiki/mw-translationexports.service.erb'),
    active          => true,
    enable          => true,
  }

  file { '/etc/systemd/system/mw-jobrunner.service':
    content => template('wiki/mw-jobrunner.service.erb'),
  }
  ~> service { 'mw-jobrunner':
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
    'poolcounter',
    'composer',
  ]
  stdlib::ensure_packages($packages, {
    ensure => 'present',
  })
}
