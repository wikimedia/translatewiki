lookup('classes', {merge => unique}).include

node 'web2.translatewiki.net' {
  mount { '/scratch':
    ensure  => 'mounted',
    device  => '46.38.248.210:/voln264518a1',
    fstype  => 'nfs',
    options => 'rw',
    require => File['/scratch'],
  }
}
