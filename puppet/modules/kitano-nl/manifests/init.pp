# = Class: kitano-nl
#
# Provides kitano.nl website
#
class kitano-nl {
  include fcgiwrap

  file { '/etc/nginx/sites-available/kitano.nl':
    source  => 'puppet:///modules/kitano-nl/kitano.nl',
    require => [Package['nginx'], Service['fcgiwrap']],
    notify  => Service['nginx'],
  }

  file { '/etc/nginx/sites-enabled/kitano.nl':
    ensure => 'link',
    target => '../sites-available/kitano.nl',
  }
}
