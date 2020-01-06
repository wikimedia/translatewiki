# = Class: users
#
# Provides /etc/aliases
#
class users::aliases {
  # Pull down the main aliases file
  file { '/etc/aliases':
    source => 'puppet:///modules/users/aliases'
  }

  # Rebuild the database, but only when the file changes
  exec { 'newaliases':
    path        => ['/usr/bin', '/usr/sbin'],
    subscribe   => File['/etc/aliases'],
    refreshonly => true
  }
}
