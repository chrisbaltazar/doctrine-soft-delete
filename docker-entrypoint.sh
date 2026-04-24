#!/bin/bash
set -e

# Update dependencies on every start
composer update \
  --no-interaction \
  --prefer-dist \
  --optimize-autoloader && \

bin/console doctrine:database:drop --force --if-exists && \
bin/console doctrine:database:create && \
bin/console doctrine:migrations:migrate --no-interaction

exec symfony server:start --port=8000 --no-tls --allow-http --allow-all-ip --dir=public