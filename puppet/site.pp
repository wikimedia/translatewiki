lookup('classes', {merge => unique}).include

# It is not possible to create resources directly in hiera data
lookup('keyholder::agent').each |$name, $cfg| {
  ::keyholder::agent {
    "${name}": * => $cfg,
  }
}
