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

  file { "/etc/cron.d/backup":
    # Enable when new server is primary
    ensure  => absent,
    content => template("backup/backup.erb"),
  }

  file { "/root/backup.sh":
    source  => 'puppet:///modules/backup/backup.sh',
  }

  file { "/root/.duplicity.conf":
    source  => 'puppet:///modules/backup/duplicity.conf',
  }

  file { "/root/dump-databases.sh":
    content => template("backup/dump-databases.sh.erb"),
  }

  file { '/etc/logrotate.d/twn-database-backup':
    source  => 'puppet:///modules/backup/logrotate'
  }
}
