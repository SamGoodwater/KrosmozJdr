# Performance globale — Krosmoz-JDR

Guide transversal pour réduire le coût fixe de chaque navigation Inertia et du premier chargement SPA.

## Objectifs

| Métrique | Cible |
|----------|-------|
| Props Inertia partagées (navigation 2+) | −60 % min |
| Requêtes SQL fixes par hit | ≤ 5 |
| TTFB navigation SPA (local) | −30 % |
| Chunk `app-*.js` (gzip) | −15 à −25 % |

## Méthode de mesure

1. **Laravel Debugbar** (local, `APP_DEBUG=true`) : onglet Database → nombre de requêtes.
2. **Network** (Chrome / Cursor browser) : taille du document HTML + requête XHR Inertia (`X-Inertia`).
3. **Build** : `pnpm run build` → comparer `public/build/assets/app-*.js`.

### Scénarios de référence

| Scénario | URL | Contexte |
|----------|-----|----------|
| Page publique | `/pages/accueil` ou `/login` | Invité |
| Page connectée | `/user` ou page CMS | Auth |
| Navigation SPA | 2e clic menu Inertia | Même session |

### Tableau baseline / après

| Mesure | Baseline (2026-05-21) | Après optimisations |
|--------|----------------------|-------------------|
| SQL 1ère charge `/pages/accueil` | À mesurer | |
| SQL navigation Inertia #2 | À mesurer | |
| Taille JSON props partagées | À mesurer | |
| `app-*.js` gzip | ~158 KB | |

## Optimisations implémentées

### Phase 1 — Inertia `shareOnce` + `defer`

- [`HandleInertiaRequests`](../../app/Http/Middleware/HandleInertiaRequests.php) : `permissions`, `oauth_enabled_providers`, `ziggy` (routes) en `shareOnce` ; `notifications_unread_count`, `pending_erasure` en `defer` ; eager load `oauthAccounts`.
- Props lourdes non recalculées à chaque navigation SPA.

### Phase 2 — Ziggy

- Suppression de `@routes` dupliqué dans [`app.blade.php`](../../resources/views/app.blade.php).
- Config [`config/ziggy.php`](../../config/ziggy.php) : exclusion `debugbar.*`.
- Client : [`resources/js/Utils/ziggy-inertia.js`](../../resources/js/Utils/ziggy-inertia.js).

### Phase 3 — Permissions invité

- Cache `permissions.entities.guest.r{revision}` dans [`EntityPermissionService`](../../app/Support/EntityPermissions/EntityPermissionService.php).

### Phase 4 — Characteristics

- Chargement via `GET /api/characteristics` + Pinia ([`useCharacteristicsPiniaStore.js`](../../resources/js/Composables/store/useCharacteristicsPiniaStore.js)).
- TTL cache backend : 3600 s.

### Phase 5 — Shell frontend

- Polices Bunny réduites (400, 600, 700).
- `manualChunks` Vite (vendor, layout, formatters, cally).
- `cally` et `preloadCommonTemplates` chargés à la demande.

### Phase 6 — Infra production

- `.env.example` : commentaires Redis recommandé en prod pour `CACHE_STORE` / `SESSION_DRIVER`.
- `config/cache.php` : défaut `file` (aligné `.env.example`).
- Déploiement : `composer install --no-dev`, `php artisan optimize`, `config:cache`, `route:cache`, `php artisan ziggy:generate resources/js/ziggy.js` (sans Debugbar).

## Workflow Cursor

```bash
php artisan test tests/Feature/Http/HandleInertiaSharedPropsTest.php
composer phpstan
pnpm run build
composer dev:review:agent
```

## Hors scope (par fonctionnalité)

Tables TanStack, fiches entité full, scrapping, GlobalSearch, CharacteristicGetterService — plans séparés.

## Références

- [Upgrade Laravel 13](../100-%20Done/UPGRADE_LARAVEL_13_PHPUNIT_PAO_INERTIA.md)
- [Inertia deferred props](https://inertiajs.com/deferred-props)
- [TECHNOLOGIES.md](../00-Project/TECHNOLOGIES.md)
