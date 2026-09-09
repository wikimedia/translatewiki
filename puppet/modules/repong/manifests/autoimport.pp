# = Class: repong::autoimport
#
# Sets up automated imports using systemd timers
#
class repong::autoimport (
  String $domain,
  String $translate_cache_dir,
  String $l10nbot_user = lookup('repong::l10nbot_user'),
  String $bin_dir      = lookup('repong::bin_dir'),
) {
  file { '/etc/update-motd.d':
    ensure => directory,
    owner  => 'root',
    group  => 'root',
    mode   => '0755',
  }

  file { '/etc/update-motd.d/20-twn':
    ensure  => file,
    owner   => 'root',
    group   => 'root',
    mode    => '0755',
    content => template('repong/20-twn.erb'),
    require => File['/etc/update-motd.d'],
  }

  Repong::Autoimport_timer {
    require => User[ $l10nbot_user ],
  }

  repong::autoimport_timer { 'autoimport':
    when => '*-*-* 0/2:30:00 UTC',
  }

  repong::autoimport_timer { 'autoimport-mediawiki':
    command => 'autoimport-mediawiki',
    when    => '*-*-* 6,12,20:48:00 UTC',
  }
}
