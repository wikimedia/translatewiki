# = Class: base::vcs
#
# Provides installation and configuration for translatewiki.net supported
# version control systems.
#
class base::vcs {
  $packages = [
    'bzr',
    'cvs',
    'gettext',
    'mercurial',
    'subversion',
  ]
  ensure_packages($packages, {
    ensure => 'present',
  })
}
