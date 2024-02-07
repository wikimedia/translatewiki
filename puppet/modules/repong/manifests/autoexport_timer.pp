# = Type: repong::autobackport_mediawiki_timer
define repong::autoexport_timer (
  String $when,
  String $command      = 'autoexport',
  String $l10nbot_user = $repong::autoexport::l10nbot_user,
  String $bin_dir      = $repong::autoexport::bin_dir,
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
