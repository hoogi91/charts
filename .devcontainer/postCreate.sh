#!/usr/bin/env bash
set -x

composer install
.Build/bin/typo3cms install:setup -f -n \
    --database-driver=pdo_sqlite \
    --admin-user-name=admin \
    --admin-password=password \
    --site-name="TYPO3 Devcontainer" \
    --site-setup-type=site \
    --web-server-config=apache

sudo chmod a+x "$(pwd)"
sudo rm -rf /var/www/html
sudo ln -s "$(pwd)/.Build/web/" /var/www/html
