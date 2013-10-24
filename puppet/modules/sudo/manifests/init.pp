class sudo {
  package { 'sudo':
    ensure => present,
  }

  file { '/etc/sudoers.d/local':
    source => 'puppet:///modules/sudo/local',
    mode   => '0440',
  }
}
