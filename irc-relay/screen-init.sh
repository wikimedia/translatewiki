#!/bin/sh
screen -dmS ircrelay

screen -S ircrelay -p0 -X title "IRC error log relay"
screen -S ircrelay -p0 -X exec perl /home/betawiki/config/irc-relay/error-relay.pl

screen -S ircrelay -p1 -X screen
screen -S ircrelay -p1 -X title "IRC recent changes relay"
screen -S ircrelay -p1 -X exec perl /home/betawiki/config/irc-relay/rc-relay.pl

screen -S ircrelay -p2 -X screen
screen -S ircrelay -p2 -X title "Error log filter"
screen -S ircrelay -p2 -X exec php /www/w/extensions/Translate/scripts/logfilter.php /www/w/logs/error_php | nc -u localhost 8966
