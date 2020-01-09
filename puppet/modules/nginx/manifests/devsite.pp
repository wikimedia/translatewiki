# = Class: nginx::ssl
#
# Provides SSL configuration for nginx sites.
#
class nginx::devsite (
  String $domain = 'dev.translatewiki.net',
) {
  $certdir = '/etc/ssl/localcerts';

  file { $certdir:
    ensure  => 'directory',
  }

  exec { 'dhparam':
    command => '/usr/bin/openssl dhparam -out /etc/ssl/certs/dhparam.pem 2048',
    creates => '/etc/ssl/certs/dhparam.pem',
    notify  => Service[ 'nginx' ],
  }

  $dirs = [
    '/etc/nginx',
    '/etc/nginx/sites',
    '/etc/nginx/includes',
  ]
  ensure_resource('file', $dirs, {
    ensure => 'directory',
  })

  file { '/etc/nginx/includes/ssl.conf':
    source => 'puppet:///modules/nginx/ssl.conf',
    notify => Service[ 'nginx' ],
  }

  file { '/etc/nginx/includes/ssl-selfsigned.conf':
    content => template('nginx/ssl-selfsigned.conf.erb'),
    notify  => Service[ 'nginx' ],
  }

  file { "/etc/nginx/sites/${domain}":
    content => template('nginx/devsite.conf.erb'),
    notify  => Service[ 'nginx' ],
  }

  file { "/www/${domain}/error":
    ensure => 'directory',
  }

  file { "/www/${domain}/error/error.html":
    source => 'puppet:///modules/nginx/error.html',
  }

  $command = @("COMMAND"/L)
    openssl req -newkey rsa:2048 -nodes  -x509 -days 365 \
    -keyout ${domain}.key -out ${domain}.crt -subj '/CN=${domain}'
    |-COMMAND

  exec { 'Create self-signed cert':
    command => $command,
    cwd     => $certdir,
    creates => [ "${certdir}/${domain}.key", "${certdir}/${domain}.crt", ],
    path    => $::path,
    notify  => Service[ 'nginx' ],
  }
}
