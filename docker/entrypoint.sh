#!/bin/bash

# Fix AH00534: Force disable conflicting MPMs at runtime before starting Apache
# This is a fail-safe in case the build-time fix was overridden
if [ -d "/etc/apache2/mods-enabled" ]; then
    rm -f /etc/apache2/mods-enabled/mpm_event.load
    rm -f /etc/apache2/mods-enabled/mpm_worker.load
    rm -f /etc/apache2/mods-enabled/mpm_event.conf
    rm -f /etc/apache2/mods-enabled/mpm_worker.conf
fi

# Configure port for Railway/Heroku/Render if PORT env is set
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
fi

# Fix permissions at runtime (crucial for PaaS)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run migrations
php artisan migrate --force

# Run seeders (safe because we use firstOrCreate)
php artisan db:seed --force

# Cache config and routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
apache2-foreground
