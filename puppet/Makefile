.PHONY: test testpp checkheaders apply
test: testpp checkheaders

checkheaders:
	for files in modules/*/files; do find $$files -type f |xargs -r grep -L 'file managed by puppet'; done

testpp:
	git ls-files | grep "\.pp$$" | xargs -r /opt/puppetlabs/bin/puppet parser validate

noop:
	librarian-puppet install --path ./vendor
	sudo /opt/puppetlabs/bin/puppet apply --noop --test --modulepath './modules:./vendor:$$basemodulepath' site.pp

apply: site.pp
	librarian-puppet install --path ./vendor
	sudo /opt/puppetlabs/bin/puppet apply --modulepath './modules:./vendor:$$basemodulepath' site.pp
