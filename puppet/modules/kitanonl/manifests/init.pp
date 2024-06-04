# = Class: kitanonl
#
# Provides kitano.nl website
#
class kitanonl {
  $directories = [
    '/www',
    '/www/kitano.nl',
  ]
  ensure_resource('file', $directories, {
    'ensure' => 'directory',
    'owner'  => 'betawiki',
    'group'  => 'users'
  })

  file { '/www/kitano.nl/index.html':
    source  => 'puppet:///modules/kitanonl/index.html',
    require => File['/www/kitano.nl'],
  }

  file { '/etc/nginx/sites/kitano.nl':
    source  => 'puppet:///modules/kitanonl/kitano.nl',
    require => [
      Package['nginx'],
      Service['fcgiwrap']
    ],
    notify  => Service['nginx'],
  }
}
