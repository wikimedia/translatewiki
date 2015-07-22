class exim-conf {
  file { '/etc/mailname':
    content  => 'translatewiki.net'
  }
}
