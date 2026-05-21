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

## Optimisations par fonctionnalité (audit 2026-05)

### CMS — pages et sections

| Zone | Comportement actuel | Piste |
|------|---------------------|-------|
| `PageRenderer` | `SectionLazyGate` : montage différé (`IntersectionObserver`, `rootMargin` ~320/480px) | **Implémenté** — 2 premières sections + ancre URL + édition en eager |
| `SectionRenderer` | Import dynamique du template (OK) | — |
| `SectionTextRead` + krefs | TipTap readonly (`RichTextReadonlyView`) — **conservé** (rendu identique aux krefs inline) | Pas de remplacement par `v-html` : TipTap assure le rendu `referenceInline` |
| `SectionEntityTableRead` | TanStack + API `api.tables.*` ; virtualisation si ≥ 40 lignes (CMS uniquement) | **Implémenté** — seuil Index inchangé (500+) |
| `PageController::show` | Sections + `media` en une requête | OK ; éviter payloads JSON énormes dans `section.data` |

### Kref — survol / popover

| Type | Chargement réseau | Statut |
|------|------------------|--------|
| **Section** (`pageSection`) | `RichTextKrefInteractions` : délai 380 ms puis `GET` snippet | OK (à la demande) |
| **Entité** | `KrefEntityTooltipBody` via `OverlayTrigger` lazy → fetch au premier survol + cache | OK |
| **Caractéristique** (TipTap) | Avant : `watch` `immediate` → API dès le rendu de chaque kref | **Corrigé** : `GET reference-table` au `@open` du `Tooltip` |
| **Page** | Texte statique dans le popover | OK |

Fichiers : `ReferenceInlineNodeView.vue`, `RichTextKrefInteractions.vue`, `kref*PreviewCache.js`.

### Caractéristiques (hors kref)

- Métadonnées : Pinia `fetchOnce()` → `/api/characteristics` (plus dans Inertia).
- Affichage entité : `resolveDef` / store synchrone ; éviter N appels API dans les grilles.

### Entités — vues minimal / line

- Données en général déjà dans la ligne API table (`format=entities`).
- `SpellViewMinimal` : `provideCharacteristicRuntime`, effets résolus côté cellule — coût CPU, pas de fetch extra si `tableMeta` fourni.
- Piste : virtualisation TanStack (déjà partiel) ; différer `EntityActions` / PDF hors viewport.

### Fichiers CMS perf

- `resources/js/Pages/Organismes/section/SectionLazyGate.vue`
- `resources/js/Pages/Organismes/section/PageRenderer.vue`

### Prochaines pistes

- Test Vitest : pas de fetch carac. kref sans `@open` du `Tooltip`

## Hors scope (plans séparés)

Tables TanStack avancées, fiches entité full, scrapping, GlobalSearch, CharacteristicGetterService.

## Références

- [Upgrade Laravel 13](../100-%20Done/UPGRADE_LARAVEL_13_PHPUNIT_PAO_INERTIA.md)
- [Inertia deferred props](https://inertiajs.com/deferred-props)
- [TECHNOLOGIES.md](../00-Project/TECHNOLOGIES.md)
