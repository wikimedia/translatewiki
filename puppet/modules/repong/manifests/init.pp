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
  Hash[String, String] $forge_tokens = {},
) {
  Exec {
    require => User[ $l10nbot_user ],
  }

  exec { "Create import directory ${import_dir}":
    creates => $import_dir,
    command => "mkdir -p ${import_dir}",
    path    => $facts['path'],
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
    path    => $facts['path'],
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

  unless empty($forge_tokens) {
    file { '/etc/l10n-bot':
      ensure => 'directory',
      owner  => 'root',
      group  => $l10nbot_user,
      mode   => '0750',
    }

    $env_content = $forge_tokens.map |$name, $token| {
      "export ${name}=${token}\n"
    }.join('')

    file { '/etc/l10n-bot/env':
      content => Sensitive($env_content),
      owner   => 'root',
      group   => $l10nbot_user,
      mode    => '0640',
    }
  }

  systemd::timer { 'repong-cleanups.timer':
    timer_content   => template('repong/repong-cleanups.timer.erb'),
    service_content => template('repong/repong-cleanups.service.erb'),
    active          => true,
    enable          => true,
  }
}
