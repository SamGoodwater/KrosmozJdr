# Routes — IA

> Organisation des routes Laravel.

## Fichiers pivots

- `routes/web.php`
- `routes/api.php`
- `routes/auth.php`
- `routes/web/page.php`
- `routes/api/scrapping.php` — API atelier `/api/dofusdb` (redirect 307 depuis `/api/scrapping`).
- `routes/api/types.php` — registres races / types de sorts (`role:admin`).
- `routes/api/cms.php`
- `routes/entities/*.php`
- `routes/admin/*.php` — back-office ; référentiels contenu sous `/admin/content/{characteristics,effects,languages,…}` (anciennes URLs `/admin/…` redirigent).
