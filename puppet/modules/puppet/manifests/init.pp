class puppet {
  package { 'puppet':
    ensure => present,
  }

  # We are applying manually via CLI
  service { 'puppet':
    enable => false,
    ensure => stopped,
  }
}
