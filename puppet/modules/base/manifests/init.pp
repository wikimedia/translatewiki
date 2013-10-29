class base {
  package { [
    # Basic packages
    'ack-grep',
    'bash-completion',
    'doxygen',
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

    # Other
    'mysqltuner',
    ]: ensure => present,
  }

  file { '/etc/environment':
    content => 'PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/home/betawiki/config/bin"',
  }
}
