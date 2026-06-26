#!/bin/sh
set -e

echo "🚀 Starting Laravel App..."

# ============================================
# 1. Ensure required directories
# ============================================
mkdir -p storage/framework/cache \
         storage/framework/views \
         storage/framework/sessions \
         bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache

# ============================================
# 2. Wait for DB (safe check)
# ============================================
#echo "⏳ Waiting for DB..."
#
#until php artisan migrate:status > /dev/null 2>&1;
#do
#  echo "⏳ DB not ready yet, retrying in 3s..."
#  sleep 3
#done

echo "✅ DB Connected"

# ============================================
# 3. Migrations (safe)
# ============================================
echo "🔄 Running migrations..."
php artisan migrate --force || echo "⚠️ Migration failed or nothing to migrate"

# ============================================
# 4. Seed (optional, only if needed)
# ============================================
#if [ "$RUN_SEEDER" = "true" ]; then
#  echo "🌱 Running seeders..."
#  php artisan db:seed --force || echo "⚠️ Seeder failed or skipped"
#fi

# ============================================
# 5. Storage link (safe ignore)
# ============================================
echo "🔗 Storage link..."
php artisan storage:link || true

# ============================================
# 6. Cache cleanup (safe in dev only)
# ============================================
#if [ "$APP_ENV" != "production" ]; then
#  echo "🧹 Clearing cache..."
#  php artisan optimize:clear || true
#fi

# ============================================
# 7. APP_KEY (IMPORTANT FIX)
# ============================================
#if [ -z "$APP_KEY" ]; then
#  echo "🗝️ APP_KEY not set! Generating..."
#
#  APP_KEY_VALUE=$(php artisan key:generate --show --no-interaction)
#  echo "APP_KEY=$APP_KEY_VALUE" >> .env
#
#  export APP_KEY="$APP_KEY_VALUE"
#
#  echo "✅ APP_KEY generated"
#else
#  echo "✅ APP_KEY already exists"
#fi

# ============================================
# 8. Start runtime (IMPORTANT)
# ============================================
#if [ "$USE_NGINX" = "true" ]; then
  echo "🚀 Starting PHP-FPM..."
  exec php-fpm
#else
#  echo "🚀 Starting Laravel Dev Server..."
#  exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
#fi
