#!/bin/bash

# Railway deployment script for WhatsML
# This script runs after the application is deployed

echo "🚀 Running post-deployment setup..."

# Create missing directories
echo "📁 Creating missing directories..."
mkdir -p modules/*/resources/views
touch modules/*/resources/views/.gitkeep

# Create storage directories
echo "📁 Creating storage directories..."
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache

# Generate APP_KEY if not set
echo "🔑 Generating application key..."
if [ -z "$APP_KEY" ]; then
    export APP_KEY=$(php -r "echo 'base64:' . base64_encode(random_bytes(32));")
    echo "Generated APP_KEY: ${APP_KEY:0:20}..."
fi

# Set basic environment variables if not set
echo "⚙️ Setting basic environment variables..."
export APP_NAME="${APP_NAME:-WhatsML}"
export APP_ENV="${APP_ENV:-production}"
export APP_DEBUG="${APP_DEBUG:-false}"
export APP_URL="${APP_URL:-https://whatsml-production-d457.up.railway.app}"

# Set database to file-based SQLite for reliability
echo "🗄️ Configuring database..."
export DB_CONNECTION="${DB_CONNECTION:-sqlite}"
export DB_DATABASE="${DB_DATABASE:-database/database.sqlite}"

# Create SQLite database file if it doesn't exist
mkdir -p database
touch database/database.sqlite

# Set session and cache to file-based for reliability
echo "💾 Configuring sessions and cache..."
export SESSION_DRIVER="${SESSION_DRIVER:-file}"
export CACHE_DRIVER="${CACHE_DRIVER:-file}"
export QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"

# Try to run database migrations (non-critical for healthcheck)
echo "📊 Running database migrations..."
php artisan migrate --force || echo "⚠️ Migrations failed, continuing..."

# Create storage link if needed
echo "🔗 Creating storage link..."
php artisan storage:link || echo "⚠️ Storage link failed, continuing..."

# Try to cache configurations (non-critical for healthcheck)
echo "⚡ Optimizing application..."
php artisan config:cache || echo "⚠️ Config cache failed, continuing..."
php artisan route:cache || echo "⚠️ Route cache failed, continuing..."
php artisan view:cache || echo "⚠️ View cache failed, continuing..."

echo "✅ Post-deployment setup completed!"
