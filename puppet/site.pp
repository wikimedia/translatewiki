File {
  owner   => 'root',
  group   => 'root',
}

node default {
  include base
  include nginx
  include php
  include puppet
  include memcached
}
