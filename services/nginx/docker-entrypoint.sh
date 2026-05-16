#!/bin/sh
set -e

VARS='${FRONTEND_DOMAIN} ${API_DOMAIN} ${MINIO_DOMAIN} ${REDIS_DOMAIN} ${UPTIME_DOMAIN}'

envsubst "$VARS" \
  < /etc/nginx/conf-templates/default.conf.template \
  > /etc/nginx/conf.d/default.conf

envsubst "$VARS" \
  < /etc/nginx/html-src/index.html.template \
  > /usr/share/nginx/html/index.html

exec "$@"
