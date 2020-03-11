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

  # FIXME: use facl to ensure log files are writable by www-shared
  file { "/www/${domain}/logs":
    ensure => 'directory',
    group  => 'www-shared',
    mode   => '2664',
  }

  file { "/www/${domain}/docroot/w":
    ensure => 'symlink',
    target => $production_dir,
  }

  file { "/www/${domain}/docroot/images":
    ensure => 'directory',
    group  => 'www-shared',
    mode   => '0664',
  }

  file { "/www/${domain}/docroot/sitemap":
    ensure => 'directory',
    owner  => $maintenance_user,
    mode   => '0664',
  }
}
