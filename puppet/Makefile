.PHONY: test testpp checkheaders apply
test: testpp checkheaders

checkheaders:
	for files in modules/*/files; do find $$files -type f |xargs -r grep -L 'file managed by puppet'; done

testpp:
	git ls-files | grep "\.pp$$" | xargs -r puppet parser validate

noop:
	puppet apply --noop --verbose --show_diff --modulepath ./modules site.pp

apply: site.pp
	puppet apply --modulepath ./modules site.pp
