#!/bin/sh
set -e

php /home/betawiki/config/relays/logfilter.php /www/translatewiki.net/logs/error_php | nc -u 127.0.0.1 8966
