#!/usr/bin/env bash

set -e

echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

composer install

php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

exec "$@"
