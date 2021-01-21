source 'https://rubygems.org'

gem 'puppet', ENV['PUPPET_GEM_VERSION'] || '~> 4.10.9'
gem 'puppetlabs_spec_helper', '< 3.0.0'

# Puppet 3.7 fails on ruby 2.2+
# https://tickets.puppetlabs.com/browse/PUP-3796
gem 'safe_yaml', '~> 1.0.4'

gem 'rake', '~> 12.3', '>= 12.3.3'
gem 'xmlrpc' if RUBY_VERSION >= '2.4.0'

# https://phabricator.wikimedia.org/T242339
gem 'gettext', '~> 3.2.9'
gem 'pathspec', '< 1.0.0'
