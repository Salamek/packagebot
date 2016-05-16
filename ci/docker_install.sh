#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && [[ ! -e /.dockerinit ]] && exit 0

set -xe

# Install git (the php image doesn't have it) which is required by composer
apt-get update -yqq
apt-get install git -yqq
apt-get install php5-curl -yqq
apt-get install unzip -yqq
apt-get install phpunit -yqq

# Install phpunit, the tool that we will use for testing
#curl -o /usr/local/bin/phpunit https://phar.phpunit.de/phpunit.phar
#chmod +x /usr/local/bin/phpunit

# Install mysql driver
# Here you can install any other extension that you need
docker-php-ext-install pdo_mysql