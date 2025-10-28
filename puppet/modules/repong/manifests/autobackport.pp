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

  # LTS; supported till December 2027
  repong::autobackport_mediawiki_timer { 'REL1_43':
    branch => 'REL1_43',
    when   => 'Tue 05:30:00 UTC',
  }

  # supported till June 2026
  repong::autobackport_mediawiki_timer { 'REL1_44':
    branch => 'REL1_44',
    when   => 'Wed 05:30:00 UTC',
  }

  # supported till December 2026
  repong::autobackport_mediawiki_timer { 'REL1_45':
    branch => 'REL1_45',
    when   => 'Thu 05:30:00 UTC',
  }
}
