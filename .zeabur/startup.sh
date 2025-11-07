#!/bin/bash
# Zeabur startup script to fix database permissions

echo "=== Fixing database permissions ==="

# Find the database file
DB_FILE="/var/www/html/data/skidiyog.db"

if [ -f "$DB_FILE" ]; then
    echo "Database file found: $DB_FILE"

    # Fix permissions
    chmod 666 "$DB_FILE"
    echo "Permissions set to 666"

    # Verify
    ls -la "$DB_FILE"
else
    echo "Database file not found, will be created on first run"

    # Ensure data directory has correct permissions
    mkdir -p /var/www/html/data
    chmod 777 /var/www/html/data
    echo "Data directory created with 777 permissions"
fi

echo "=== Startup complete ==="
