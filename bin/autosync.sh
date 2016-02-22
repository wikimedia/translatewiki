#!/bin/sh

echo blockly citationhunt codev freecol hivejs | xargs -n1 -P2 repo update
php /srv/mediawiki/targets/production/extensions/Translate/scripts/processMessageChanges.php \
	--safe-import --group=out-blockly*,citationhunt,codev,out-freecol,hivejs* \
	| xargs -l /home/betawiki/config/bin/udpcast
