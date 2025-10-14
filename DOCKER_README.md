# Laravel Docker Configuration with Podman

Dokumen ini menjelaskan cara mengatur dan menjalankan aplikasi Laravel SIPO ICBP menggunakan Docker/Podman dengan database MariaDB untuk lingkungan production.

## Persyaratan

- Podman (atau Docker)
- Podman Compose (atau Docker Compose)
- Git

## Struktur Direktori

```
sipo-icbp/
├── docker/
│   ├── common/
│   │   └── php-fpm/
│   │       └── Dockerfile
│   └── production/
│       ├── nginx/
│       │   ├── Dockerfile
│       │   ├── nginx.conf
│       │   └── default.conf
│       └── php-fpm/
│           └── entrypoint.sh
├── compose.prod.yaml
├── .env.production
├── .dockerignore
├── docker-deploy.sh
└── DOCKER_README.md
```

## Konfigurasi Environment

1. Salin file environment:
   ```bash
   cp .env.example .env.production
   ```

2. Edit file `.env.production` sesuai kebutuhan:
   - `DB_DATABASE`: Nama database
   - `DB_USERNAME`: Username database
   - `DB_PASSWORD`: Password database
   - `DB_ROOT_PASSWORD`: Password root MariaDB
   - `APP_KEY`: Akan digenerate otomatis oleh skrip deploy

## Deployment

### Cara Cepat (Direkomendasikan)

Jalankan skrip deployment otomatis:

```bash
./docker-deploy.sh
```

### Manual

1. Generate APP_KEY:
   ```bash
   php artisan key:generate --env=production
   ```

2. Build dan jalankan containers:
   ```bash
   podman-compose -f compose.prod.yaml up --build -d
   ```

3. Tunggu beberapa saat hingga semua service siap.

## Aplikasi

- **URL Aplikasi**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - Server: mariadb
  - Username: laravel
  - Password: (sesuai .env.production)

## Service yang Dijalankan

1. **Nginx** (Port 8080): Web server
2. **PHP-FPM** (Internal): PHP processing
3. **PHP-CLI**: Command line interface untuk artisan commands
4. **MariaDB** (Port 3306): Database server
5. **Redis** (Internal): Cache dan session storage
6. **phpMyAdmin** (Port 8081): Database management interface

## Perintah Berguna

### Melihat Logs
```bash
# Semua logs
podman-compose -f compose.prod.yaml logs -f

# Logs service tertentu
podman-compose -f compose.prod.yaml logs -f php-fpm
podman-compose -f compose.prod.yaml logs -f mariadb
podman-compose -f compose.prod.yaml logs -f nginx
```

### Menghentikan Services
```bash
podman-compose -f compose.prod.yaml down
```

### Me-restart Services
```bash
# Semua services
podman-compose -f compose.prod.yaml restart

# Service tertentu
podman-compose -f compose.prod.yaml restart php-fpm
```

### Menjalankan Artisan Commands
```bash
# Contoh: menjalankan migrasi manual
podman-compose -f compose.prod.yaml exec php-cli php artisan migrate

# Contoh: membuat user baru
podman-compose -f compose.prod.yaml exec php-cli php artisan tinker
```

### Backup Database
```bash
podman-compose -f compose.prod.yaml exec mariadb mysqldump -u laravel -p sipo_icbp > backup.sql
```

### Restore Database
```bash
podman-compose -f compose.prod.yaml exec -T mariadb mysql -u laravel -p sipo_icbp < backup.sql
```

## Volumes

- **laravel-storage-production**: Storage Laravel (uploads, cache, logs)
- **mariadb-data-production**: Data database MariaDB
- **redis-data-production**: Data Redis cache

## Troubleshooting

### Database Connection Error
Jika terjadi error koneksi database:
1. Pastikan service mariadb sudah running:
   ```bash
   podman-compose -f compose.prod.yaml ps mariadb
   ```
2. Cek logs mariadb:
   ```bash
   podman-compose -f compose.prod.yaml logs mariadb
   ```
3. Restart service:
   ```bash
   podman-compose -f compose.prod.yaml restart mariadb
   ```

### Permission Issues
Jika terjadi permission error pada storage:
```bash
podman-compose -f compose.prod.yaml exec php-fpm chown -R www-data:www-data /var/www/storage
```

### Application Not Loading
1. Cek status semua services:
   ```bash
   podman-compose -f compose.prod.yaml ps
   ```
2. Restart semua services:
   ```bash
   podman-compose -f compose.prod.yaml restart
   ```

## Security Notes

- Pastikan password database yang kuat
- Jangan expose database port ke public di production
- Regular backup database dan storage
- Monitor logs untuk aktivitas mencurigakan

## Development vs Production

Untuk development, gunakan konfigurasi terpisah:
- File: `compose.dev.yaml` (belum dibuat)
- Environment: `.env`
- Port yang berbeda untuk menghindari konflik

## Update Application

1. Pull latest code:
   ```bash
   git pull origin main
   ```

2. Rebuild dan restart:
   ```bash
   podman-compose -f compose.prod.yaml up --build -d
   ```

3. Run migrations jika ada:
   ```bash
   podman-compose -f compose.prod.yaml exec php-cli php artisan migrate --force
   ```

## Monitoring

### Health Check
Semua service memiliki health check yang bisa dimonitor:
```bash
podman-compose -f compose.prod.yaml ps
```

### Resource Usage
```bash
podman stats
```

## Support

Jika mengalami masalah:
1. Cek troubleshoot section di atas
2. Periksa logs untuk error details
3. Pastikan semua persyaratan terpenuhi
4. Coba restart services
