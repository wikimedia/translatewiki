# = Class: nginx::sites
#
# Provides configuration information for nginx sites.
#
class nginx::sites {
  include nginx::ssl

  file { '/www':
    ensure => direcory,
    owner  => 'betawiki',
    group  => 'users',
  }

  file { '/etc/nginx/sites/translatewiki.net':
    source => 'puppet:///modules/nginx/translatewiki.net',
  }

  file { '/www/translatewiki.net/error/error.html':
    source => 'puppet:///modules/nginx/error.html';
  }

  file { '/etc/nginx/sites/translatewiki.org':
    source => 'puppet:///modules/nginx/translatewiki.org',
  }
}
