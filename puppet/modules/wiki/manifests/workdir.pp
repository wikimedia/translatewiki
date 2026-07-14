# = Class: wiki::workdir
#
# Sets up working directory for MediaWiki
#
class wiki::workdir (
  String $config_dir,
  String $domain,
  String $deployment_owner,
  String $deployment_group,
  String $deployment_dir,
  String $mediawiki_config,
  Hash[String, Data] $repos = {},
) {
  $workdir = "${deployment_dir}/workdir";

  Exec {
    require => User[ $deployment_owner ],
  }

  Vcsrepo {
    require => [ User[ $deployment_owner ], Group[ $deployment_group ] ],
  }

  # Debian 13 creates homes with mode 0700 (login.defs HOME_MODE); www-data
  # (jobrunner, php-fpm) must be able to traverse into the deployment dir
  file { "/home/${deployment_owner}":
    ensure  => directory,
    owner   => $deployment_owner,
    mode    => '0751',
    require => User[ $deployment_owner ],
  }

  exec { "Create ${deployment_dir}":
    creates => $deployment_dir,
    command => "mkdir -p ${deployment_dir}",
    path    => $facts['path'],
  } -> file { $deployment_dir:
    ensure => 'directory',
    owner  => $deployment_owner,
    group  => $deployment_group,
  }

  $cache_dirs = [
    '/resources',
    '/resources/caches',
    "/resources/caches/${domain}",
    "/resources/caches/${domain}/general",
    "/resources/caches/${domain}/groups",
  ]

  file { $cache_dirs:
    ensure => 'directory',
  }

  $command = @("COMMAND"/L)
    /usr/bin/setfacl -R -m \
    user::rwx,\
    group::r-x,\
    group:www-shared:rwx,\
    mask::rwx,\
    other::r-x,\
    default:user::rwx,\
    default:group::r-x,\
    default:group:www-shared:rwx,\
    default:mask::rwx,\
    default:other::r-x \
    /resources/caches/${domain}
    |-COMMAND

  exec { 'Set cache permissions':
    command => $command,
    require => [ Package[ 'acl' ], Group[ 'www-shared' ] ],
  }

  $repos.each |$name, $cfg| {
    # Nested repos must wait for the core clone, or they create the
    # directory first and the core clone fails on a non-empty path
    $repo_require = $name ? {
      'workdir' => [ User[ $deployment_owner ], Group[ $deployment_group ] ],
      default   => [ User[ $deployment_owner ], Group[ $deployment_group ], Vcsrepo[$workdir] ],
    }
    vcsrepo {
      default:
        ensure   => present,
        provider => 'git',
        owner    => $deployment_owner,
        group    => $deployment_group,
        require  => $repo_require;
      "${deployment_dir}/${name}":
        * => $cfg,
    }
  }

  vcsrepo { $config_dir:
    ensure   => present,
    provider => 'git',
    owner    => $deployment_owner,
    group    => $deployment_group,
    source   => 'https://gerrit.wikimedia.org/r/translatewiki',
  }

  exec { 'Generate secret key':
    command => 'openssl rand -hex -out secretkey 64',
    cwd     => $deployment_dir,
    user    => $deployment_owner,
    creates => "${deployment_dir}/secretkey",
    path    => $facts['path'],
  }

  file { "${workdir}/composer.local.json":
    ensure  => 'link',
    target  => "${config_dir}/translatewiki-composer.json",
    owner   => $deployment_owner,
    group   => $deployment_group,
    require => Vcsrepo[$workdir],
  }

  exec { 'Initial composer install':
    command     => 'composer install',
    cwd         => $workdir,
    user        => $deployment_owner,
    creates     => "${workdir}/vendor",
    path        => $facts['path'],
    environment => [ "HOME=/home/${deployment_owner}" ],
    require     => [ Package[ 'composer' ], Vcsrepo[$workdir] ],
  }

  # Create the wiki DB user; the installer's MysqlCreateUserTask skips
  # CREATE USER when its wikiuser test connection matches MariaDB's
  # anonymous account (empty password), and its GRANT then fails because
  # the default sql_mode (NO_AUTO_CREATE_USER) forbids implicit creation
  mysql_user { 'wikiuser@localhost':
    ensure => present,
  }
  mysql_grant { 'wikiuser@localhost/wiki.*':
    ensure     => present,
    user       => 'wikiuser@localhost',
    table      => 'wiki.*',
    privileges => ['ALL'],
    require    => Mysql_user['wikiuser@localhost'],
  }

  # Throwaway password (reset after setup); MediaWiki rejects passwords that
  # match the user name or are shorter than 10 characters
  $install_cmd = @(COMMAND/L)
    php maintenance/install.php --dbname=wiki --dbuser=wikiuser --installdbuser=root \
    --pass change-this-password X Developer
    |-COMMAND

  exec { 'Install MediaWiki':
    command => $install_cmd,
    cwd     => $workdir,
    creates => "${workdir}/LocalSettings.php",
    require => [ Exec['Initial composer install'], Mysql_grant['wikiuser@localhost/wiki.*'] ],
    path    => $facts['path'],
  } ~> exec { 'Remove auto-generated LocalSettings.php':
    refreshonly => true,
    command     => 'rm LocalSettings.php',
    cwd         => $workdir,
    path        => $facts['path'],
  }

  file { "${workdir}/LocalSettings.php":
    ensure  => present,
    content => template('wiki/LocalSettings.php.erb'),
    owner   => $deployment_owner,
    group   => $deployment_group,
    require => Exec['Remove auto-generated LocalSettings.php'],
  }

  file { "${deployment_dir}/CustomSettings.php":
    ensure  => present,
    replace => 'no', # do not overwrite
    content => '',
    owner   => $deployment_owner,
    group   => $deployment_group,
  }
}
