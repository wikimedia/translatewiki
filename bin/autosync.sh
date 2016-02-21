#!/bin/sh

echo freecol hivejs | xargs -n1 -P2 repo update
php /srv/mediawiki/targets/production/extensions/Translate/scripts/processMessageChanges.php \
	--safe-import --group=out-freecol,hivejs* \
	| xargs -l /home/betawiki/config/bin/udpcast
