# = Class: mailmanconf
#
# Provides mailman configuration.
#
class mailmanconf {
  # Would prefer to just use "list" but the module does not support this, so
  # make an alias
  user {
  'mailman':
    ensure     => present,
    uid        => 38,
    allowdupe  => true,
    gid        => 38,
    shell      => '/bin/sh',
    password   => '',
    home       => '/var/list',
    managehome => true,
    comment    => 'Mailing List Manager'
  }

  # Would prefer to just use "list" but the module does not support this, so
  # make an alias
  group { 'mailman':
    ensure    => present,
    gid       => 38,
    allowdupe => true,
  }

  file { '/etc/nginx/sites/lists.translatewiki.net':
    source  => 'puppet:///modules/mailmanconf/nginx/lists.translatewiki.net',
  }

  file { '/etc/exim4/conf.d/main/04_mailman_options':
    source => 'puppet:///modules/mailmanconf/exim4/conf.d/main/04_mailman_options',
    notify => Service['exim4'],
  }

  file { '/etc/exim4/conf.d/router/450_mailman_aliases':
    source => 'puppet:///modules/mailmanconf/exim4/conf.d/router/450_mailman_aliases',
    notify => Service['exim4'],
  }

  file { '/etc/exim4/conf.d/transport/40_mailman_pipe':
    source => 'puppet:///modules/mailmanconf/exim4/conf.d/transport/40_mailman_pipe',
    notify => Service['exim4'],
  }

# Actual installation using submodule
  class { 'mailman':
    default_url_host    => 'lists.translatewiki.net',
    default_email_host  => 'lists.translatewiki.net',
    default_url_pattern => 'https://%s/',
    mailman_site_list   => 'mailman',
    mm_cfg_settings     => {
    'ALLOW_SITE_ADMIN_COOKIES'      => 'Yes',
    'PUBLIC_ARCHIVE_URL'            => "'https://%(hostname)s/pipermail/%(listname)s'",
    'MTA'                           => "'None'",
    'POSTFIX_STYLE_VIRTUAL_DOMAINS' => "'False'",
    'DEFAULT_SUBJECT_PREFIX'        => "''",
    'DEFAULT_REPLY_GOES_TO_LIST'    => '1',
    'SMTPHOST'                      => "'translatewiki.net'",
    }
  }
}
