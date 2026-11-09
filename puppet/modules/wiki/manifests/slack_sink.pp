# = Class: wiki::slack_sink
#
# Discards Slack notifications on development hosts without a Slack relay.
# systemd-tmpfiles recreates the sink after /run is cleared at boot.
#
class wiki::slack_sink {
  file { '/etc/tmpfiles.d/slack-relay.conf':
    ensure  => file,
    mode    => '0644',
    content => "d /run/slack-relay 0755 root root -\nL /run/slack-relay/pipe - - - - /dev/null\n",
    notify  => Exec['create-slack-relay-sink'],
  }

  exec { 'create-slack-relay-sink':
    command     => '/usr/bin/systemd-tmpfiles --create /etc/tmpfiles.d/slack-relay.conf',
    refreshonly => true,
  }
}
