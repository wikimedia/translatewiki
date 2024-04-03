# = Class: mariadb
#
# Provides mariadb installation and configuration for translatewiki.net.
#
class mariadb {
  class { '::mysql::server':
    package_name     => 'mariadb-server',
    service_name     => 'mariadb',
    restart          => false,
    override_options => {
      mysqld      => {
        character-set-server         => 'utf8',
        innodb_buffer_pool_instances => '12',
        innodb_buffer_pool_size      => '20G',
        innodb_file_per_table        => '1',
        innodb_log_file_size         => '2G',
        thread_pool_size             => '16',
        key_buffer_size              => '16M',
        query_cache_size             => '0',
        query_cache_type             => '0',
        ssl                          => '0',
        table_cache                  => '1500',
        log-error                    => undef,
        # temporary fix for https://github.com/puppetlabs/puppetlabs-mysql/issues/1509
        ssl-ca                       => undef,
        ssl-cert                     => undef,
        ssl-key                      => undef,
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

  $packages = [
    'mysqltuner',
  ]
  stdlib::ensure_packages($packages, {
    ensure => 'present',
  })
}
