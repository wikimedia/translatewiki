class exim-conf {
  file { '/etc/logrotate.d/twn':
    source  => 'puppet:///modules/logrotate/twn'
  }

  # @todo Should eventually end up in a backup module
  file { '/etc/logrotate.d/twn-database-backup':
    source  => 'puppet:///modules/logrotate/twn-database-backup'
  }
}
