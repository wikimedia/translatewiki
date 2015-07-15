class nginx::ssl {
  include nginx

  file { '/etc/ssl/private/rapidssl.pem':
    source  => 'puppet:///modules/nginx/rapidssl.pem',
  }

  exec { 'dhparam':
    command => '/usr/bin/openssl dhparam -out /etc/ssl/certs/dhparam.pem 2048',
    creates => '/etc/ssl/certs/dhparam.pem'
  }
}
