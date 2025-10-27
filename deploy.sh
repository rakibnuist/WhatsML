#!/bin/bash

# WhatsML Railway Deployment Script
# This script automates the deployment of WhatsML to Railway

set -e

echo "🚀 Starting WhatsML Railway Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if Railway CLI is installed
check_railway_cli() {
    print_status "Checking Railway CLI installation..."
    if ! command -v railway &> /dev/null; then
        print_error "Railway CLI is not installed. Please install it first:"
        echo "npm install -g @railway/cli"
        exit 1
    fi
    print_success "Railway CLI is installed"
}

# Check if user is logged in to Railway
check_railway_auth() {
    print_status "Checking Railway authentication..."
    if ! railway whoami &> /dev/null; then
        print_error "Not logged in to Railway. Please login first:"
        echo "railway login"
        exit 1
    fi
    print_success "Authenticated with Railway"
}

# Initialize Railway project
init_railway_project() {
    print_status "Initializing Railway project..."
    
    if [ ! -f "railway.json" ]; then
        print_warning "railway.json not found. Creating default configuration..."
        cat > railway.json << EOF
{
  "\$schema": "https://railway.app/railway.schema.json",
  "build": {
    "builder": "NIXPACKS"
  },
  "deploy": {
    "startCommand": "vendor/bin/heroku-php-apache2 public/",
    "healthcheckPath": "/",
    "healthcheckTimeout": 100,
    "restartPolicyType": "ON_FAILURE",
    "restartPolicyMaxRetries": 10
  }
}
EOF
    fi
    
    railway init
    print_success "Railway project initialized"
}

# Add required services
add_services() {
    print_status "Adding required services..."
    
    # Add PostgreSQL
    print_status "Adding PostgreSQL database..."
    railway add postgresql
    
    # Add Redis
    print_status "Adding Redis cache..."
    railway add redis
    
    print_success "Services added successfully"
}

# Set environment variables
set_environment_variables() {
    print_status "Setting environment variables..."
    
    # Basic configuration
    railway variables set APP_NAME="WhatsML Pro"
    railway variables set APP_ENV=production
    railway variables set APP_DEBUG=false
    
    # Generate APP_KEY if not set
    if [ -z "$APP_KEY" ]; then
        print_status "Generating application key..."
        APP_KEY=$(php artisan key:generate --show 2>/dev/null || echo "base64:$(openssl rand -base64 32)")
    fi
    railway variables set APP_KEY="$APP_KEY"
    
    # Database configuration
    railway variables set DB_CONNECTION=pgsql
    
    # Redis configuration
    railway variables set REDIS_HOST='${{Redis.REDIS_HOST}}'
    railway variables set REDIS_PASSWORD='${{Redis.REDIS_PASSWORD}}'
    railway variables set REDIS_PORT='${{Redis.REDIS_PORT}}'
    
    # Session and cache
    railway variables set SESSION_DRIVER=redis
    railway variables set CACHE_DRIVER=redis
    railway variables set QUEUE_CONNECTION=redis
    
    # Security
    railway variables set SESSION_SECURE_COOKIE=true
    
    print_success "Environment variables set"
}

# Set Mailtrap configuration
set_mailtrap_config() {
    print_status "Configuring Mailtrap for email testing..."
    
    railway variables set MAIL_MAILER=smtp
    railway variables set MAIL_HOST=sandbox.smtp.mailtrap.io
    railway variables set MAIL_PORT=2525
    railway variables set MAIL_USERNAME="332e1bc337992f"
    railway variables set MAIL_PASSWORD="d62bfacda4f02528aad2019527a2fb41"
    railway variables set MAIL_ENCRYPTION=tls
    
    if [ -z "$MAIL_FROM_ADDRESS" ]; then
        MAIL_FROM_ADDRESS="noreply@yourdomain.com"
    fi
    railway variables set MAIL_FROM_ADDRESS="$MAIL_FROM_ADDRESS"
    railway variables set MAIL_FROM_NAME="WhatsML Pro"
    
    print_success "Mailtrap configured for email testing"
}

# Set WhatsApp configuration
set_whatsapp_config() {
    print_status "Configuring WhatsApp integration..."
    
    if [ -n "$WHATSAPP_CLOUD_API_TOKEN" ]; then
        railway variables set WHATSAPP_CLOUD_API_TOKEN="$WHATSAPP_CLOUD_API_TOKEN"
    fi
    
    if [ -n "$WHATSAPP_CLOUD_API_PHONE_NUMBER_ID" ]; then
        railway variables set WHATSAPP_CLOUD_API_PHONE_NUMBER_ID="$WHATSAPP_CLOUD_API_PHONE_NUMBER_ID"
    fi
    
    if [ -n "$WHATSAPP_CLOUD_API_VERIFY_TOKEN" ]; then
        railway variables set WHATSAPP_CLOUD_API_VERIFY_TOKEN="$WHATSAPP_CLOUD_API_VERIFY_TOKEN"
    fi
    
    # Get app URL for webhook
    APP_URL=$(railway domain 2>/dev/null || echo "https://your-app-name.railway.app")
    railway variables set APP_URL="$APP_URL"
    railway variables set WHATSAPP_WEBHOOK_URL="$APP_URL/api/whatsapp-web/webhook"
    railway variables set SANCTUM_STATEFUL_DOMAINS="${APP_URL#https://}"
    
    print_success "WhatsApp integration configured"
}

# Set OpenRouter configuration
set_openrouter_config() {
    print_status "Configuring OpenRouter API..."
    
    if [ -n "$OPENROUTER_API_KEY" ]; then
        railway variables set OPENAI_API_KEY="$OPENROUTER_API_KEY"
        railway variables set OPENROUTER_API_KEY="$OPENROUTER_API_KEY"
    else
        # Use provided OpenRouter API key
        railway variables set OPENAI_API_KEY="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"
        railway variables set OPENROUTER_API_KEY="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"
    fi
    
    railway variables set OPENROUTER_BASE_URL="https://openrouter.ai/api/v1"
    railway variables set OPENROUTER_MODEL="meta-llama/llama-3.1-8b-instruct:free"
    
    print_success "OpenRouter API configured with free Llama model"
}

# Set payment gateway configuration
set_payment_config() {
    print_status "Configuring payment gateways..."
    
    if [ -n "$STRIPE_KEY" ]; then
        railway variables set STRIPE_KEY="$STRIPE_KEY"
    fi
    
    if [ -n "$STRIPE_SECRET" ]; then
        railway variables set STRIPE_SECRET="$STRIPE_SECRET"
    fi
    
    if [ -n "$PAYPAL_CLIENT_ID" ]; then
        railway variables set PAYPAL_CLIENT_ID="$PAYPAL_CLIENT_ID"
    fi
    
    if [ -n "$PAYPAL_CLIENT_SECRET" ]; then
        railway variables set PAYPAL_CLIENT_SECRET="$PAYPAL_CLIENT_SECRET"
    fi
    
    if [ -n "$MOLLIE_KEY" ]; then
        railway variables set MOLLIE_KEY="$MOLLIE_KEY"
    fi
    
    print_success "Payment gateways configured"
}

# Set file storage configuration
set_storage_config() {
    print_status "Configuring file storage..."
    
    if [ -n "$CLOUDINARY_CLOUD_NAME" ]; then
        railway variables set CLOUDINARY_CLOUD_NAME="$CLOUDINARY_CLOUD_NAME"
    fi
    
    if [ -n "$CLOUDINARY_API_KEY" ]; then
        railway variables set CLOUDINARY_API_KEY="$CLOUDINARY_API_KEY"
    fi
    
    if [ -n "$CLOUDINARY_API_SECRET" ]; then
        railway variables set CLOUDINARY_API_SECRET="$CLOUDINARY_API_SECRET"
    fi
    
    print_success "File storage configured"
}

# Deploy application
deploy_application() {
    print_status "Deploying application to Railway..."
    
    railway up
    
    print_success "Application deployed successfully"
}

# Run database migrations
run_migrations() {
    print_status "Running database migrations..."
    
    railway shell --command "php artisan migrate --force"
    
    print_success "Database migrations completed"
}

# Seed database
seed_database() {
    print_status "Seeding database..."
    
    railway shell --command "php artisan db:seed --force"
    
    print_success "Database seeded successfully"
}

# Optimize application
optimize_application() {
    print_status "Optimizing application..."
    
    railway shell --command "php artisan config:cache"
    railway shell --command "php artisan route:cache"
    railway shell --command "php artisan view:cache"
    railway shell --command "php artisan optimize"
    
    print_success "Application optimized"
}

# Setup queue worker
setup_queue_worker() {
    print_status "Setting up queue worker..."
    
    # Add queue worker service
    railway add
    
    # Configure queue worker
    railway variables set START_COMMAND="php artisan queue:work --sleep=3 --tries=3 --max-time=3600"
    
    print_success "Queue worker configured"
}

# Test deployment
test_deployment() {
    print_status "Testing deployment..."
    
    # Get app URL
    APP_URL=$(railway domain 2>/dev/null || echo "https://your-app-name.railway.app")
    
    # Test basic connectivity
    if curl -s -o /dev/null -w "%{http_code}" "$APP_URL" | grep -q "200"; then
        print_success "Application is responding"
    else
        print_warning "Application may not be responding correctly"
    fi
    
    # Test API endpoint
    if curl -s -o /dev/null -w "%{http_code}" "$APP_URL/api/health" | grep -q "200"; then
        print_success "API endpoint is responding"
    else
        print_warning "API endpoint may not be responding correctly"
    fi
    
    print_success "Deployment testing completed"
}

# Display deployment information
show_deployment_info() {
    print_status "Deployment Information:"
    
    APP_URL=$(railway domain 2>/dev/null || echo "https://your-app-name.railway.app")
    
    echo ""
    echo "🎉 WhatsML has been successfully deployed!"
    echo ""
    echo "📱 Application URL: $APP_URL"
    echo "🔧 Railway Dashboard: https://railway.app/dashboard"
    echo "📊 Monitoring: https://railway.app/dashboard (built-in)"
    echo ""
    echo "📋 Next Steps:"
    echo "1. Configure your custom domain (optional)"
    echo "2. Set up monitoring and alerts"
    echo "3. Test all features thoroughly"
    echo "4. Configure backup strategies"
    echo "5. Set up SSL certificates (automatic with Railway)"
    echo ""
    echo "🔐 Important:"
    echo "- Keep your API keys secure"
    echo "- Monitor your usage and costs"
    echo "- Set up proper error monitoring"
    echo "- Configure automated backups"
    echo ""
    echo "📚 Documentation:"
    echo "- Railway Docs: https://docs.railway.app"
    echo "- SendGrid Docs: https://docs.sendgrid.com"
    echo "- Laravel Docs: https://laravel.com/docs"
    echo ""
}

# Main deployment function
main() {
    echo "🚀 WhatsML Railway Deployment Script"
    echo "====================================="
    echo ""
    
    # Check prerequisites
    check_railway_cli
    check_railway_auth
    
    # Initialize project
    init_railway_project
    add_services
    
    # Configure environment
    set_environment_variables
    set_mailtrap_config
    set_whatsapp_config
    set_openrouter_config
    set_payment_config
    set_storage_config
    
    # Deploy and setup
    deploy_application
    run_migrations
    seed_database
    optimize_application
    setup_queue_worker
    
    # Test and finalize
    test_deployment
    show_deployment_info
    
    print_success "Deployment completed successfully! 🎉"
}

# Handle script arguments
case "${1:-}" in
    --help|-h)
        echo "WhatsML Railway Deployment Script"
        echo ""
        echo "Usage: $0 [options]"
        echo ""
        echo "Options:"
        echo "  --help, -h     Show this help message"
        echo "  --test         Test deployment only"
        echo "  --migrate      Run migrations only"
        echo "  --optimize      Optimize application only"
        echo ""
        echo "Environment Variables:"
        echo "  MAILTRAP_USERNAME              Mailtrap username (already configured: 332e1bc337992f)"
        echo "  OPENROUTER_API_KEY              OpenRouter API key (already configured)"
        echo "  WHATSAPP_CLOUD_API_TOKEN        WhatsApp Cloud API token"
        echo "  WHATSAPP_CLOUD_API_PHONE_NUMBER_ID  WhatsApp phone number ID"
        echo "  WHATSAPP_CLOUD_API_VERIFY_TOKEN      WhatsApp verify token"
        echo "  STRIPE_KEY                    Stripe public key"
        echo "  STRIPE_SECRET                  Stripe secret key"
        echo "  PAYPAL_CLIENT_ID              PayPal client ID"
        echo "  PAYPAL_CLIENT_SECRET          PayPal client secret"
        echo "  MOLLIE_KEY                    Mollie API key"
        echo "  CLOUDINARY_CLOUD_NAME         Cloudinary cloud name"
        echo "  CLOUDINARY_API_KEY            Cloudinary API key"
        echo "  CLOUDINARY_API_SECRET         Cloudinary API secret"
        echo "  MAIL_FROM_ADDRESS             Email from address"
        echo ""
        exit 0
        ;;
    --test)
        test_deployment
        exit 0
        ;;
    --migrate)
        run_migrations
        exit 0
        ;;
    --optimize)
        optimize_application
        exit 0
        ;;
    *)
        main
        ;;
esac
