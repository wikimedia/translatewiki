# This file is managed by puppet
if [ -z ${PATH-} ] ; then
  export PATH=/home/betawiki/config/bin
elif ! echo ${PATH} | grep -q /home/betawiki/config/bin ; then
  export PATH=${PATH}:/home/betawiki/config/bin
fi
