class eximconf {
  file { '/etc/mailname':
    content  => 'translatewiki.net'
  }
}
