# = Class: mariadb
#
# Provides mariadb installation and configuration for translatewiki.net.
#
class mariadb (
  # MariaDB 11 maps the full pool at startup; keep the default small for dev
  String $innodb_buffer_pool_size = '1G',
) {
  class { '::mysql::server':
    package_name            => 'mariadb-server',
    service_name            => 'mariadb',
    restart                 => false,
    # Drop anonymous accounts, remote root, and the test database
    remove_default_accounts => true,
    override_options        => {
      mysqld      => {
        character-set-server      => 'utf8',
        innodb_buffer_pool_size   => $innodb_buffer_pool_size,
        innodb_file_per_table     => '1',
        innodb_log_file_size      => '2G',
        innodb_snapshot_isolation => '0',
        thread_pool_size          => '16',
        key_buffer_size           => '16M',
        query_cache_size          => '0',
        query_cache_type          => '0',
        # boolean false also makes the module omit its ssl-ca/cert/key defaults
        ssl                       => false,
        table_cache               => '1500',
        log-error                 => undef,
      },
      mysqld_safe => {
        log-error => undef
      },
      client      => {
        default-character-set => 'utf8',
        ssl                   => '0',
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
