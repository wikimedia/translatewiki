#!/bin/sh
WIKI=/www/translatewiki.net/w
php /home/betawiki/config/irc-relay/logfilter.php ${WIKI}/logs/error_php | nc -u localhost 8966
