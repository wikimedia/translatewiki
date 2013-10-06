class base {
  package { [
    # Basic packages
    'ack-grep',
    'bash-completion',
    'doxygen',
    'duplicity',
    'fontconfig',
    'htop',
    'iotop', # IO view
    'iperf', # Network performance
    'jnettop', # Network view
    'make',
    'mytop', # MySQL view
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
