#!/bin/sh
php /www/w/extensions/Translate/scripts/logfilter.php /www/w/logs/error_php | nc -u localhost 8966
