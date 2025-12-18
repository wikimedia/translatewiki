# = Class: wiki::slack
#
# Configures various Slack related requirements.
#
class wiki::slack (
  String $config_dir,
  String $maintenance_user,
  String $slack_webhook_url = lookup('slack_webhook_url')
) {

  file { '/etc/systemd/system/slack-log-relay.service':
    ensure => absent,
  }

  service { 'slack-log-relay':
    ensure => stopped,
    enable => false,
  }

  file { '/etc/systemd/system/slack-relay.service':
    content => template('wiki/slack-relay.service.erb'),
    notify  => [
      Service['slack-relay']
    ]
  }

  service { 'slack-relay':
    ensure  => running,
    enable  => true,
    require => [
      File['/etc/systemd/system/slack-relay.service'],
    ]
  }

  file { '/etc/systemd/system/phplog2slack.service':
    content => template('wiki/phplog2slack.service.erb'),
    notify  => [
      Service['phplog2slack']
    ]
  }

  service { 'phplog2slack':
    ensure  => running,
    enable  => true,
    require => [
      File['/etc/systemd/system/phplog2slack.service'],
    ]
  }
}
