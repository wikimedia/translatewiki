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
    domains              => [
      'translatewiki.net',
      'translatewiki.org',
      'lists.translatewiki.net',
      'stats.translatewiki.net',
      'dev.translatewiki.net',
    ],
    webroot_paths        => [
      '/www/translatewiki.net/docroot',
      '/www/translatewiki.net/docroot',
      '/usr/lib/cgi-bin/mailman',
      '/www/stats.translatewiki.net',
      '/www/dev.translatewiki.net/docroot',
    ],
  }
}
