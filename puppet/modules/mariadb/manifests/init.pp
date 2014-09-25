class mariadb {
  class { '::mysql::server':
    package_name     => 'mariadb-server',
    restart          => false,
    override_options => {
      mysqld => {
        character-set-server            => 'utf8',
        innodb_additional_mem_pool_size => '400M',
        innodb_buffer_pool_size         => '6G',
        innodb_file_per_table           => 'true',
        innodb_log_file_size            => '500M',
        key_buffer_size                 => '64M',
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
  }
}
