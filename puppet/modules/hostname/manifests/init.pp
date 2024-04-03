# = Class: hostname
#
# Provides hostname
#
class hostname {
  $packages = [
    'libnss-myhostname',
  ]
  stdlib::ensure_packages($packages, {
    ensure => 'present',
  })

  $hostname = $::ipaddress ? {
    '152.89.106.205' => 'web2.translatewiki.net',
    default          => 'unknown.tranlatewiki.net',
  }

  file { '/etc/hostname':
    ensure  => file,
    content => $hostname,
  }
}
