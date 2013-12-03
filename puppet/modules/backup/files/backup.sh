#!/bin/bash
# file managed by puppet

# uncomment for debug
#set -x

source /root/.duplicity.conf

# duplicity command
DUPEXEC="--encrypt-key $ENCRKEY --sign-key $SIGNKEY $DUPOPTS $*"
# loop on directories
echo -n "---- Incremental backup of $HOSTNAME ---- "; date
for i in $BACKDIRS
do
	echo "Starting backup of directory /$i"
	# create dirs and then backup
	$MKDIR $LPATH/$i && duplicity $DUPEXEC /$i $RPATH/$i
	# clean up
	duplicity remove-older-than 2M --force $DUPEXEC $RPATH/$i
	duplicity clean --force $DUPEXEC $RPATH/$i
	echo
done
echo -n "---- Finished backup on $HOSTNAME ---- "; date
echo
echo
