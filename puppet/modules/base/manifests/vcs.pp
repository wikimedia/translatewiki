class base::vcs {
  package { [
    'bzr',
    'cvs',
    'gettext',
    'mercurial',
    'subversion',
    ]: ensure => present,
  }
}
