# = Type: repong::autoimport_timer
define repong::autoimport_timer (
  String $when,
  String $command      = 'autoimport',
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
    timer_content   => template('repong/autoimport.timer.erb'),
    service_content => template('repong/autoimport.service.erb'),
    active          => $active,
    enable          => $active,
  }
}
