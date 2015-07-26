class nginx::sites {
  include nginx::ssl

  file { '/etc/nginx/sites/translatewiki.net':
    source  => 'puppet:///modules/nginx/translatewiki.net',
  }

  file { '/etc/nginx/sites/translatewiki.org':
    source  => 'puppet:///modules/nginx/translatewiki.org',
  }
}
