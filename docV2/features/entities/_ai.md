# Entités JDR — carte IA (degré 1a)

> Système générique de gestion des « entités de jeu » (sorts, objets, monstres, classes, etc.). Toutes partagent un modèle de champs commun, des droits unifiés, et un pipeline d'affichage/édition standardisé côté front.

## Quand lire ce nœud

- Ajouter/modifier un type d'entité, un champ, une vue, une colonne de table.
- Comprendre les droits de lecture/écriture d'une fiche.
- Travailler sur les pages Index/Show/Edit d'une entité.

## Concepts clés

- **Champs communs** : `state` (`raw`/`draft`/`playable`/`archived`), `read_level` (0-5), `write_level` (0-5), `created_by`, soft delete. Beaucoup ont aussi `official_id`, `dofusdb_id`, `auto_update`. Détail : [README](./README.md#champs-communs).
- **Droits** : matrice rôle × état, puis `read_level`/`write_level`, l'auteur garde l'accès. Code : `app/Policies/Entity/BaseEntityPolicy.php`. Détail : [README](./README.md#droits).
- **Backend CRUD** : un contrôleur web par entité dans `app/Http/Controllers/Entity/` ; validation par Form Requests `app/Http/Requests/Entity/`.
- **Tables (lecture)** : API server-side TanStack via `app/Http/Controllers/Api/*TableController.php` + bulk via `*BulkController`, changement d'état via `EntityStateController`.
- **Registre front** : `resources/js/Entities/entity-registry.js` (modèle + descriptors + adapter par type ; `normalizeEntityType()` normalise singulier/pluriel).
- **Vues** : `minimal` | `line` | `text` | `full` | `edit`. Résolution dynamique : `resources/js/Utils/entity/resolveEntityViewComponent.js`. Conventions : rule `.cursor/rules/entity-views.mdc`.

## Fichiers pivots

- `app/Policies/Entity/BaseEntityPolicy.php` — logique de droits commune (view/create/update/delete + matrice visibilité).
- `app/Http/Controllers/Entity/SpellController.php` — exemple de contrôleur web d'entité (pattern réutilisé).
- `app/Http/Controllers/Api/` — `*TableController`, `*BulkController`, `EntityStateController`.
- `resources/js/Entities/entity-registry.js` — point d'entrée front d'une entité.
- `resources/js/Utils/entity/resolveEntityViewComponent.js` — charge le bon composant de vue.
- `resources/js/Pages/Organismes/table/EntityTanStackTable.vue` — table d'index.
- `resources/js/Pages/Molecules/entity/<type>/` — composants de vue par type (`*ViewMinimal`, `*LineRow`, `*ViewText`, `*ViewFull`).

## Descendre

- [README humain](./README.md) — modèle complet, liste des types, droits, flux front/back détaillés.
- Doc existante (L2) : `docs/20-Content/21-Entities/`, `docs/30-UI/ENTITY_VIEWS.md`, `docs/10-BestPractices/ENTITY_VIEWS_PHASE_C.md`.
