class ssh-conf {
  include ssh::client

  # @todo Remove root login after migrating
  class { "ssh::server":
    port => 22,
    permit_root_login => 'no'
  }
}
