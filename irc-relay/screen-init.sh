#!/bin/sh
screen -dmS ircrelay
screen -S ircrelay -p0 -X title "IRC error log relay"
screen -S ircrelay -p0 -X exec perl /home/betawiki/config/irc-relay/error-relay.pl

screen -dmS ircrcrelay
screen -S ircrcrelay -p0 -X title "IRC recent changes relay"
screen -S ircrcrelay -p0 -X exec perl /home/betawiki/config/irc-relay/rc-relay.pl

screen -dmS errorfilter
screen -S errorfilter -p0 -X title "Error log filter"
screen -S errorfilter -p0 -X exec /home/betawiki/config/irc-relay/rakkauspipe.sh
