# = Class: nginx::ssl
#
# Provides SSL configuration for nginx sites.
#
class nginx::ssl {
  include nginx

  file { '/etc/ssl/certs/dhparam.pem':
    source => 'puppet:///modules/nginx/ffdhe2048.txt',
    notify => Service[ 'nginx' ],
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

  class { '::letsencrypt':
    email => 'root@translatewiki.net',
  }

  letsencrypt::certonly { 'translatewiki.net':
    plugin               => 'webroot',
    manage_cron          => true,
    cron_success_command => 'systemctl reload nginx.service',
    cron_output          => 'suppress',
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
