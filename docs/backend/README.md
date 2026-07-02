# Backend

Le backend est un monolithe Laravel 12 sous `app/`, sans architecture `app/Modules`. Il expose les pages Inertia, les APIs sessionnées et les commandes de maintenance.

## Sous-domaines

- [Laravel](laravel/README.md) : structure applicative, providers, middlewares, services.
- [Database](database/README.md) : migrations, modèles, seeders, schéma.
- [Routes](routes/README.md) : organisation web/API/admin/entities.

## Fichiers pivots

- `bootstrap/app.php`
- `app/Providers/AppServiceProvider.php`
- `app/Http/Middleware/HandleInertiaRequests.php`
- `routes/web.php`, `routes/api.php`
- `app/Models/`, `app/Services/`, `app/Policies/`
