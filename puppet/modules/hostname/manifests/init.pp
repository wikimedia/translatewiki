# = Class: hostname
#
# Provides hostname
#
class hostname (
  Stdlib::Fqdn $fqdn,
) {
  $packages = [
    'libnss-myhostname',
  ]
  stdlib::ensure_packages($packages, {
    ensure => 'present',
  })

  file { '/etc/hostname':
    ensure  => file,
    content => $fqdn,
  }
}
