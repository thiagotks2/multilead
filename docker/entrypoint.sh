#!/bin/bash

# Criar pasta do socket e dar permissão
mkdir -p /var/run/php
chown www-data:www-data /var/run/php

# O Node já está no PATH via apk, não precisa de NVM
# O Laravel Installer já está no PATH via ENV no Dockerfile

# Inicia o supervisor
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf