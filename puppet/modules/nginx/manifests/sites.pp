# = Class: nginx::sites
#
# Provides configuration information for nginx sites.
#
class nginx::sites {
  include nginx::ssl

  $directories = [
    '/www',
    '/www/translatewiki.net',
    '/www/translatewiki.net/error',
  ]
  ensure_resource('file', $directories, {
    'ensure' => 'directory',
    'owner'  => 'betawiki',
    'group'  => 'users'
  })

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
