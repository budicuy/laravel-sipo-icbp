# Copilot Instructions for AI Agents

## Project Overview
This is a Laravel-based web application for managing medical records, patients, employees, medicines, and related entities. The codebase follows standard Laravel conventions but includes custom domain models and workflows for a clinical/medical context.

## Architecture & Key Components
- **MVC Structure**: Controllers in `app/Http/Controllers`, models in `app/Models`, and Blade views in `resources/views`.
- **Database**: Migrations and seeders are in `database/migrations` and `database/seeders`. The SQLite database is at `database/database.sqlite`.
- **Routing**: Main routes are defined in `routes/web.php` (web) and `routes/console.php` (CLI commands).
- **Assets**: Frontend assets (CSS/JS) are in `resources/css` and `resources/js`, built with Vite (`vite.config.js`).

## Developer Workflows
- **Install dependencies**: `composer install` (PHP), `npm install` (JS)
- **Run local server**: `php artisan serve`
- **Run migrations**: `php artisan migrate`
- **Seed database**: `php artisan db:seed`
- **Run tests**: `php artisan test` or `vendor\bin\pest`
- **Build assets**: `npm run build` (or `npm run dev` for hot reload)

## Project-Specific Conventions
- **Model Naming**: Singular, PascalCase (e.g., `User`, `Karyawan`, `Pasien`).
- **Migration Naming**: Timestamps and descriptive names (e.g., `2025_10_04_060801_create_departemen_table.php`).
- **Seeder Naming**: Entity-based (e.g., `DepartemenSeeder`).
- **Blade Views**: Use `.blade.php` extension, stored in `resources/views`.
- **Testing**: Uses Pest (`tests/Pest.php`) and PHPUnit (`phpunit.xml`).

## Integration & External Dependencies
- **Laravel Framework**: Core backend logic and routing.
- **Vite**: Asset bundling and hot reload for frontend.
- **Pest**: Modern PHP testing framework.
- **Composer**: PHP dependency management.
- **NPM**: JS dependency management.

## Examples
- To add a new entity (e.g., `Poli`):
  1. Create a migration in `database/migrations`.
  2. Add a model in `app/Models`.
  3. Add a controller in `app/Http/Controllers`.
  4. Register routes in `routes/web.php`.
  5. Create Blade views in `resources/views`.

## References
- `README.md`: General project info
- `phpunit.xml`: Test configuration
- `vite.config.js`: Asset build config
- `artisan`: Laravel CLI entry point

---

**When in doubt, follow Laravel conventions unless a project-specific pattern is documented above.**
