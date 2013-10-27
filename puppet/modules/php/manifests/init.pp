class php {
  include apt
  apt::ppa { 'ppa:ondrej/php5-oldstable': }

  package { ['php5-fpm', 'php5-cli']:
    ensure => present,
  }

  package { [
    'php5-curl',
    'php5-gd',
    'php5-intl',
    'php5-mysql',
    'php-apc',
    'php-pear',
    ]:
    ensure => present,
    require => Package['php5-fpm'],
  }

  service { 'php5-fpm':
    ensure     => running,
    enable     => true,
    hasstatus  => true,
    hasrestart => true,
    require    => Package['php5-fpm'],
  }
}
