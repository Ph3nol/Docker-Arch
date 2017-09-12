#!/bin/sh
set -e

composer install

docker-php-entrypoint php -a

exec "$@"
