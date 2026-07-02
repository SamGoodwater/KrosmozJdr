# Optimisations performance globales (2026-05)

Réduction du coût fixe Inertia et du premier chargement SPA.

## Livré

- **Inertia** : `shareOnce` (permissions, ziggy, oauth) ; `defer` (notifications, DSR) ; eager load OAuth.
- **Ziggy** : suppression `@routes`, `config/ziggy.php` (sans `debugbar.*`), plugin `InertiaZiggyVue`.
- **Permissions invité** : cache `permissions.entities.guest.r{revision}`.
- **Characteristics** : hors payload Inertia → `GET /api/characteristics` + Pinia, TTL 3600 s.
- **Frontend** : polices Bunny allégées, chunks Vite, `cally` et templates CMS en chargement différé.
- **Doc** : [PERFORMANCE_GLOBAL.md](../40-DevGuides/PERFORMANCE_GLOBAL.md).

## Tests

- `tests/Feature/Http/HandleInertiaSharedPropsTest.php`
- `tests/Unit/Support/EntityPermissionServiceGuestCacheTest.php`
