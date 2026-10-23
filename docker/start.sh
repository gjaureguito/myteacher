#!/bin/sh
set -e
# Railway asigna el puerto en $PORT; Apache tiene que escuchar ahí.
PORT="${PORT:-8080}"
sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf
# Evita el error "More than one MPM loaded" en algunos entornos
a2dismod -q mpm_event mpm_worker >/dev/null 2>&1 || true
a2enmod -q mpm_prefork >/dev/null 2>&1 || true
exec apache2-foreground
