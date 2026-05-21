# Montée de version Laravel 13, PHPUnit 12, PAO et Inertia

**Date** : 2026-05-21

## Résumé

Migration du stack backend et client Inertia vers les versions courantes, avec activation de Laravel PAO pour les workflows agent (Cursor).

## Versions

| Composant | Avant | Après |
|-----------|-------|-------|
| Laravel | 12.59 | **13.11** |
| PHPUnit | 11.5 | **12.5** |
| Laravel PAO | — | **1.0** (dev) |
| inertia-laravel | 2.0 | **3.1** |
| @inertiajs/vue3 | 1.3 | **2.3** |
| laravel/tinker | 2.x | **3.0** |
| barryvdh/laravel-debugbar | 3.x | **4.x** |

## Changements notables

- Middleware CSRF renommé : `VerifyCsrfToken` → `PreventRequestForgery` (Laravel 13).
- `config/inertia.php` publié : chemin des pages `resources/js/Pages`, SSR désactivé par défaut.
- `config/cache.php` : option `serializable_classes` (L13) — le cache ne doit stocker que tableaux/scalaires, pas de modèles Eloquent.
- CI : suppression de `--no-interaction` sur `php artisan test` (PHPUnit 12).

## Validation

- `php artisan about` — Laravel 13.11.2
- PHPStan — OK
- Build Vite — OK
- Tests ciblés Inertia/auth — OK (MySQL requis pour la suite complète)
