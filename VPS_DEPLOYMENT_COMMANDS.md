# 🚀 VPS Deployment Commands for Enhanced Barcode Scanner

## 📋 Step-by-Step VPS Update Commands

### 1. **Connect to your VPS**
```bash
ssh your-username@your-vps-ip
# or
ssh -i your-key.pem your-username@your-vps-ip
```

### 2. **Navigate to your project directory**
```bash
cd /var/www/html/your-project-name
# or wherever your Laravel project is located
# Common paths: /var/www/html, /home/user/public_html, /opt/your-project
```

### 3. **Backup current version (IMPORTANT!)**
```bash
# Create a backup of current code
sudo cp -r . ../backup-$(date +%Y%m%d-%H%M%S)
# or create a git commit if using git
git add .
git commit -m "Backup before barcode scanner update"
```

### 4. **Update the code** (Choose ONE method)

#### Option A: If using Git (RECOMMENDED)
```bash
# Pull latest changes
git pull origin main
# or
git pull origin master
```

#### Option B: If uploading files manually
```bash
# Upload your files via SCP from your local machine
# Run this from your LOCAL machine (Windows):
scp -r resources/js/components/pos/AdvancedPOS.vue your-username@your-vps-ip:/var/www/html/your-project/resources/js/components/pos/
```

### 5. **Install/Update Node.js dependencies**
```bash
# Install or update npm packages
npm install
# or if you have package-lock.json conflicts
npm ci
```

### 6. **Install/Update PHP dependencies**
```bash
# Update Composer dependencies
composer install --optimize-autoloader --no-dev
# or if you need to update
composer update
```

### 7. **Build the frontend assets**
```bash
# Build production assets
npm run build
# or
npm run production
```

### 8. **Clear Laravel caches**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 9. **Set proper permissions**
```bash
# Set correct ownership (replace 'www-data' with your web server user)
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

### 10. **Restart services** (if needed)
```bash
# Restart web server (choose your server)
sudo systemctl restart nginx
# or
sudo systemctl restart apache2

# Restart PHP-FPM (if using)
sudo systemctl restart php8.1-fpm
# or
sudo systemctl restart php8.2-fpm
```

---

## 🔧 **Complete Update Script** (Copy and paste all at once)

```bash
#!/bin/bash
# Enhanced Barcode Scanner VPS Update Script

echo "🚀 Starting VPS update for Enhanced Barcode Scanner..."

# Navigate to project (UPDATE THIS PATH!)
cd /var/www/html/your-project-name

# Backup current version
echo "📦 Creating backup..."
sudo cp -r . ../backup-$(date +%Y%m%d-%H%M%S)

# Pull latest code (if using Git)
echo "📥 Pulling latest code..."
git pull origin main

# Install dependencies
echo "📚 Installing dependencies..."
npm ci
composer install --optimize-autoloader --no-dev

# Build assets
echo "🔨 Building frontend assets..."
npm run build

# Clear caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
echo "🔐 Setting permissions..."
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache

# Restart services
echo "🔄 Restarting services..."
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm

echo "✅ Update completed successfully!"
echo "🎉 Enhanced Barcode Scanner is now live!"
```

---

## 📝 **Manual File Upload Method** (Alternative)

If you prefer to upload files manually:

### From your Windows machine:
```powershell
# Using SCP to upload the main component
scp resources/js/components/pos/AdvancedPOS.vue your-username@your-vps-ip:/var/www/html/your-project/resources/js/components/pos/

# Upload package.json if dependencies changed
scp package.json your-username@your-vps-ip:/var/www/html/your-project/

# Upload any other changed files
scp BARCODE_SCANNER_ENHANCED.md your-username@your-vps-ip:/var/www/html/your-project/
```

### Then on VPS:
```bash
# Install dependencies and build
npm install
npm run build

# Clear caches and restart
php artisan cache:clear
php artisan config:cache
sudo systemctl restart nginx
```

---

## 🚨 **IMPORTANT Notes:**

### **Before Running Commands:**
1. **Replace `your-username`** with your actual VPS username
2. **Replace `your-vps-ip`** with your actual VPS IP address
3. **Replace `/var/www/html/your-project-name`** with your actual project path
4. **Replace `www-data`** with your web server user (could be `nginx`, `apache`, or `ubuntu`)
5. **Replace `php8.1-fpm`** with your PHP version (could be `php8.0-fpm`, `php8.2-fpm`, etc.)

### **Check Your Setup:**
```bash
# Check PHP version
php -v

# Check web server
sudo systemctl status nginx
# or
sudo systemctl status apache2

# Check Node.js version
node -v
npm -v
```

### **If you encounter permission errors:**
```bash
# Run commands with sudo if needed
sudo npm install
sudo npm run build
sudo php artisan cache:clear
```

---

## ✅ **Verification Steps:**

After deployment, verify everything works:

1. **Visit your POS page:** `https://your-domain.com/pos`
2. **Test the scanner:** Click the camera button
3. **Check browser console:** No JavaScript errors
4. **Test on mobile:** Scanner should work on smartphones
5. **Check server logs:** `tail -f storage/logs/laravel.log`

---

## 🔧 **Troubleshooting:**

### If npm build fails:
```bash
# Clear npm cache
npm cache clean --force
rm -rf node_modules package-lock.json
npm install
npm run build
```

### If permissions are wrong:
```bash
# Fix Laravel permissions
sudo chmod -R 755 /var/www/html/your-project
sudo chmod -R 775 /var/www/html/your-project/storage
sudo chmod -R 775 /var/www/html/your-project/bootstrap/cache
sudo chown -R www-data:www-data /var/www/html/your-project
```

### If services won't restart:
```bash
# Check service status
sudo systemctl status nginx
sudo systemctl status php8.1-fpm

# Check logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/php8.1-fpm.log
```

---

**🎉 Your enhanced barcode scanner will be live after running these commands!**
