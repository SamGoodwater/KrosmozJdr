# Backend — carte IA (degré 1) [STUB]

> Backend Laravel 12 / PHP 8.4, monolithe classique sous `app/` (pas de `app/Modules/`). Sert Inertia + une API « hybride » (session + CSRF).

> Statut : stub. Pointe vers la doc/code existants ; à remixer dans `docV2` lors d'une itération suivante.

## Quand lire ce nœud

- Travailler sur les modèles, contrôleurs, services, jobs, commandes, config Laravel.

## Structure (réelle)

| Dossier | Rôle |
| --- | --- |
| `app/Models/` | Modèles Eloquent (`User`, `Page`, `Section`, `Entity/*`, `Type/*`, `Scrapping/*`, `Effect*`, `Characteristic*`). |
| `app/Http/Controllers/` | `Entity/`, `Admin/`, `Auth/`, `Api/`, `Scrapping/`, `Type/`. |
| `app/Http/Requests/` | Validation par action (Form Requests). |
| `app/Http/Resources/` | Transformation JSON (Inertia/API). |
| `app/Http/Middleware/` | `HandleInertiaRequests`, `CheckRole`, `EnsureAdminAreaAccess`, `RequirePasswordWithInactivity`, … |
| `app/Policies/` | Autorisation (`Entity/BaseEntityPolicy`, `UserPolicy`, `PagePolicy`…). |
| `app/Services/` | Logique métier (`Scrapping/`, `Characteristic/`, `Effect/`, `Entity/`, `Media/`, `Privacy/`…). |
| `app/Jobs/`, `app/Console/Commands/` | Files d'attente et CLI (`project:*`, `scrapping:*`, `pages:*`). |
| `routes/` | Modulaire : `web.php`/`api.php` incluent `routes/{web,api,entities,admin}/*.php`. |
| `config/` | `scrapping.php`, `entity-permissions.php`, `access-permissions.php`, `services.php`. |

## Fichiers pivots

- `bootstrap/app.php`, `app/Providers/AppServiceProvider.php` — bootstrap, singletons, CSRF.
- `app/Http/Middleware/HandleInertiaRequests.php` — props partagées (auth, permissions, ziggy).

## Descendre

- Features : [features/_ai.md](../features/_ai.md).
- Doc existante (L2) : `docs/10-BestPractices/ROUTES_ARCHITECTURE.md`, `docs/10-BestPractices/PROJECT_STRUCTURE.md`, `routes/README.md`.
