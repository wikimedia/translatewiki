# = Class: wiki::irc
#
# Configures various IRC related wiki stuff
#
class wiki::irc (
  String $config_dir,
  String $maintenance_user,
) {
  $packages = [
    # irc bots
    'libpoe-component-irc-perl',
  ]
  stdlib::ensure_packages($packages, {
    ensure => 'present',
  })

  file { '/etc/irc-relay':
    ensure => 'directory',
  }

  file { '/etc/irc-relay/irc-log-relay.env':
    content => template('wiki/irc-log-relay.env.erb'),
  }

  file { '/etc/systemd/system/irc-log-relay.service':
    content => template('wiki/irc-log-relay.service.erb'),
  }

  service { 'irc-log-relay':
    ensure  => running,
    enable  => true,
    require => [
      File['/etc/systemd/system/irc-log-relay.service'],
      File['/etc/irc-relay/irc-log-relay.env']
    ],
  }

  file { '/etc/irc-relay/irc-rc-relay.env':
    content => template('wiki/irc-rc-relay.env.erb'),
  }

  file { '/etc/systemd/system/irc-rc-relay.service':
    content => template('wiki/irc-rc-relay.service.erb'),
  }

  service { 'irc-rc-relay':
    ensure  => running,
    enable  => true,
    require => [
      File['/etc/systemd/system/irc-rc-relay.service'],
      File['/etc/irc-relay/irc-rc-relay.env']
    ],
  }

  file { '/etc/systemd/system/phplog2irc.service':
    source => 'puppet:///modules/wiki/phplog2irc.service',
  }

  service { 'phplog2irc':
    ensure  => running,
    enable  => true,
    require => File['/etc/systemd/system/phplog2irc.service'],
  }
}
