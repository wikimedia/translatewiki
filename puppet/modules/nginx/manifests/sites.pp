class nginx::sites {
  include nginx::ssl

  file { '/etc/nginx/sites/translatewiki.net':
    source  => 'puppet:///modules/nginx/translatewiki.net',
  }
}
