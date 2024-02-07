# = Class: repong::autoexport
#
# Sets up automated exports using systemd timers
#
class repong::autoexport (
  String $l10nbot_user,
  String $bin_dir
) {

  Repong::Autoexport_timer {
    require => User[ $l10nbot_user ],
  }

  repong::autoexport_timer { 'autoexport':
    when => 'Mon,Thu 12:00:00 UTC',
  }

  repong::autoexport_timer { 'autoexport-mediawiki':
    command => 'autoexport-mediawiki',
    when    => 'Mon-Fri 7:00:00 UTC',
  }
}
