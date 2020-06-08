# = Class: wiki::docroot
#
# Creates directories for wiki deployment
#
class wiki::docroot (
  String $maintenance_user,
  String $domain,
  String $production_dir,
) {
  $directories = [
    '/www',
    "/www/${domain}",
    "/www/${domain}/docroot",
  ]
  ensure_resource('file', $directories, {
    ensure => 'directory',
  })

  file { "/www/${domain}/docroot/w":
    ensure  => 'symlink',
    target  => $production_dir,
    require => File[ "/www/${domain}/docroot" ],
  }

  $shared_directories = [
    "/www/${domain}/logs",
    "/www/${domain}/docroot/images",
    "/www/${domain}/docroot/sitemap",
  ]
  file { $shared_directories:
    ensure  => 'directory',
    group   => 'www-shared',
    mode    => '2664',
    require => Group[ 'www-shared' ],
  }

  # It seems that php ignores umask for error_log file. Hence default setfacl permissions
  # are not sufficient here. Instead create the files pre-emptively, which has the added
  # benefit that also log rotation can use the same list.
  $log_files = [
    "/www/${domain}/logs/access_cli",
    "/www/${domain}/logs/auth",
    "/www/${domain}/logs/debug",
    "/www/${domain}/logs/error_js",
    "/www/${domain}/logs/error_php",
  ]

  file { $log_files:
    ensure  => 'file',
    owner   => $maintenance_user,
    group   => 'www-shared',
    mode    => 'ug=rw,o=r',
    require => [
      Group[ 'www-shared' ],
      User[ $maintenance_user ],
      File[ "/www/${domain}/logs/" ],
    ],
  }

  file { '/etc/logrotate.d/mediawiki':
    content => template('wiki/mw-logrotate.erb'),
  }
}
