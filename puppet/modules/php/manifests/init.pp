class php {
  include apt
  apt::ppa { 'ppa:ondrej/php5-oldstable': }

  package { [
    'php5-cli',
    'php5-curl',
    'php5-fpm',
    'php5-gd',
    'php5-intl',
    'php5-mysql',
    'php-pear',
    ]:
    ensure => present,
  }

  service { 'php5-fpm':
    ensure     => running,
    enable     => true,
    hasstatus  => true,
    hasrestart => true,
    require    => Package['php5-fpm'],
  }
}
