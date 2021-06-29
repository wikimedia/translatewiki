# = Class: repong::autoexport
#
# Sets up automated exports using systemd timers
#
class repong::autoexport (
  String $l10nbot_user,
  String $bin_dir
) {
  systemd::timer { 'autoexport.timer':
    timer_content   => template('repong/autoexport.timer.erb'),
    service_content => template('repong/autoexport.service.erb'),
    active          => true,
    enable          => true,
    require         => User[ $l10nbot_user ],
  }
}
