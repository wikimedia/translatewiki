# = Class: repong::autoexport
#
# Sets up automated exports using systemd timers
#
class repong::autoexport (
  String $l10nbot_user = lookup('repong::l10nbot_user'),
) {
  Repong::Autoexport_timer {
    require => User[ $l10nbot_user ],
  }

  repong::autoexport_timer { 'autoexport':
    when => 'Mon,Thu 12:00:00 UTC',
  }

  repong::autoexport_timer { 'autoexport-mediawiki':
    command => 'autoexport-mediawiki',
    when    => 'Mon-Fri 7:30:00 UTC',
  }
}
