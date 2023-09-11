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
  # LTS; supported till September 2023
  repong::autobackport_mediawiki_timer { 'REL1_35':
    branch => 'REL1_35',
    when   => 'Mon 06:30:00',
  }

  # supported till June 2023
  repong::autobackport_mediawiki_timer { 'REL1_38':
    active => false,
    branch => 'REL1_38',
    when   => 'Wed 06:30:00',
  }

  # LTS; supported till November 2025
  repong::autobackport_mediawiki_timer { 'REL1_39':
    branch => 'REL1_39',
    when   => 'Tue 06:30:00',
  }

  # supported till May 2024
  repong::autobackport_mediawiki_timer { 'REL1_40':
    branch => 'REL1_40',
    when   => 'Thu 06:30:00',
  }
}
