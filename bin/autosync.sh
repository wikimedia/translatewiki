#!/bin/bash

set -o pipefail

DIRSCRIPT="`dirname \"$0\"`"
DIRSCRIPT="`( cd \"$DIRSCRIPT\" && pwd )`"

cat "$DIRSCRIPT/EXTERNAL-PROJECTS" | xargs -n1 -P2 "$DIRSCRIPT/repo" update

php /srv/mediawiki/targets/production/extensions/Translate/scripts/processMessageChanges.php \
	--safe-import --group=* --skipgroup=ext-*,core,mediawiki*,out-wikimedia-mobile-wikipedia-ios* \
	| xargs -l "$DIRSCRIPT/udpcast" || "$DIRSCRIPT/udpcast" "Fatal error!"
