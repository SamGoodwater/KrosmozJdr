# Laravel — IA

> Structure backend classique, pas de `app/Modules`.

## Fichiers pivots

- `bootstrap/app.php` — middlewares et routing.
- `app/Providers/AppServiceProvider.php` — singletons et boot.
- `app/Http/Middleware/HandleInertiaRequests.php` — props partagées.
- `app/Services/` — logique métier.
