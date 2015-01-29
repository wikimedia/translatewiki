class sshd {
  file { '/etc/ssh/sshd_config':
    source => 'puppet:///modules/sshd/sshd_config',
  }
}
