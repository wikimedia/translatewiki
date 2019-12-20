# = Class: nginx
#
# Provides installation and configuration information for the nginx package.
#
# Manual actions:
# apt install curl gnupg2 lsb-release
# echo "deb http://nginx.org/packages/mainline/debian `lsb_release -cs` nginx" > /etc/apt/sources.list.d/nginx.list
# curl -fsSL https://nginx.org/keys/nginx_signing.key | sudo apt-key add -
class nginx {
  $packages = [
    'nginx-light',
  ]
  ensure_packages($packages, {
    ensure => 'present',
  })

  service { 'nginx':
    ensure  => running,
    enable  => true,
    require => Package['nginx-light'],
  }

  File {
    require => Package['nginx-light'],
    notify  => Service['nginx'],
  }

  file { '/etc/nginx/nginx.conf':
    source => 'puppet:///modules/nginx/nginx.conf',
  }

  file { '/etc/nginx/mime.types':
    source => 'puppet:///modules/nginx/mime.types',
  }

  file { '/etc/nginx/sites':
    ensure => 'directory',
  }

  file { '/etc/logrotate.d/nginx':
    source => 'puppet:///modules/nginx/logrotate'
  }
}
