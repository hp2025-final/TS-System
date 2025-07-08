# Deployment Checklist

## Pre-Deployment
- [ ] Code is committed to main branch
- [ ] Database is backed up
- [ ] Environment variables are configured
- [ ] SSL certificate is ready
- [ ] Domain is pointed to server

## Deployment Steps
- [ ] SSH into Cloudways server
- [ ] Navigate to application directory
- [ ] Run deployment script: `./deploy.sh`
- [ ] Check application logs
- [ ] Test all functionality

## Post-Deployment
- [ ] Test login system
- [ ] Test POS functionality
- [ ] Test collections page
- [ ] Test database operations
- [ ] Verify SSL certificate
- [ ] Set up monitoring
- [ ] Configure backups

## Rollback Plan
- [ ] Keep previous version tagged
- [ ] Database backup available
- [ ] Quick rollback commands ready

## Commands Quick Reference

```bash
# SSH into server
ssh username@server-ip

# Navigate to app
cd applications/app-name/public_html

# Deploy
./deploy.sh

# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Database commands
php artisan migrate --force
php artisan db:seed --force
php artisan db:clean-seed-data --force  # Remove all seed data except users

# Permissions
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs

# Build assets
npm run build
```
