class base::es {
  file { '/etc/network/interfaces':
    source => 'puppet:///modules/base/interfaces-es'
  }
}
