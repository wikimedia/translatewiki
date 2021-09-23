# = Class: backup
#
# Handles translatewiki.net offsite backups. We backup certain directories
# and a database dump daily to offsite with duplicity. Backups are encrypted.
#
# == Parameters:
#
# $databases:: What databases to dump and backup.
#
class backup (
  Array $databases = [],
) {
  $packages = [
    'duplicity',
    'backblaze-b2',
    'python3-b2sdk',
    'pbzip2'
  ]
  ensure_packages($packages, {
    ensure => 'present',
  })

  file { '/etc/cron.d/backup':
    ensure => present,
    source => 'puppet:///modules/backup/backup',
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
