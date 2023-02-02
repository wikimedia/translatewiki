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
    content => template('wiki/slack-log-relay.service.erb'),
    notify  => [
      Service['slack-log-relay']
    ]
  }

  service { 'slack-log-relay':
    ensure  => running,
    enable  => true,
    require => [
      File['/etc/systemd/system/slack-log-relay.service'],
    ]
  }
}
