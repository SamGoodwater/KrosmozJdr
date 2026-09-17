# Structure du projet
 
- Respect strict des conventions Laravel 13 (backend) et Vue 3 + Atomic Design (frontend).
- Arborescence unifiée : pas de dossier frontend/ séparé, tout est intégré dans le projet Laravel.
- Organisation des composants frontend par Atomic Design (atoms, molecules, organisms, etc.), thématiques ou fonctionnalités.
- Utiliser les conventions officielles des frameworks pour les routes, migrations, tests, etc.
- **Routes** : découpage par thème ; point d'entrée unique `web.php` / `api.php` qui charge des fichiers dédiés (entities, admin, api/*). Voir [ROUTES_ARCHITECTURE.md](ROUTES_ARCHITECTURE.md).
- Voir aussi : [ATOMIC_DESIGN.md](../40-UI/ATOMIC_DESIGN.md) 

> Pour la gestion des scripts, automatisations et commandes artisan personnalisées, voir [docs/40-DevGuides/README.md](../40-DevGuides/README.md).

## Pages Admin (Inertia)

- Composants Vue : `resources/js/Pages/Admin/` (sous-dossiers par domaine : `characteristics/`, entités, etc.).
- Contrôleurs : `app/Http/Controllers/Admin/` — une route Inertia = un contrôleur + une page sous `Pages/Admin/`.
- Ne pas dupliquer de second arbre `Pages/Pages/Admin` : l’admin vit uniquement sous `Pages/Admin/`.

## Commandes Artisan

| Dossier | Rôle |
| --- | --- |
| `app/Console/Commands/Project/` | Bootstrap (`project:init`, `project:init:verify`, review, seed, …) |
| `app/Console/Commands/Scrapping/` | Pipeline DofusDB |
| `app/Console/Commands/Scrapping/Effects/` | Qualité / mapping effets scrapping |
| `app/Console/Commands/Effects/` | Maintenance transverse (`effects:rebuild-signatures`, appelé par `project:update`) |
| `app/Console/Commands/Characteristics/` | Audit / progression JSON `characteristic-definitions` |

Après déplacement de composants Vue : `pnpm run update:atomic-index`.