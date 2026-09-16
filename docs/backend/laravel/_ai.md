# Laravel — IA

> Structure backend classique, pas de `app/Modules`.

## Fichiers pivots

- `bootstrap/app.php` — middlewares et routing.
- `app/Providers/AppServiceProvider.php` — singletons et boot.
- `app/Http/Middleware/HandleInertiaRequests.php` — props partagées.
- `app/Services/` — logique métier.
- `app/Services/ImageService.php` — miniatures (Intervention Image 4, driver Imagick).
- Tests : PHPUnit 13 (`php artisan test`, `phpunit.xml`). E2E Playwright (`pnpm test:e2e`), pas Cypress.
