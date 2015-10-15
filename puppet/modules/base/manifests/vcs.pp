# = Class: base
#
# Provides installation and configuration for translatewiki.net supported
# version control systems.
#
class base::vcs {
  package { [
    'bzr',
    'cvs',
    'gettext',
    'mercurial',
    'subversion',
    ]: ensure => present,
  }
}
