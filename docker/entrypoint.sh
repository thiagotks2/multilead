#!/bin/sh

mkdir -p /var/run/php
chown www-data:www-data /var/run/php

exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf