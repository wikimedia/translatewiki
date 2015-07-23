# = Class: kitano-nl
#
# Provides kitano.nl website
#
class kitano-nl {
  include fcgiwrap

  file { '/etc/nginx/sites/kitano.nl':
    source  => 'puppet:///modules/kitano-nl/kitano.nl',
    require => [Package['nginx'], Service['fcgiwrap']],
    notify  => Service['nginx'],
  }
}
