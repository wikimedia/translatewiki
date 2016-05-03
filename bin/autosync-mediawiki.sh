#!/bin/sh

DIRSCRIPT="`dirname \"$0\"`"
DIRSCRIPT="`( cd \"$DIRSCRIPT\" && pwd )`"

NAME=mediawiki

echo mediawiki mediawiki-extensions mediawiki-skins | xargs -n1 -P2 "$DIRSCRIPT/repo" update

php /srv/mediawiki/targets/production/extensions/Translate/scripts/processMessageChanges.php \
	--name $NAME --group=core,ext-*,mediawiki* --quiet

if [ -f "/resources/caches/translatewiki.net/messagechanges.$NAME.cdb" ]
then
	"$DIRSCRIPT/udpcast" "Raymond: https://translatewiki.net/wiki/Special:ManageMessageGroups/$NAME"
else
	"$DIRSCRIPT/udpcast" "Raymond: No updates"
fi
