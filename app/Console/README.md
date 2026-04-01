# Commandes Artisan — Krosmoz-JDR

Les commandes sont chargées **récursivement** depuis `app/Console/Commands/` (`App\Console\Kernel::commands()`). Chaque sous-dossier correspond à une **thématique** ; le namespace PHP est `App\Console\Commands\{Thématique}`.

- **Liste à jour :** `php artisan list`
- **Aide d’une commande :** `php artisan <nom> --help`
- **Données DofusDB (sync catalogue / entités, cas limites) :** le détail des règles et exemples avancés reste dans [docs/40-DevGuides/PROJECT_CLI.md](../../docs/40-DevGuides/PROJECT_CLI.md).

---

## 1. Commandes `project:*` et `setup`

C’est l’**interface principale** du projet : dépendances, dev local, nettoyages, données DofusDB, sauvegardes, bootstrap. La logique partagée vit dans **`App\Services\Project\ProjectRunService`** ; plusieurs commandes `project:*` y délèguent.

**Garde-fous :** beaucoup de commandes ci-dessous sont **interdites en production** (`GuardsProductionEnvironment`) ou réservées au **développement local** — voir la colonne « Environnement » ou `php artisan <cmd> -h`.

### 1.1 `project:deps` — stack (apt, composer, pnpm, CSS, doc, migrate)

Met à jour l’environnement de développement. **Par défaut** (aucune option ou `--all`) : apt + composer + pnpm + CSS + doc + dump-autoload + **migrate**.

| Option | Effet |
|--------|--------|
| *(défaut)* / `--all` | Stack complète + migrate |
| `--apt` | Mise à jour système (via `setup`) |
| `--composer` | `composer update` |
| `--pnpm` | Mise à jour pnpm / deps front |
| `--css` | Rebuild CSS |
| `--docs` | Index + schéma documentation |
| `--dump` | `composer dump-autoload` |
| `--migrate` | Migrations (`setup --db`) |
| `--ide` | IDE Helper + méta |
| `--laravel-clear` | `optimize:clear` Laravel |

```bash
php artisan project:deps
php artisan project:deps --pnpm --css
```

**Non destiné à la production** (déploiements contrôlés à la place).

### 1.2 `project:dev` — serveurs PHP + Vite

| Option | Effet |
|--------|--------|
| *(défaut)* | Serveur PHP + Vite (flux dev principal) |
| `--prepare` | Nettoyage, deps de base, optimisations, **migrate** |
| `--migrate` | Migrations uniquement (`setup --db`) |
| `--watch` | Watch CSS au lieu du serveur dev optimisé |

```bash
php artisan project:dev --prepare
php artisan project:dev
```

**Interdit en production.**

### 1.3 `project:clear` — caches et artefacts

Combiner les options selon le besoin ; `--all` couvre un nettoyage large (cache, config, routes, vues, CSS générés, etc.). `--kill` arrête les processus sur les ports dev habituels (8000, 8001, 8002, 5173).

```bash
php artisan project:clear --all
php artisan project:clear --kill --cache
```

**Interdit en production.**

### 1.4 `project:optimize` — IDE Helper / Laravel

Indiquer au moins une cible : `--all`, et/ou `--ide`, et/ou `--laravel`.

```bash
php artisan project:optimize --all
```

**Interdit en production.**

### 1.5 `project:reset` — réinitialisations lourdes

| Option | Effet |
|--------|--------|
| `--pnpm` | Réinit pnpm (`setup --refresh`) |
| `--composer` | Réinit composer (`setup --refresh`) |
| `--all` | Réinit large (vendor/node, caches…) |
| `--full` | Très destructif : reset large + `migrate:fresh --seed` |

**Interdit en production.**

### 1.6 `project:fix-permissions` — propriétaire des fichiers

```bash
php artisan project:fix-permissions www-data
```

Attribue le dépôt à l’utilisateur système indiqué (`chown` / chmod Laravel). **Interdit en production** (sauf besoin d’infra documenté).

### 1.7 `project:effects` — qualité / pipeline effets de sorts

Raccourci vers la quality gate et le pipeline scrapping effets (strict ou dev, avec options de pagination / filtres pour le pipeline). Voir `php artisan project:effects --help`.

**Interdit en production.**

### 1.8 `project:refresh` — base vide + caches (local)

Destructif : `migrate:fresh` (avec confirmation sauf `--force`).

| Option | Effet |
|--------|--------|
| `--hard` | `setup --refresh` avant (réinstall vendor + node_modules) |
| `--without-seed` | `migrate:fresh` sans `--seed` |
| `--force` | Sans confirmation (CI / scripts) |

Enchaîne un nettoyage complet type `project:clear --all`.

**Interdit en production.**

### 1.9 `setup` — machine & projet (apt, deps, BDD, clean, refresh)

Installe ou met à jour paquets apt, dépendances PHP/JS, base MySQL/Postgres (création user/base si besoin), ou nettoie / réinstalle.

| Option | Effet |
|--------|--------|
| `--install` | Vérifier / installer apt + composer + pnpm |
| `--update` | Mettre à jour apt, pnpm, composer |
| `--db` | BDD : migrations + seeders (sauf `--no-seed`) |
| `--no-seed` | Avec `--db`, sans seeders |
| `--clean` | Supprimer `node_modules`, `vendor`, locks ; clear config |
| `--refresh` | Clean puis réinstall deps |

```bash
php artisan setup --install --db
php artisan setup --db --no-seed
```

Liste des paquets apt : `app/Console/Commands/Project/SetupCommand.php`.

### 1.10 `project:data` — entrée « données DofusDB »

**Action** obligatoire : `sync` | `updates` | `init` | `fill` | `upgrade`.

| Action | Rôle |
|--------|------|
| `sync` / `updates` | Délègue à `project:data:sync` (+ catalogue optionnel : `--type`, `--races`, `--entity`, etc.) |
| `init` | Pipeline d’init données (voir `project:init` pour le bootstrap complet) |
| `fill` / `upgrade` | Guide complétion — pas entièrement automatisé |

```bash
php artisan project:data sync --entity=monster,spell
php artisan project:data sync --type=all
php artisan project:data init --fresh
```

**Détail des alias d’entités, interaction `--type` / `--entity`, sync admin web :** [PROJECT_CLI.md](../../docs/40-DevGuides/PROJECT_CLI.md).

### 1.11 `project:data:sync` — alias `project:update`

Met à jour les fiches en base avec `auto_update=true` depuis DofusDB. Planifiable :

- `PROJECT_UPDATE_AUTO_ENABLED=true`
- `PROJECT_UPDATE_CRON="..."`

### 1.12 `project:data:import-rules-toc`

Importe la table des matières des règles vers le CMS — **délègue** à `pages:import-rules-toc`. Préférer cette entrée pour le domaine « données ».

### 1.13 `project:init` / `init` — installation complète

Migrations, seeders, types, scrapping, capacités selon options. **`--deps`** enchaîne d’abord `project:deps`.

```bash
php artisan project:init --deps --fresh
php artisan project:init -h
```

### 1.14 `project:super-admin`

Création interactive du premier compte **super_admin** si aucun n’existe (hors flux `init`). Partage la logique avec `project:init`.

### 1.15 `project:backup` — sauvegardes locales

Dump BDD (gzip) + archive `storage/app` (tar.gz ou ZIP), rotation selon rétention. Configuration : `config/project-backup.php`, variables `PROJECT_BACKUP_*`.

```bash
php artisan project:backup
php artisan project:backup --no-storage
php artisan project:backup --prune-only --dry-run
```

Voir [PROJECT_CLI.md](../../docs/40-DevGuides/PROJECT_CLI.md) (section backup) pour les options complètes.

### 1.16 Flux dev courant

- **`project:dev`**, **`project:clear`**, **`project:optimize`** pour le quotidien.
- **`server:load`** : `optimize` puis équivalent **`project:dev`** (section [Development](#9-development--outils-locaux)).
- **PHP + queue + CSS en parallèle :** `composer run dev` (scripts Composer du projet).

---

## 2. Planificateur (`Kernel::schedule`)

| Commande / job | Fréquence |
|----------------|-----------|
| `media:clean-thumbnails` | Quotidien |
| `privacy:process-deletion-requests` | Quotidien 02:00 |
| `SendNotificationDigestsJob` | Voir horaires dans `App\Console\Kernel` |
| `project:data:sync` | Si `PROJECT_UPDATE_AUTO_ENABLED=true` (`PROJECT_UPDATE_CRON`) |
| `project:backup` | Si `PROJECT_BACKUP_ENABLED=true` (`PROJECT_BACKUP_CRON`, défaut 4h) |
| `scrapping` (alias `scrapping:run`) | Si `SCRAPPING_RESOURCES_AUTO_SYNC=true` |

---

## 3. Scrapping — DofusDB & catalogue

| Commande | Rôle |
|----------|------|
| `scrapping:run` (`scrapping`) | Import principal (monstres, items, sorts, ressources, …) — **`--help`** |
| `scrapping:setup` | Socle scrapping (migrations + caractéristiques + mappings) |
| `scrapping:seeders:export` | Export BDD → `database/seeders/data/` |
| `scrapping:types:seed` | Types d’objets API → BDD + seeders (`--only=…`) |
| `scrapping:types:extract` | Extraction types → fichiers seeders |
| `scrapping:types:migrate-items` | Migration `item_type_id` (superTypes) |
| `scrapping:races:seed` | Races monstres DofusDB |
| `scrapping:items:repair-routing` | Rattrapage items mal classés |
| `scrapping:effects:map` | Propositions de mapping effets → seeder |
| `scrapping:effects:pipeline` | Import sorts + quality gate effets |
| `scrapping:effects:audit-quality` | Audit qualité pipeline / conversions |
| `scrapping:effects:audit-autre` | Audit sous-effets « autre » |
| `scrapping:effects:quality-gate` | Seuils type CI |
| `scrapping:effects:report-missing-characteristics` | Rapport mappings sans `characteristic_key` |
| `scrapping:effects:backfill-characteristics` | Backfill `characteristic_key` |

Les audits effets et la quality gate partagent le même domaine métier ; la gate s’appuie sur la logique d’audit.

---

## 4. Effects — modèle effets Krosmoz

| Commande | Rôle |
|----------|------|
| `effects:rebuild-signatures` | Recalcule `config_signature` des degrés d’effet |

---

## 5. Media — Spatie Media Library

| Commande | Rôle |
|----------|------|
| `media:fix-storage-paths` | Chemins médias vers `MEDIA_PATH` des modèles |
| `media:clean-thumbnails` | Nettoie thumbnails legacy (`ImageService`, `--older-than`). **Planifié** quotidiennement |

---

## 6. Privacy — RGPD

| Commande | Rôle |
|----------|------|
| `privacy:process-deletion-requests` | Traite les demandes de suppression après délai |

---

## 7. Pages — CMS

| Commande | Rôle |
|----------|------|
| `pages:import-rules-toc` | Pages / sections depuis la TOC des règles — préférer **`project:data:import-rules-toc`** côté domaine données |

---

## 8. Characteristics & capabilities

| Commande | Rôle |
|----------|------|
| `characteristics:generate-color-css` | CSS des couleurs (classes `.color-{key}`) |
| `capabilities:import-legacy` | Import capacités depuis export JSON (ancienne base) |

---

## 9. Development — outils locaux

| Commande | Garde-fou | Rôle |
|----------|-----------|------|
| `server:load` | `guardNotProduction` | `optimize` puis équivalent `project:dev` |
| `server:prepare` | `guardDevelopmentOnly` | Bootstrap large : `composer update`, ide-helper, `pnpm install`, `migrate`, … (voir `PrepareProjectCommand` — chevauche partiellement `project:dev --prepare`) |
| `generate:test {name}` | `guardDevelopmentOnly` | Esquisse de test PHPUnit Feature pour un modèle |

---

## 10. Conventions

- **Suffixe `Command`** pour les classes (PSR / Laravel).
- **Préfixes** : `scrapping:`, `project:`, `media:`, etc. pour un `php artisan list` lisible.
- **Production :** `GuardsProductionEnvironment` — `guardDevelopmentOnly()` (local + testing) ou `guardNotProduction()` selon les commandes.
