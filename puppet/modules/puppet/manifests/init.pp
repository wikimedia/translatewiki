class puppet {
  package { 'puppet':
    ensure => present,
  }

  # We are applying manually via CLI
  service { 'puppet':
    ensure => stopped,
    enable => false,
  }
}
