# = Class: hostname
#
# Provides hostname
#
class hostname {
  package { 'libnss-myhostname':
    ensure => present,
  }

  $hostname = $::ipaddress ? {
    '37.120.188.137' => 'web1.translatewiki.net',
    '37.120.188.139' => 'es.translatewiki.net',
    default          => 'unknown.tranlatewiki.net',
  }

  file { '/etc/hostname':
    ensure  => file,
    content => $hostname,
  }
}
