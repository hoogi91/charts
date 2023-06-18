#!/usr/bin/env bash
set -x

composer install
.Build/bin/typo3 install:setup -f -n \
    --database-driver=pdo_sqlite \
    --admin-user-name=admin \
    --admin-password=\$Password2 \
    --site-name="TYPO3 Devcontainer" \
    --site-setup-type=site \
    --web-server-config=apache

.Build/bin/typo3 configuration:set SYS/trustedHostsPattern '.*'
.Build/bin/typo3 configuration:set SYS/features/security.backend.enforceReferrer false --json

sudo chmod a+x "$(pwd)"
sudo rm -rf /var/www/html
sudo ln -s "$(pwd)/.Build/web/" /var/www/html
