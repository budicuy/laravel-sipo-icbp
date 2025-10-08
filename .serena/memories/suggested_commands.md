# Suggested Commands

## Development Setup
```bash
# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed
```

## Running the Application
```bash
# Start local development server
php artisan serve

# Build frontend assets (production)
npm run build

# Watch and hot-reload frontend assets (development)
npm run dev
```

## Testing
```bash
# Run all tests
php artisan test

# Run Pest tests directly
vendor/bin/pest

# Run specific test file
php artisan test --filter=TestName
```

## Code Quality
```bash
# Run Laravel Pint (code formatter)
./vendor/bin/pint

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Database Operations
```bash
# Fresh migration (drop all tables and re-migrate)
php artisan migrate:fresh

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Generate migrations from existing database
php artisan migrate:generate
```

## System Commands (Linux/Zsh)
- `ls -la` - List all files with details
- `cd <directory>` - Change directory
- `grep -r "pattern" .` - Search for pattern recursively
- `find . -name "*.php"` - Find PHP files
- `git status` - Check git status
- `git add .` - Stage all changes
- `git commit -m "message"` - Commit changes
