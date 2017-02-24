# This Rakefile is meant to run linters and tests.
#
# You will need 'bundler' to install dependencies:
#
#  $ apt-get install bundler
#  $ bundle install
#
# Then run rake in using bundler environment:
#
#  $ bundle exec rake test
#
# To see all available tasks:
#
#  $ bundle exec rake
#

require 'puppet-lint/tasks/puppet-lint'
require 'puppet-syntax/tasks/puppet-syntax'

task :default => [:help]

desc 'Show the help'
task :help do
  system 'rake -T'
end

desc 'Run all build/tests commands (CI entry point)'
task test: [:syntax, :lint]
