# = Class: repong::autobackport
#
# Sets up automated backports using systemd timers
#
class repong::autobackport (
  String $l10nbot_user = lookup('repong::l10nbot_user'),
  String $bin_dir      = lookup('repong::bin_dir'),
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
    when   => 'Mon 05:30:00 UTC',
  }

  # supported till May 2024
  repong::autobackport_mediawiki_timer { 'REL1_40':
    branch => 'REL1_40',
    when   => 'Tue 05:30:00 UTC',
  }

  # supported till December 2024
  repong::autobackport_mediawiki_timer { 'REL1_41':
    branch => 'REL1_41',
    when   => 'Wed 05:30:00 UTC',
  }

  # supported till June 2025
  repong::autobackport_mediawiki_timer { 'REL1_42':
    branch => 'REL1_42',
    when   => 'Thu 05:30:00 UTC',
  }
}
