# 🚀 Railway Production Deployment & Installation Guide

## Current Status

### ✅ **Railway Deployment Status**
- **URL**: [https://whatsml-production-d457.up.railway.app](https://whatsml-production-d457.up.railway.app)
- **Health Check**: ✅ Healthy (`/health` endpoint responding)
- **Environment**: Production
- **Database**: SQLite (configured in deploy script)
- **PHP Version**: 8.3.15

### 🔧 **Installation Requirements Met**
Based on the web installer at [https://whatsml-production-d457.up.railway.app/install](https://whatsml-production-d457.up.railway.app/install), the following PHP extensions are required:

| Extension  | Status | Required |
|------------|--------|----------|
| PHP >= 8.1 | ✅ 8.3.15 | ✅ |
| mbstring   | ✅ | ✅ |
| bcmath     | ✅ | ✅ |
| ctype      | ✅ | ✅ |
| json       | ✅ | ✅ |
| openssl    | ✅ | ✅ |
| pdo        | ✅ | ✅ |
| tokenizer  | ✅ | ✅ |
| xml        | ✅ | ✅ |

## 🛠️ **Files Created/Modified for Installation**

### 1. **InstallerController** (`app/Http/Controllers/InstallerController.php`)
- Handles installation steps: requirements check, verification, database setup
- Creates the `public/uploads/installed` file when complete
- Runs migrations and creates storage links

### 2. **Installer Routes** (`routes/web.php`)
- Added installer routes under `/install` prefix
- Handles all installation steps via AJAX

### 3. **Installer View** (`resources/views/installer/index.blade.php`)
- Modern, responsive installer interface
- Step-by-step installation process
- Real-time requirement checking
- Progress tracking

### 4. **Deployment Script** (`deploy-post.sh`)
- Enhanced to create uploads directory
- Sets proper permissions for installer

## 🚀 **Deployment Instructions**

### Step 1: Deploy Changes to Railway
```bash
# Commit all changes
git add .
git commit -m "Add web installer for Railway deployment"
git push origin main
```

### Step 2: Monitor Deployment
- Railway will automatically deploy the changes
- Monitor the deployment logs in Railway dashboard
- Wait for health check to pass

### Step 3: Complete Installation
1. Visit: [https://whatsml-production-d457.up.railway.app/install](https://whatsml-production-d457.up.railway.app/install)
2. Follow the installation wizard:
   - **Step 1**: Requirements check (should all pass)
   - **Step 2**: System verification
   - **Step 3**: Database setup (runs migrations)
   - **Step 4**: Complete installation

### Step 4: Verify Installation
After installation completes:
- Visit: [https://whatsml-production-d457.up.railway.app](https://whatsml-production-d457.up.railway.app)
- Should redirect to the main application (no longer to installer)

## 🔍 **Troubleshooting**

### If Installation Fails:

1. **Check Railway Logs**:
   ```bash
   # In Railway dashboard, check deployment logs
   ```

2. **Manual Database Setup**:
   ```bash
   # SSH into Railway container (if available)
   php artisan migrate --force
   php artisan storage:link
   ```

3. **Create Installed File Manually**:
   ```bash
   # Create the installed marker file
   touch public/uploads/installed
   ```

### If Requirements Check Fails:
- All requirements should pass on Railway (PHP 8.3.15 with all extensions)
- If any fail, check Railway environment configuration

### If Database Setup Fails:
- SQLite database is configured in `deploy-post.sh`
- Check file permissions on `database/database.sqlite`
- Verify migrations can run

## 📊 **Production Environment Configuration**

### Current Railway Configuration:
- **Database**: SQLite (`database/database.sqlite`)
- **Sessions**: File-based
- **Cache**: File-based
- **Queue**: Synchronous
- **Storage**: Local file system

### Environment Variables Set:
- `APP_NAME`: WhatsML
- `APP_ENV`: production
- `APP_DEBUG`: false
- `APP_URL`: https://whatsml-production-d457.up.railway.app
- `DB_CONNECTION`: sqlite
- `DB_DATABASE`: database/database.sqlite

## 🎯 **Next Steps After Installation**

1. **Configure Admin User**:
   - Set up initial admin account
   - Configure user permissions

2. **Configure Modules**:
   - Enable/configure WhatsApp modules
   - Set up API keys for external services

3. **Configure Email**:
   - Set up SMTP settings
   - Test contact form functionality

4. **Configure Storage**:
   - Set up file uploads
   - Configure media storage

5. **Security Setup**:
   - Configure SSL/TLS
   - Set up proper authentication

## 📝 **Installation Checklist**

- [ ] Deploy changes to Railway
- [ ] Monitor deployment success
- [ ] Access installer at `/install`
- [ ] Complete requirements check
- [ ] Complete system verification
- [ ] Complete database setup
- [ ] Verify installation completion
- [ ] Test main application access
- [ ] Configure admin user
- [ ] Test core functionality

## 🆘 **Support**

If you encounter issues:
1. Check Railway deployment logs
2. Verify all requirements are met
3. Check database connectivity
4. Review installer error messages
5. Use the debugging tools created earlier

---

**Last Updated**: October 27, 2025  
**Railway URL**: https://whatsml-production-d457.up.railway.app  
**Installer URL**: https://whatsml-production-d457.up.railway.app/install
