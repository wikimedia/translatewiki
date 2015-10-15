# = Class: kitanonl
#
# Provides kitano.nl website
#
class kitanonl {
  include fcgiwrap

  file { '/etc/nginx/sites/kitano.nl':
    source  => 'puppet:///modules/kitanonl/kitano.nl',
    require => [Package['nginx'], Service['fcgiwrap']],
    notify  => Service['nginx'],
  }
}
