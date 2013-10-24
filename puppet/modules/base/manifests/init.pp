class base {
  package { [
    # Basic packages
    'ack-grep',
    'bash-completion',
    'doxygen',
    'duplicity',
    'fontconfig',
    'htop',
    'make',
    'nano',
    'netcat-openbsd',
    'screen',
    'wget',

    # Tools related to supporting all our products
    'bzr',
    'cvs',
    'gettext',
    'git-core',
    'mercurial',
    'subversion',

    # Development extras
    'git-review',

    ]: ensure => present,
  }
}
