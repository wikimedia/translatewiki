class mariadb {
  apt::source { 'mariadb':
    location        => 'http://mirror3.layerjet.com/mariadb/repo/5.5/ubuntu',
    release         => "${::lsbdistcodename}",
    repos           => 'main',
    key             => 'cbcb082a1bb943db',
    key_server      => 'keyserver.ubuntu.com',
  }

  class { '::mysql::server':
    package_name => 'mariadb-server',
    require => Apt::Source['mariadb'],
    override_options => {
      mysqld => {
        character-set-server => 'utf8'
      },
      client => {
        default-character-set => 'utf8'
      }
    }
  }

  class { '::mysql::client':
    package_name => 'mariadb-client',
    require => Apt::Source['mariadb']
  }
}
