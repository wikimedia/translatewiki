# = Class: nginx::sites
#
# Provides configuration information for nginx sites.
#
class nginx::sites {
  include nginx::ssl

  File {
    require => File['/etc/nginx/nginx.conf']
  }

  $directories = [
    '/www',
    '/www/stats.translatewiki.net',
    '/www/translatewiki.net',
    '/www/translatewiki.net/docroot',
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

  file { '/etc/nginx/sites/translatewiki.org':
    source => 'puppet:///modules/nginx/translatewiki.org',
  }
}
