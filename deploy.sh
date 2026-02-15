#!/bin/sh

# 1. Check Maintenance Mode (Direct check)
if [ "$MAINTENANCE_MODE" = "true" ]; then
  echo "Entering maintenance mode..."
  php artisan down
fi

# 2. Build Assets
npm run build

# 3. Clear Cache
php artisan optimize:clear

# 4. Cache Config & View ONLY (No Route Cache)
php artisan config:cache
php artisan event:cache
# php artisan route:cache  <-- (Disabling route cache to avoid duplicates)
php artisan view:cache

# 5. Run Migrations
php artisan migrate --force
php artisan db:seed --force
# 6. Exit Maintenance Mode
if [ "$MAINTENANCE_MODE" != "true" ]; then
  echo "Exiting maintenance mode..."
  php artisan up
fi
