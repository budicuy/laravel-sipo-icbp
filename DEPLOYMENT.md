# Deployment Guide - Digital Ocean

## 🚀 Pre-Deployment Checklist

### 1. Build Assets (PENTING!)
```bash
# Di local, compile assets untuk production
npm run build

# Commit hasil build ke git
git add public/build
git commit -m "Build assets for production"
git push
```

### 2. Environment Setup di Server

```bash
# SSH ke Digital Ocean server
ssh user@your-server-ip

# Navigate ke project directory
cd /var/www/sipo-icbp

# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# Copy environment file
cp .env.example .env

# Edit .env dengan credentials production
nano .env
```

### 3. Configure .env untuk Production

```env
APP_NAME="SIPO ICBP"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipoicbp
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Session & Cache
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# Asset URL (PENTING untuk styling!)
ASSET_URL=https://your-domain.com
```

### 4. Generate App Key & Setup Database

```bash
# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed database (optional, hanya untuk data awal)
php artisan db:seed --force

# Cache config untuk performa
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Set Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/sipo-icbp

# Set permissions
sudo chmod -R 755 /var/www/sipo-icbp
sudo chmod -R 775 /var/www/sipo-icbp/storage
sudo chmod -R 775 /var/www/sipo-icbp/bootstrap/cache
```

### 6. Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/sipo-icbp/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|gif|png|css|js|ico|xml|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 7. Restart Services

```bash
# Restart Nginx
sudo systemctl restart nginx

# Restart PHP-FPM
sudo systemctl restart php8.4-fpm
```

## 🔧 Troubleshooting

### Styling Tidak Muncul

**Penyebab:**
1. Assets belum di-build
2. File build tidak ter-commit ke git
3. APP_URL atau ASSET_URL salah

**Solusi:**
```bash
# Di local
npm run build
git add public/build
git commit -m "Add compiled assets"
git push

# Di server
cd /var/www/sipo-icbp
git pull origin main
php artisan config:clear
php artisan cache:clear
```

### Permission Errors

```bash
sudo chown -R www-data:www-data /var/www/sipo-icbp
sudo chmod -R 755 /var/www/sipo-icbp
sudo chmod -R 775 /var/www/sipo-icbp/storage
sudo chmod -R 775 /var/www/sipo-icbp/bootstrap/cache
```

### 500 Internal Server Error

```bash
# Check error logs
sudo tail -f /var/log/nginx/error.log

# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Re-cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Assets 404 Not Found

**Pastikan:**
1. Folder `public/build` ada dan berisi file
2. `.env` memiliki `ASSET_URL` yang benar
3. Nginx config mengarah ke folder `public`

```bash
# Cek apakah build folder ada
ls -la public/build/

# Should show:
# - manifest.json
# - assets/app-*.css
# - assets/app-*.js
```

## 📝 Update Workflow

Setiap kali ada perubahan code:

```bash
# Di local
npm run build
git add .
git commit -m "Update: description"
git push

# Di server
cd /var/www/sipo-icbp
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl restart php8.4-fpm
```

## 🔐 SSL Certificate (Recommended)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d your-domain.com

# Auto-renewal is setup automatically
```

## ✅ Verification Checklist

- [ ] Assets compiled (`npm run build`)
- [ ] `public/build` folder exists and committed
- [ ] `.env` configured correctly
- [ ] Database migrated
- [ ] Permissions set correctly
- [ ] Nginx configured
- [ ] PHP-FPM running
- [ ] Can access homepage
- [ ] CSS/JS loading correctly
- [ ] Alpine.js working
- [ ] Forms submitting correctly
- [ ] Images displaying

## 📞 Need Help?

Check Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

Check Nginx logs:
```bash
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
```
