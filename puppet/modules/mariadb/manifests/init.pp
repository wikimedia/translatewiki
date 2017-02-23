# = Class: mariadb
#
# Provides mariadb installation and configuration for translatewiki.net.
#
class mariadb {
  class { '::mysql::server':
    package_name     => 'mariadb-server',
    restart          => false,
    override_options => {
      mysqld      => {
        character-set-server            => 'utf8',
        innodb_additional_mem_pool_size => '400M',
        innodb_buffer_pool_instances    => '12',
        innodb_buffer_pool_size         => '12G',
        innodb_file_per_table           => '1',
        innodb_log_file_size            => '500M',
        thread_pool_size                => '16',
        key_buffer_size                 => '16M',
        query_cache_size                => '0',
        query_cache_type                => '0',
        ssl                             => '0',
        table_cache                     => '1500',
        log-error                       => undef
      },
      mysqld_safe => {
        log-error => undef
      },
      client      => {
        default-character-set => 'utf8'
      }
    }
  }

  class { '::mysql::client':
    package_name => 'mariadb-client',
  }

  package { [
    'mytop',
    'mysqltuner',
    ]: ensure => present,
  }
}
