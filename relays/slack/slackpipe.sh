#!/bin/sh
set -e

DIRSCRIPT=$(dirname "$(readlink -f "$0")")

php $DIRSCRIPT/../logfilter.php /www/translatewiki.net/logs/error_php | php $DIRSCRIPT/slack-logger.php
