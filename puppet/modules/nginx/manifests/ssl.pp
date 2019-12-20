# = Class: nginx::ssl
#
# Provides SSL configuration for nginx sites.
#
class nginx::ssl {
  include nginx

  exec { 'dhparam':
    command => '/usr/bin/openssl dhparam -out /etc/ssl/certs/dhparam.pem 2048',
    creates => '/etc/ssl/certs/dhparam.pem'
  }

  file { '/etc/nginx/includes':
    ensure => 'directory',
  }

  file { '/etc/nginx/includes/ssl-certbot.conf':
    source  => 'puppet:///modules/nginx/ssl-certbot.conf',
  }

  class { '::letsencrypt':
    email => 'root@translatewiki.net',
  }

  letsencrypt::certonly { 'translatewiki.net':
    plugin               => 'webroot',
    manage_cron          => true,
    cron_success_command => 'systemctl reload nginx.service',
    suppress_cron_output => true,
    domains              => [
      'translatewiki.net',
      'translatewiki.org',
      'stats.translatewiki.net',
      'kitano.nl',
    ],
    webroot_paths        => [
      '/www/translatewiki.net/docroot',
      '/www/translatewiki.net/docroot',
      '/www/stats.translatewiki.net',
      '/www/kitano.nl',
    ],
  }
}
