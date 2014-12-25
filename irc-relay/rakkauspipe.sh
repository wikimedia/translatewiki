#!/bin/sh
php /home/betawiki/config/irc-relay/logfilter.php /www/translatewiki.net/logs/error_php | nc -u 127.0.0.1 8966
