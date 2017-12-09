#!/bin/bash

set -o pipefail

DIRSCRIPT="`dirname \"$0\"`"
DIRSCRIPT="`( cd \"$DIRSCRIPT\" && pwd )`"
NAME="non-mediawiki"

cd /resources/projects
php "$DIRSCRIPT/../repong/repong.php" list | grep -v ^mediawiki | xargs -n1 -P4 "$DIRSCRIPT/repo" update

php /srv/mediawiki/targets/production/extensions/Translate/scripts/processMessageChanges.php \
	--name "$NAME" --safe-import --group=* --skipgroup=ext-*,core,mediawiki* \
	| xargs -l "$DIRSCRIPT/udpcast" || "$DIRSCRIPT/udpcast" "Fatal error!"
