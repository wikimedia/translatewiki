class mailman-conf {
  # Would prefer to just use "list" but the module does not support this, so make an alias
  user {
  'mailman':
    ensure     => present,
    uid        => 38,
    allowdupe  => true,
    gid        => 38,
    shell      => '/bin/sh',
    password   => '',
    home       => '/var/list',
    comment    => 'Mailing List Manager'
  }

  # Would prefer to just use "list" but the module does not support this, so make an alias
  group { "mailman":
    ensure     => present,
    gid        => 38,
    allowdupe  => true,
  }

  file { '/etc/nginx/sites-available/lists.translatewiki.net':
    source  => 'puppet:///modules/mailman-conf/nginx/lists.translatewiki.net',
  }

  file { '/etc/nginx/sites-enabled/lists.translatewiki.net':
    ensure => 'link',
    target => '../sites-available/lists.translatewiki.net',
    notify  => Service['nginx'],
  }

  # Actual installation using submodule
  class { 'mailman':
    default_url_host    => 'lists.translatewiki.net',
    default_email_host  => 'lists.translatewiki.net',
    default_url_pattern => 'https://%s/',
    mailman_site_list   => 'mailman',
    mm_cfg_settings     => {
    'ALLOW_SITE_ADMIN_COOKIES' => 'Yes',
    'PUBLIC_ARCHIVE_URL' => "'https://%(hostname)s/pipermail/%(listname)s'",
    'MTA' => "'None'",
    'POSTFIX_STYLE_VIRTUAL_DOMAINS' => "'False'",
    'DEFAULT_SUBJECT_PREFIX' => "''",
    'DEFAULT_REPLY_GOES_TO_LIST' => '1',
    'SMTPHOST' => "'translatewiki.net'",
    },
  }
}
