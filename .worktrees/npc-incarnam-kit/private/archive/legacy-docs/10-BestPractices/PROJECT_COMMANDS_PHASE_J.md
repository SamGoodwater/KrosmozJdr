# Phase J — Commandes projet & organisation CLI

## Nouveautés

### `project:seed`

Ensemencement **sans DofusDB** : délègue à `project:init` avec `--skip-scrapping` et `--skip-types` (migrations, seeders, règles, capacités, pages bibliothèque).

```bash
php artisan project:seed
php artisan project:seed --fresh
```

### `project:refresh --fast`

Raccourci pour une réinit locale après `migrate:fresh` **sans** types ni scrapping (`--skip-scrapping` + `--skip-types`).

### `project:cron --update`

Passe-plat vers `project:data:sync` pour le scheduler (options préfixées `update-*`).

```bash
php artisan project:cron --clear --update --update-entity=spell
```

## Doublons / alias conservés

| Entrée | Cible canonique |
|--------|-----------------|
| `server:prepare` | `project:prepare` (déprécié) |
| `server:load` | `project:dev` |
| `project:update` | `project:data:sync` |
| `project:data:import-rules-toc` | `pages:import-rules-toc` |
| `init` | `project:init` |

## Réorganisation

Les commandes `scrapping:effects:*` vivent dans `app/Console/Commands/Scrapping/Effects/` (`App\Console\Commands\Scrapping\Effects`).

Référence détaillée : [PROJECT_CLI.md](../40-DevGuides/PROJECT_CLI.md).
