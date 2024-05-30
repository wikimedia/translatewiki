# = Class: nginx
#
# Provides installation and configuration information for the nginx package.
#
class nginx {
  $packages = [
    'nginx',
  ]
  stdlib::ensure_packages($packages, {
    ensure => 'present',
  })

  service { 'nginx':
    ensure  => running,
    enable  => true,
    require => Package['nginx'],
  }

  File {
    require => Package['nginx'],
    notify  => Service['nginx'],
  }

  file { '/etc/nginx/nginx.conf':
    source => 'puppet:///modules/nginx/nginx.conf',
  }

  file { '/etc/logrotate.d/nginx':
    source => 'puppet:///modules/nginx/logrotate'
  }

  file { '/www/error/':
    ensure => 'directory';
  }

  file { '/www/error/error.html':
    source => 'puppet:///modules/nginx/error.html';
  }

  # set up the default parameters for all firewall rules
  Firewall {
    before  => Class['base::firewall::post'],
    require => Class['base::firewall::pre'],
  }

  firewall { '020 Allow inbound HTTP (v4)':
    dport    => 80,
    proto    => tcp,
    jump     => accept,
    protocol => 'IPv4',
  }

  firewall { '020 Allow inbound HTTP (v6)':
    dport    => 80,
    proto    => tcp,
    jump     => accept,
    protocol => 'IPv6',
  }

  firewall { '021 Allow inbound HTTPS (v4)':
    dport    => 443,
    proto    => tcp,
    jump     => accept,
    protocol => 'IPv4',
  }

  firewall { '021 Allow inbound HTTPS (v6)':
    dport    => 443,
    proto    => tcp,
    jump     => accept,
    protocol => 'IPv6',
  }
}
