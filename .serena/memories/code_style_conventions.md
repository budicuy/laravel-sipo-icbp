# Code Style & Conventions

## Naming Conventions
- **Models**: Singular, PascalCase (e.g., `User`, `Karyawan`, `Pasien`, `RekamMedis`)
- **Controllers**: PascalCase with `Controller` suffix
- **Migrations**: Timestamp prefix + descriptive name (e.g., `2025_10_04_060801_create_departemen_table.php`)
- **Seeders**: Entity name + `Seeder` suffix (e.g., `DepartemenSeeder`, `DiagnosaSeeder`)
- **Views**: Blade templates with `.blade.php` extension in `resources/views`

## Architecture
- **MVC Pattern**: 
  - Models in `app/Models`
  - Controllers in `app/Http/Controllers`
  - Views in `resources/views`
- **Database**: 
  - Migrations in `database/migrations`
  - Seeders in `database/seeders`
  - Factories in `database/factories`
- **Routing**: 
  - Web routes in `routes/web.php`
  - Console routes in `routes/console.php`

## Laravel Conventions
- Follow standard Laravel conventions for all code
- Use Eloquent ORM for database operations
- Use Blade templating for views
- PSR-4 autoloading for App namespace
