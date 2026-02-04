#!/bin/bash

# Configure port for Railway/Heroku/Render if PORT env is set
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
fi

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
