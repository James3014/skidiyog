#!/bin/bash
set -e

# Use PORT environment variable from Zeabur, default to 80
PORT=${PORT:-80}

echo "[ENTRYPOINT] Starting container..."
echo "[ENTRYPOINT] PORT environment variable: $PORT"

# Update Apache configuration to listen on the correct port
echo "[ENTRYPOINT] Updating Apache configuration..."
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf

echo "[ENTRYPOINT] Apache will listen on port: $PORT"
grep "Listen" /etc/apache2/ports.conf
grep "VirtualHost" /etc/apache2/sites-available/000-default.conf | head -1

# Start Apache
echo "[ENTRYPOINT] Starting Apache..."
exec apache2-foreground
