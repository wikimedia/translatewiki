# = Class: backup
#
# Handles translatewiki.net offsite backups. We backup certain directories
# and a database dump daily to offsite with duplicity. Backups are encrypted.
#
# == Parameters:
#
# $databases:: What databases to dump and backup.
#
class backup ($databases) {
  package { 'duplicity':
    ensure => present,
  }

  package { 'pbzip2':
    ensure => present,
  }

  file { '/etc/cron.d/backup':
    # Enable when new server is primary
    ensure  => present,
    content => template('backup/backup.erb'),
  }

  file { '/root/.ssh/config':
    source => 'puppet:///modules/backup/ssh-config',
  }

  file { '/root/backup.sh':
    source => 'puppet:///modules/backup/backup.sh',
    mode   => '0744',
  }

  file { '/root/.duplicity.conf':
    source => 'puppet:///modules/backup/duplicity.conf',
  }

  file { '/root/dump-databases.sh':
    content => template('backup/dump-databases.sh.erb'),
    mode    => '0744',
  }
}
