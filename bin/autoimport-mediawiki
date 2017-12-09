#!/bin/sh

DIRSCRIPT="`dirname \"$0\"`"
DIRSCRIPT="`( cd \"$DIRSCRIPT\" && pwd )`"
NAME=mediawiki

cd /resources/projects
php "$DIRSCRIPT/../repong/repong.php" list | grep ^mediawiki | xargs -n1 -P4 "$DIRSCRIPT/repo" update

php /srv/mediawiki/targets/production/extensions/Translate/scripts/processMessageChanges.php \
	--name $NAME --group=core,ext-*,mediawiki* --quiet

if [ -f "/resources/caches/translatewiki.net/messagechanges.$NAME.cdb" ]
then
	"$DIRSCRIPT/udpcast" "Raymond: https://translatewiki.net/wiki/Special:ManageMessageGroups/$NAME"
else
	"$DIRSCRIPT/udpcast" "Raymond: No updates"
fi
