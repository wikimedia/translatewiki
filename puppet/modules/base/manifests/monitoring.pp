# = Class: base::monitoring
#
# Provides server monitoring and alerting.
#
class base::monitoring (
  String $bin_dir,
) {
  systemd::timer { 'failed-services-notify.timer':
    timer_content   => template('base/failed-services-notify.timer.erb'),
    service_content => template('base/failed-services-notify.service.erb'),
    active          => true,
    enable          => true,
  }
}
