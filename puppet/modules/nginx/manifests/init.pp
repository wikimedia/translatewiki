# = Class: nginx
#
# Provides installation and configuration information for the nginx package.
#
class nginx {
  $packages = [
    'nginx-full',
  ]
  ensure_packages($packages, {
    ensure => 'present',
  })

  service { 'nginx':
    ensure  => running,
    enable  => true,
    require => Package['nginx-full'],
  }

  File {
    require => Package['nginx-full'],
    notify  => Service['nginx'],
  }

  file { '/etc/nginx/nginx.conf':
    source => 'puppet:///modules/nginx/nginx.conf',
  }

  file { '/etc/nginx/mime.types':
    source => 'puppet:///modules/nginx/mime.types',
  }

  file { '/etc/logrotate.d/nginx':
    source => 'puppet:///modules/nginx/logrotate'
  }

  # set up the default parameters for all firewall rules
  Firewall {
    before  => Class['base::firewall::post'],
    require => Class['base::firewall::pre'],
  }

  firewall { '020 Allow inbound HTTP (v4)':
    dport    => 80,
    proto    => tcp,
    action   => accept,
    provider => 'iptables',
  }

  firewall { '020 Allow inbound HTTP (v6)':
    dport    => 80,
    proto    => tcp,
    action   => accept,
    provider => 'ip6tables',
  }

  firewall { '021 Allow inbound HTTPS (v4)':
    dport    => 443,
    proto    => tcp,
    action   => accept,
    provider => 'iptables',
  }

  firewall { '021 Allow inbound HTTPS (v6)':
    dport    => 443,
    proto    => tcp,
    action   => accept,
    provider => 'ip6tables',
  }
}
