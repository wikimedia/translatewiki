# = Class: wiki
#
# Enables daily stat scripts
class wiki::stats (
  String $config_dir,
  String $maintenance_user,
) {
  file { '/etc/cron.d/wikistats':
    content => template('wiki/wikistats.erb'),
  }
}
