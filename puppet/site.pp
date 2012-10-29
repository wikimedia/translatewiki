File {
  owner   => 'root',
  group   => 'root',
}

node default {
  include nginx
  include php
  include puppet
}
