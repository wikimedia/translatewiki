# = Class: translationmemory
#
# Stub for our currently solr based translation memory
#
class translationmemory {
  package { 'openjdk-6-jre-headless':
    ensure => 'present',
  }
}
