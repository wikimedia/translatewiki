# = Class: repong::autobackport
#
# Sets up automated backports using systemd timers
#
class repong::autobackport (
  String $l10nbot_user,
  String $bin_dir,
) {
  systemd::unit_file { 'autobackport-mediawiki@.service':
    content => template('repong/autobackport-mediawiki@.service.erb'),
  }

  Repong::Autobackport_mediawiki_timer {
    require => User[ $l10nbot_user ],
  }

  # Using early European mornings when CI has low use

  # LTS; supported till November 2025
  repong::autobackport_mediawiki_timer { 'REL1_39':
    branch => 'REL1_39',
    when   => 'Mon 06:30:00',
  }

  # supported till May 2024
  repong::autobackport_mediawiki_timer { 'REL1_40':
    branch => 'REL1_40',
    when   => 'Tue 06:30:00',
  }

  # supported till December 2024
  repong::autobackport_mediawiki_timer { 'REL1_41':
    branch => 'REL1_41',
    when   => 'Wed 06:30:00',
  }
}
