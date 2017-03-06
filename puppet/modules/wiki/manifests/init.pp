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

  file { '/etc/cron.d/wikistats':
    content => template('wiki/wikistats.erb'),
  }

  file { '/etc/systemd/system/mw-jobrunner.service':
    source  => 'puppet:///modules/wiki/mw-jobrunner.service',
  }

  service { 'mw-jobrunner':
    ensure  => running,
    enable  => true,
    require => File['/etc/systemd/system/mw-jobrunner.service'],
  }


  file { '/etc/systemd/system/irc-log-relay.service':
    source  => 'puppet:///modules/wiki/irc-log-relay.service',
  }

  service { 'irc-log-relay':
    ensure  => running,
    enable  => true,
    require => File['/etc/systemd/system/irc-log-relay.service'],
  }


  file { '/etc/systemd/system/irc-rc-relay.service':
    source  => 'puppet:///modules/wiki/irc-rc-relay.service',
  }

  service { 'irc-rc-relay':
    ensure  => running,
    enable  => true,
    require => File['/etc/systemd/system/irc-rc-relay.service'],
  }


  file { '/etc/systemd/system/phplog2irc.service':
    source  => 'puppet:///modules/wiki/phplog2irc.service',
  }

  service { 'phplog2irc':
    ensure  => running,
    enable  => true,
    require => File['/etc/systemd/system/phplog2irc.service'],
  }

  package { [
    # irc bots
    'libpoe-component-irc-perl',
    # needed for svg images
    'librsvg2-bin'
  ]:
    ensure => present,
  }
}
