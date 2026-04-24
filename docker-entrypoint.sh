#!/bin/bash
set -e

# Update dependencies on every start
composer update \
  --no-interaction \
  --prefer-dist \
  --optimize-autoloader

exec symfony server:start --port=8000 --no-tls --allow-http --allow-all-ip --dir=public