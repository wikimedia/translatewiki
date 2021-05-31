.PHONY: test testpp checkheaders apply
PUPPET=/opt/puppetlabs/bin/puppet

test: testpp checkheaders lint

checkheaders:
	for files in modules/*/files; do find $$files -type f |xargs -r grep -L 'managed by puppet'; done

testpp:
	git ls-files | grep "\.pp$$" | xargs -r $(PUPPET) parser validate

lint:
	sudo apt install -y -qqq puppet-lint
	puppet-lint modules

noop:
	librarian-puppet install --path ./vendor
	sudo $(PUPPET) apply --hiera_config hiera.yaml --modulepath 'modules:vendor:$$basemodulepath' --noop --test site.pp

apply: site.pp
	librarian-puppet install --path ./vendor
	sudo $(PUPPET) apply --hiera_config hiera.yaml --modulepath 'modules:vendor:$$basemodulepath' site.pp
