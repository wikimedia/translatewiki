class base::web1 {
  file { '/etc/hostname':
    content => 'translatewiki.net',
  }

  file { '/etc/network/interfaces':
    source => 'puppet:///modules/base/interfaces-web1'
  }
}
