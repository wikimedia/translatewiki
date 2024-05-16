# = Class: wiki
#
# Enables daily stat scripts
class wiki::stats (
  String $config_dir,
  String $l10nbot_user,
) {
  systemd::timer { 'mw-newcorelangs.timer':
    timer_content   => template('wiki/mw-newcorelangs.timer.erb'),
    service_content => template('wiki/mw-newcorelangs.service.erb'),
    active          => true,
    enable          => true,
  }
}
