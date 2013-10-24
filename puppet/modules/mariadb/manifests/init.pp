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
        character-set-server            => 'utf8',
        innodb_additional_mem_pool_size => '400M',
        innodb_buffer_pool_size         => '8G',
        innodb_file_per_table           => 'true',
        innodb_log_file_size            => '500M',
        key_buffer                      => '64M',
        log_slow_queries                => '/var/log/mysql/mysql-slow.log',
        long_query_time                 => '1',
        query_cache_size                => '0',
        query_cache_type                => '0',
        ssl                             => '0',
        table_cache                     => '750'
      },
      client => {
        default-character-set => 'utf8'
      }
    }
  }

  class { '::mysql::client':
    package_name => 'mariadb-client',
    require      => Apt::Source['mariadb']
  }
}
