#!/bin/bash

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
