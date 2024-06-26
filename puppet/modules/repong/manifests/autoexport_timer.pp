# = Type: repong::autoexport_timer
define repong::autoexport_timer (
  String $when,
  String $command      = 'autoexport',
  String $l10nbot_user = lookup('repong::l10nbot_user'),
  String $bin_dir      = lookup('repong::bin_dir'),
  Boolean $active      = true,
) {
  $ensure = $active ? {
    true    => 'present',
    default => 'absent'
  };
  systemd::timer { "${name}.timer":
    ensure          => $ensure,
    timer_content   => template('repong/autoexport.timer.erb'),
    service_content => template('repong/autoexport.service.erb'),
    active          => $active,
    enable          => $active,
  }
}
