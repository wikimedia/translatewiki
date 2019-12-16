#!/bin/bash
# file managed by puppet

# uncomment for debug
#set -x

# Provides GPG_PASSPHRASE, B2_keyID, B2_applicationKey, B2_bucket
source /root/secrets/backup

source /root/.duplicity.conf

# duplicity command
DUPEXEC="--encrypt-key $ENCRKEY --sign-key $SIGNKEY $DUPOPTS $*"
# loop on directories
echo -n "---- Incremental backup of $HOSTNAME ---- "; date
for i in $BACKDIRS
do
    echo "Starting backup of directory /$i"
    # backup $i
    duplicity $DUPEXEC /$i $RPATH/$i
    echo
done
echo -n "---- Finished backup on $HOSTNAME ---- "; date
echo
echo
