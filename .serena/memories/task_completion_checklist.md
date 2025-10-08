# Task Completion Checklist

When completing a task in this Laravel project, ensure you:

## Code Quality
1. **Format code**: Run `./vendor/bin/pint` to ensure PSR-12 compliance
2. **Clear caches** if config/routes changed:
   - `php artisan config:clear`
   - `php artisan route:clear`
   - `php artisan view:clear`

## Testing
3. **Run tests**: Execute `php artisan test` or `vendor/bin/pest` to ensure nothing is broken
4. **Manual testing**: Test the feature in browser if UI changes were made

## Database Changes
5. **Run migrations** if database schema changed: `php artisan migrate`
6. **Update seeders** if seed data needs modification
7. **Test with fresh database**: `php artisan migrate:fresh --seed`

## Frontend Changes
8. **Build assets** if JS/CSS changed: `npm run build` (or use `npm run dev` during development)
9. **Check browser console** for any JavaScript errors

## Documentation
10. **Update comments** and docblocks where necessary
11. **Update README** if new features or setup steps are added

## Version Control
12. **Review changes**: `git diff` to see what was modified
13. **Stage and commit**: 
    - `git add .`
    - `git commit -m "descriptive message"`
