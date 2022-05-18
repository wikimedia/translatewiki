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
  repong::autobackport_mediawiki_timer { 'REL1_35':
    branch => 'REL1_35',
    when   => 'Mon 06:30:00',
  }

  repong::autobackport_mediawiki_timer { 'REL1_36':
    active => false,
    branch => 'REL1_36',
    when   => 'Tue 06:30:00',
  }

  repong::autobackport_mediawiki_timer { 'REL1_37':
    branch => 'REL1_37',
    when   => 'Wed 06:30:00',
  }

  repong::autobackport_mediawiki_timer { 'REL1_38':
    branch => 'REL1_38',
    when   => 'Thu 06:30:00',
  }
}
