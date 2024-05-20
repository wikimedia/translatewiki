# = Class: repong
#
# Sets up central directories for autoimports and autoexports
#
class repong (
  String $config_dir,
  String $l10nbot_user,
  String $import_dir,
  String $export_dir,
  String $bin_dir,
) {
  Exec {
    require => User[ $l10nbot_user ],
  }

  exec { "Create import directory ${import_dir}":
    creates => $import_dir,
    command => "mkdir -p ${import_dir}",
    path    => $::path,
  } -> file { $import_dir:
    ensure => 'directory',
    owner  => $l10nbot_user,
  }

  file { "${import_dir}/repoconfig.yaml":
    ensure => 'link',
    target => "${config_dir}/repoconfig.yaml",
    owner  => $l10nbot_user,
  }

  file { "${import_dir}/sync.lock":
    ensure  => 'file',
    content => '',
    replace => 'no',
    owner   => $l10nbot_user,
    mode    => 'ugo+rw',
  }

  exec { "Create export directory ${export_dir}":
    creates => $export_dir,
    command => "mkdir -p ${export_dir}",
    path    => $::path,
  } -> file { $export_dir:
    ensure => 'directory',
    owner  => $l10nbot_user,
  }

  file { "${export_dir}/repoconfig.yaml":
    ensure => 'link',
    target => "${config_dir}/repoconfig.yaml",
    owner  => $l10nbot_user,
  }

  file { "${export_dir}/REPONG-VARIANT":
    ensure  => 'file',
    content => 'export',
    owner   => $l10nbot_user,
  }

  systemd::timer { 'repong-cleanups.timer':
    timer_content   => template('repong/repong-cleanups.timer.erb'),
    service_content => template('repong/repong-cleanups.service.erb'),
    active          => true,
    enable          => true,
  }
}
