# = Class: wiki::irc
#
# Configures various IRC related wiki stuff
#
class wiki::irc {
  $packages = [
    # irc bots
    'libpoe-component-irc-perl',
  ]
  ensure_packages($packages, {
    ensure => 'present',
  })

  file { '/etc/systemd/system/irc-log-relay.service':
    source => 'puppet:///modules/wiki/irc-log-relay.service',
  }

  service { 'irc-log-relay':
    ensure  => running,
    enable  => true,
    require => File['/etc/systemd/system/irc-log-relay.service'],
  }

  file { '/etc/systemd/system/irc-rc-relay.service':
    source => 'puppet:///modules/wiki/irc-rc-relay.service',
  }

  service { 'irc-rc-relay':
    ensure  => running,
    enable  => true,
    require => File['/etc/systemd/system/irc-rc-relay.service'],
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
