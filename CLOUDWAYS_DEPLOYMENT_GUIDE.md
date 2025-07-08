# Cloudways Deployment Guide - Laravel+Vue POS Application

## Prerequisites

- Cloudways account
- Git reposit# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Clean seed data if needed (keep only users)
# php artisan db:clean-seed-data --force

# Seed database if needed (uncomment if required)
# php artisan db:seed --forceitHub, GitLab, or Bitbucket)
- SSH access to your Cloudways server
- Domain name (optional but recommended)

## Step 1: Prepare Your Application for Production

### 1.1 Create Production Environment File

Create a `.env.production` file in your project root:

```bash
# Copy your local .env file
cp .env .env.production
```

Edit `.env.production` with production settings:

```env
APP_NAME="TS POS System"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 1.2 Create Deployment Script

Create `deploy.sh` in your project root:

```bash
#!/bin/bash

echo "🚀 Starting deployment..."

# Pull latest code
echo "📦 Pulling latest code..."
git pull origin main

# Install PHP dependencies
echo "🔧 Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm ci

# Build frontend assets
echo "🏗️ Building frontend assets..."
npm run build

# Clear and cache Laravel configurations
echo "⚡ Optimizing Laravel..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Seed database if needed (comment out if not needed)
# php artisan db:seed --force

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs

echo "✅ Deployment completed successfully!"
```

Make the script executable:

```bash
chmod +x deploy.sh
```

### 1.3 Create .gitignore for Production

Ensure your `.gitignore` includes:

```gitignore
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.env.production
.phpunit.result.cache
docker-compose.override.yml
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
/.idea
/.vscode
/public/build/manifest.json
/public/build/assets/*
```

### 1.4 Commit Changes to Git

```bash
# Add deployment files
git add deploy.sh .env.production .gitignore

# Commit changes
git commit -m "Add deployment configuration"

# Push to repository
git push origin main
```

## Step 2: Set Up Cloudways Server

### 2.1 Create New Application

1. Log into your Cloudways account
2. Click "Add Server" or "Launch Now"
3. Choose your cloud provider (DigitalOcean, AWS, etc.)
4. Select server size (minimum 1GB RAM recommended)
5. Choose server location
6. Select PHP version (8.1 or higher)
7. Click "Launch Now"

### 2.2 Create Application

1. After server is ready, click "Add Application"
2. Choose "PHP" as application type
3. Enter application name: `ts-pos-v1`
4. Select your server
5. Click "Add Application"

## Step 3: Configure Server Settings

### 3.1 Access Server via SSH

Get SSH credentials from Cloudways:
1. Go to Server Management → Access Details
2. Copy SSH credentials

Connect to server:

```bash
ssh username@server-ip-address
```

### 3.2 Navigate to Application Directory

```bash
cd applications/app-name/public_html
```

### 3.3 Clone Your Repository

```bash
# Remove default files
rm -rf *
rm -rf .*

# Clone your repository
git clone https://github.com/yourusername/your-repo-name.git .

# Or if using SSH
git clone git@github.com:yourusername/your-repo-name.git .
```

## Step 4: Configure Database

### 4.1 Create Database

1. In Cloudways panel, go to Application Management
2. Click on your application
3. Go to "Database" tab
4. Note the database credentials

### 4.2 Import Database (if needed)

If you have existing data:

```bash
# Export from local (run on local machine)
mysqldump -u root -p ts_pos_db > ts_pos_backup.sql

# Upload to server (use SFTP or scp)
scp ts_pos_backup.sql username@server-ip:/home/username/

# Import on server
mysql -u db_user -p database_name < ts_pos_backup.sql
```

## Step 5: Configure Application Environment

### 5.1 Create Production Environment File

```bash
# Copy production environment
cp .env.production .env

# Edit environment file
nano .env
```

Update the following in `.env`:

```env
APP_URL=https://yourdomain.com
APP_KEY=base64:YOUR_GENERATED_KEY

DB_HOST=localhost
DB_DATABASE=your_cloudways_db_name
DB_USERNAME=your_cloudways_db_user
DB_PASSWORD=your_cloudways_db_password
```

### 5.2 Generate Application Key

```bash
php artisan key:generate
```

## Step 6: Install Dependencies and Build

### 6.1 Install PHP Dependencies

```bash
composer install --optimize-autoloader --no-dev
```

### 6.2 Install Node.js Dependencies

Check if Node.js is installed:

```bash
node --version
npm --version
```

If not installed, install via Cloudways panel:
1. Go to Application Management
2. Click on your application
3. Go to "Application Settings"
4. Enable Node.js

Then install dependencies:

```bash
npm ci
```

### 6.3 Build Frontend Assets

```bash
npm run build
```

## Step 7: Configure Laravel

### 7.1 Set Directory Permissions

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs
chown -R www-data:www-data storage bootstrap/cache
```

### 7.2 Run Database Migrations

```bash
php artisan migrate --force
```

### 7.3 Seed Database (if needed)

```bash
php artisan db:seed --force
```

### 7.4 Cache Configuration

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Step 8: Configure Web Server

### 8.1 Set Document Root

In Cloudways panel:
1. Go to Application Management
2. Click on your application
3. Go to "Application Settings"
4. Set "Webroot" to: `public`

### 8.2 Configure PHP Settings

In Application Settings:
1. Set PHP version to 8.1+
2. Increase memory limit to 512M
3. Set max execution time to 300 seconds
4. Enable required extensions:
   - mbstring
   - openssl
   - pdo_mysql
   - tokenizer
   - xml
   - ctype
   - json
   - bcmath
   - fileinfo

## Step 9: Set Up SSL Certificate

### 9.1 Add Domain

1. Go to Domain Management
2. Click "Add Domain"
3. Enter your domain name
4. Point to your application

### 9.2 Install SSL Certificate

1. In Domain Management, click on your domain
2. Click "Install SSL Certificate"
3. Choose "Let's Encrypt SSL" (free)
4. Click "Install Certificate"

## Step 10: Configure Scheduled Tasks (Cron Jobs)

### 10.1 Set Up Laravel Scheduler

In Cloudways panel:
1. Go to Server Management
2. Click "Cron Job Management"
3. Add new cron job:

```bash
# Command
cd /home/app-id/public_html && php artisan schedule:run

# Schedule: Every minute
* * * * *
```

## Step 11: Final Testing

### 11.1 Test Application

Visit your domain and test:
- Login functionality
- Collections page
- POS system
- Database operations

### 11.2 Check Logs

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check web server logs
tail -f /var/log/apache2/error.log
```

## Step 12: Set Up Continuous Deployment

### 12.1 Create Webhook (Optional)

1. In Cloudways panel, go to Git Deployment
2. Enable Git Deployment
3. Add your repository URL
4. Set branch to `main`
5. Set deployment path to `/`
6. Add deployment script path: `deploy.sh`

### 12.2 Manual Deployment Command

For manual deployments, create an alias:

```bash
# Add to ~/.bashrc
echo 'alias deploy="cd /home/app-id/public_html && ./deploy.sh"' >> ~/.bashrc
source ~/.bashrc
```

## Troubleshooting

### Common Issues:

1. **Permission Errors**:
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

2. **Database Connection Issues**:
   - Check database credentials in `.env`
   - Verify database exists
   - Test connection: `php artisan tinker`

3. **Frontend Assets Not Loading**:
   ```bash
   npm run build
   php artisan view:clear
   ```

4. **500 Server Error**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   tail -f storage/logs/laravel.log
   ```

## Post-Deployment Checklist

- [ ] Application loads correctly
- [ ] Database connection working
- [ ] Login system functional
- [ ] Frontend assets loading
- [ ] SSL certificate installed
- [ ] Cron jobs configured
- [ ] Logs are accessible
- [ ] Backup strategy implemented

## Backup Strategy

### Daily Backups

Create backup script `backup.sh`:

```bash
#!/bin/bash

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/backups"
APP_DIR="/home/app-id/public_html"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u db_user -p'db_password' database_name > $BACKUP_DIR/database_$DATE.sql

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C $APP_DIR .

# Keep only last 7 days
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

Add to cron jobs:
```bash
0 2 * * * /path/to/backup.sh
```

This guide provides a complete deployment process for your Laravel+Vue POS application on Cloudways. Follow each step carefully and test thoroughly after deployment.
