# = Class: repong
#
# Sets up central directories for autoimports and autoexports
#
class repong (
  String $config_dir,
  String $repo_user,
  String $l10nbot_user,
  String $import_dir,
  String $export_dir,
) {
  Exec {
    require => [ User[ $repo_user ], User[ $l10nbot_user ] ],
  }

  # TODO: Also use l10n-bot for imports?
  exec { "Create import directory ${import_dir}":
    creates => $import_dir,
    command => "mkdir -p ${import_dir}",
    path    => $::path,
  } -> file { $import_dir:
    ensure => 'directory',
    owner  => $repo_user,
  }

  file { "${import_dir}/repoconfig.yaml":
    ensure => 'link',
    target => "${config_dir}/repoconfig.yaml",
    owner  => $repo_user,
  }

  file { "${import_dir}/sync.lock":
    ensure  => 'file',
    content => '',
    replace => 'no',
    owner   => $repo_user,
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
}
