#!/usr/bin/env bash

set -e

echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

ip -4 route list match 0/0 | awk '{print $$3" host.docker.internal"}' >> /etc/hosts && php-fpm --allow-to-run-as-root
