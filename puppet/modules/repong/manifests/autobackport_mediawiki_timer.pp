# = Type: repong::autobackport_mediawiki_timer
define repong::autobackport_mediawiki_timer (
  String $branch,
  String $when,
  Boolean $active = true,
) {
  $ensure = $active ? {
    true    => 'present',
    default => 'absent'
  };
  systemd::timer { "autobackport-mediawiki-${branch}.timer":
    ensure        => $ensure,
    timer_content => template('repong/autobackport-mediawiki.timer.erb'),
    active        => $active,
    enable        => $active,
  }
}
