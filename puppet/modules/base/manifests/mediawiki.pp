# = Class: base::mediawiki
#
# Provides base configuration for MediaWiki.
#
class base::mediawiki {
  $packages = [
    'doxygen',
    'fontconfig',
    # Development extras
    'jpegoptim',
    'optipng',
    # Dependencies for compiling wikidiff2
    'g++',
    'pkg-config',
    'libthai-dev',
    # fonts for phplot
    'fonts-dejavu',
  ]
  stdlib::ensure_packages($packages, {
    ensure => 'present',
  })
}
