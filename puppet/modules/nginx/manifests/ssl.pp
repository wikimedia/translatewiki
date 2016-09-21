# = Class: nginx::ssl
#
# Provides SSL configuration for nginx sites.
#
class nginx::ssl {
  include nginx

  file { '/etc/ssl/private/rapidssl.pem':
    source  => 'puppet:///modules/nginx/rapidssl.pem',
  }

  exec { 'dhparam':
    command => '/usr/bin/openssl dhparam -out /etc/ssl/certs/dhparam.pem 2048',
    creates => '/etc/ssl/certs/dhparam.pem'
  }

  file { '/etc/nginx/includes':
    ensure => 'directory',
  }

  file { '/etc/nginx/includes/ssl.conf':
    source  => 'puppet:///modules/nginx/ssl.conf',
  }

  file { '/etc/nginx/includes/ssl-certbot.conf':
    source  => 'puppet:///modules/nginx/ssl-certbot.conf',
  }
}
