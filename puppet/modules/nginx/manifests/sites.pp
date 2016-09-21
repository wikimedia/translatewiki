# = Class: nginx::sites
#
# Provides configuration information for nginx sites.
#
class nginx::sites {
  include nginx::ssl

  file { '/etc/nginx/sites/translatewiki.net':
    source  => 'puppet:///modules/nginx/translatewiki.net',
  }

  file { '/etc/nginx/sites/translatewiki.org':
    source  => 'puppet:///modules/nginx/translatewiki.org',
  }

  file { '/etc/nginx/sites/dev.translatewiki.net':
    source  => 'puppet:///modules/nginx/dev.translatewiki.net',
  }
}
