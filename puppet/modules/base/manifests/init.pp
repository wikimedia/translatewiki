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
    'mosh', # ssh for bad connections
    'mytop', # MySQL view
    'nano',
    'netcat-openbsd',
    'screen',
    'unzip',
    'wget',

    # Tools related to supporting all our products
    'bzr',
    'cvs',
    'gettext',
    'git-core',
    'mercurial',
    'subversion',

    # Development extras
    'jpegoptim',
    'optipng',
    'python-pip',

    # Other
    'mysqltuner',

    # Dependencies for compiling wikidiff2
    'g++',
    'pkg-config',
    'libthai-dev',
    ]: ensure => present,
  }

  # Download git-review from pip, the version in current LTS has annoying bugs
  package { 'git-review':
    ensure   => present,
    provider => pip,
  }

  file { '/etc/environment':
    content => 'PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/home/betawiki/config/bin"',
  }
}
