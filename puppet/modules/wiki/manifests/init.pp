# = Class: wiki
#
# Configures various wiki stuff. Now mostly crontabs.
#
# == Parameters:
#
# $config:: Where the wiki config is stored.
#
# $user:: What user owns the wiki stuff.
#
#
# == Sample Usage:
#
#   class { 'wiki':
#     config => '/home/betawiki/config',
#     user   => 'betawiki',
#   }
#
class wiki ($config, $user) {
  file { '/etc/cron.d/wikimaintenance':
    content => template('wiki/wikimaintenance.erb'),
  }

  file { '/etc/cron.d/wikiservices':
    content => template('wiki/wikiservices.erb'),
  }

  file { '/etc/cron.d/wikistats':
    content => template('wiki/wikistats.erb'),
  }

  # This one is needed for the irc bots
  package { 'libpoe-component-irc-perl':
   ensure => present,
  }
}
