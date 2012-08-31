#!/bin/sh
WIKI=/www/translatewiki.net/w
php $WIKI/extensions/Translate/scripts/logfilter.php $WIKI/logs/error_php | nc -u localhost 8966
