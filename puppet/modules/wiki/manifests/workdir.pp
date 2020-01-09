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

  exec { "Create ${deployment_dir}":
    creates => $deployment_dir,
    command => "mkdir -p ${deployment_dir}",
    path    => $::path,
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
    vcsrepo {
      default:
        ensure   => present,
        provider => 'git',
        owner    => $deployment_owner,
        group    => $deployment_group;
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
    path    => $::path,
  }

  file { "${workdir}/composer.local.json":
    ensure => 'link',
    target => "${config_dir}/translatewiki-composer.json",
    owner  => $deployment_owner,
    group  => $deployment_group,
  }

  exec { 'Initial composer install':
    command     => 'composer install',
    cwd         => $workdir,
    user        => $deployment_owner,
    creates     => "${workdir}/vendor",
    path        => $::path,
    environment => [ "HOME=/home/${deployment_owner}" ],
    require     => [ Package[ 'composer' ], Vcsrepo[$workdir] ],
  }

  $install_cmd = @(COMMAND/L)
    php maintenance/install.php --dbname=wiki --dbuser=wikiuser --installdbuser=root \
    --pass developer X Developer
    |-COMMAND

  exec { 'Install MediaWiki':
    command => $install_cmd,
    cwd     => $workdir,
    creates => "${workdir}/LocalSettings.php",
    require => Exec['Initial composer install'],
    path    => $::path,
  } ~> exec { 'Remove auto-generated LocalSettings.php':
    refreshonly => true,
    command     => 'rm LocalSettings.php',
    cwd         => $workdir,
    path        => $::path,
  }

  file { "${workdir}/LocalSettings.php":
    ensure  => present, # do not override
    content => template('wiki/LocalSettings.php.erb'),
    owner   => $deployment_owner,
    group   => $deployment_group,
    require => Exec['Remove auto-generated LocalSettings.php'],
  }

  file { "${deployment_dir}/CustomSettings.php":
    ensure  => present,
    content => '',
    owner   => $deployment_owner,
    group   => $deployment_group,
  }
}
