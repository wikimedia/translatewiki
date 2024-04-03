#!/bin/bash
set -o nounset -o pipefail -o errexit

HOSTNAME=${1:-dev.translatewiki.net}
DEB=puppet7-release-bullseye.deb

echo -e "\n\n\nInstalling puppet..."
cd /root
wget "https://apt.puppetlabs.com/$DEB" -O "$DEB"
dpkg -i "$DEB"
apt update
apt install -y git puppet-agent make librarian-puppet

# Update PATHs
. /etc/profile
echo -e "\n\n\nDownloading configuration..."
git clone https://gerrit.wikimedia.org/r/translatewiki
cd translatewiki/puppet

hostnamectl set-hostname "$HOSTNAME"
cp data/developer.yaml.example data/developer.yaml
nano data/developer.yaml

echo -e "\n\n\nRunning puppet..."
make apply

# Update PATHs
. /etc/profile
echo -e "\n\n\nUpdating wiki..."
cd /home/developer/translatewiki
twn-update-all
# Fails halfway the first time due to checkuser
twn-update-database || twn-update-database

echo -e "\n\n\nConfiguring elastic search..."
cd /home/developer/mediawiki/workdir
php maintenance/run.php ./extensions/Translate/scripts/ttmserver-export.php

cd /root
rm -r "$DEB" translatewiki

echo "\n\n\nTo access developer account on the wiki, you need to reset the password with"
echo "  php /home/developer/mediawiki/workdir/maintenance/run.php changePassword --user Developer --password '...'"
echo "\n\n\nReboot the machine to ensure all config changes are enabled"
