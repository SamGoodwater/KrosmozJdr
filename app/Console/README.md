# Commandes Artisan — Krosmoz-JDR

Les commandes sont chargées **récursivement** depuis `app/Console/Commands/` (découverte Laravel via `Application::configure()` → `withCommands()`). Chaque sous-dossier correspond à une **thématique** ; le namespace PHP est `App\Console\Commands\{Thématique}`.

Closures Artisan : `routes/console.php`.

- **Liste à jour :** `php artisan list`
- **Aide d’une commande :** `php artisan <nom> --help`
- **Données DofusDB (sync catalogue / entités, cas limites) :** le détail des règles et exemples avancés reste dans [docs/operations/README.md](../../docs/operations/README.md).
- **Interface web :** tableau de bord `/admin` (sync DofusDB, sauvegarde, mise à jour stack, etc.) — voir la section *Interface web* dans [PROJECT_CLI.md](../../docs/operations/README.md).

### Tests PHPUnit (extrait)

| Zone | Fichiers |
|------|----------|
| Sécurité compte système & super-admin (web, policies…), console super-admin (planning, reviews) | `tests/Feature/Security/SystemAccountAndSuperAdminWebTest.php`, `tests/Feature/Admin/ProjectSuperConsoleTest.php`, `tests/Feature/User/UserPolicyTest.php`, … |
| `ProjectRunService` (carte d’options) | `tests/Unit/Services/Project/ProjectRunServiceTest.php` |
| Sauvegarde (purge fichiers) | `tests/Unit/Services/Project/ProjectBackupServiceTest.php` |
| UI admin sync / backup / `project:deps` web | `tests/Feature/Admin/ProjectMaintenanceControllerTest.php`, `ProjectBackupWebControllerTest.php`, `ProjectDepsWebControllerTest.php`, `AdminDashboardControllerTest.php` |
| Commande `project:clear` (smoke) | `tests/Feature/Console/ProjectClearCommandTest.php` |
| Commande `project:review` (smoke Pint/options) | `tests/Feature/Console/ProjectReviewCommandTest.php` |
| Scrapping (Artisan) | `tests/Feature/Scrapping/ScrappingRunCommandTest.php`, effets / seeders export, etc. |

Les tests **Feature** avec `RefreshDatabase` attendent une base MySQL dédiée (ex. `krosmoz_testing` selon `phpunit.xml`). Sans serveur MySQL accessible, PHPUnit échoue à la connexion — lancer les tests sur une machine où la BDD de test est créée.

---

## 1. Commandes `project:*` et `setup`

C’est l’**interface principale** du projet : dépendances, dev local, nettoyages, données DofusDB, sauvegardes, bootstrap. La logique partagée vit dans **`App\Services\Project\ProjectRunService`** ; plusieurs commandes `project:*` y délèguent.

**Garde-fous :** beaucoup de commandes ci-dessous sont **interdites en production** (`GuardsProductionEnvironment`) ou réservées au **développement local** — voir la colonne « Environnement » ou `php artisan <cmd> -h`. En prod, **`setup`** et **`project:init`** sont bloqués tant que `APP_ENV=production`. Le scrapping **`--batch`** n’accepte que des fichiers sous la **racine projet** (`base_path`).

### 1.1 `project:deps` — dépendances Composer & pnpm

**Par défaut** : `composer update` + `pnpm up`, puis **`project:optimize`**. Option **`--with-system`** pour enchaîner `setup --update` (apt / outils). Cibles CSS, doc, migrate, etc. : voir `php artisan project:deps -h`.

```bash
php artisan project:deps
php artisan project:deps --with-system
```

**Non destiné à la production** (déploiements contrôlés à la place).

### 1.2 `project:prepare` — CSS, caches, doc, migrations

Prépare le dépôt avant un `project:dev` (rebuild CSS, caches, doc, `setup --db`).

| Option | Effet |
|--------|--------|
| `--clear` | Avant la préparation : supprime les artefacts de tests (`.phpunit.cache`, `.phpunit.result.cache`, dossier `coverage/`, contenu de `storage/framework/testing/`) — sans toucher à la BDD ni aux vendors |
| `--dev` | Après la préparation : enchaîne `project:optimize` puis les serveurs (équivalent à enchaîner prepare + optimize + dev sans double préparation) |

```bash
php artisan project:prepare
php artisan project:prepare --clear
php artisan project:prepare --clear --dev
```

**Interdit en production.**

### 1.3 `project:dev` — prepare + optimize + serveurs PHP + Vite

| Option | Effet |
|--------|--------|
| *(défaut)* | `project:prepare` + `project:optimize` + serveurs |
| `--no-prepare` / `--no-optimize` | Sauter une étape |
| `--prepare` | Uniquement `project:prepare` puis quitter |
| `--clear` | Passe `--clear` à `project:prepare` (nettoyage artefacts de tests avant préparation) |
| `--migrate` | Migrations uniquement (`setup --db`) |
| `--watch` | Watch CSS au lieu du serveur dev optimisé |

```bash
php artisan project:dev
php artisan project:dev --no-prepare --no-optimize
php artisan project:dev --prepare --clear
```

**Interdit en production.**

### 1.4 `project:clear` — caches et artefacts

Combiner les options selon le besoin ; `--all` couvre un nettoyage large (cache, config, routes, vues, CSS générés, etc.). `--test` ne supprime que les artefacts laissés par les tests (PHPUnit, coverage, `storage/framework/testing`). `--kill` arrête les processus sur les ports dev habituels (8000, 8001, 8002, 5173).

```bash
php artisan project:clear --all
php artisan project:clear --kill --cache
php artisan project:clear --test
```

**Interdit en production.**

### 1.5 `project:optimize` — IDE Helper & Laravel

Pipeline : `optimize:clear` → IDE Helper → `composer dump-autoload` → `optimize`. Options `--clear-only` et `--ide-only` pour des sous-ensembles.

```bash
php artisan project:optimize
```

**Interdit en production.**

### 1.6 `project:reset` — réinitialisations lourdes

| Option | Effet |
|--------|--------|
| `--pnpm` | Réinit pnpm (`setup --refresh`) |
| `--composer` | Réinit composer (`setup --refresh`) |
| `--all` | Réinit large (vendor/node, caches…) |
| `--full` | Très destructif : reset large + `migrate:fresh --seed` |

**Interdit en production.**

### 1.7 `project:fix-permissions` — propriétaire des fichiers

```bash
php artisan project:fix-permissions www-data
```

Attribue le dépôt à l’utilisateur système indiqué (`chown` / chmod Laravel). **Interdit en production** (sauf besoin d’infra documenté).

### 1.8 `project:effects` — qualité / pipeline effets de sorts

Raccourci vers la quality gate et le pipeline scrapping effets (strict ou dev, avec options de pagination / filtres pour le pipeline). Voir `php artisan project:effects --help`.

**Interdit en production.**

### 1.9 `project:refresh` — base vide + caches (local)

Destructif : `migrate:fresh` (avec confirmation sauf `--force`).

| Option | Effet |
|--------|--------|
| `--hard` | `setup --refresh` avant (réinstall vendor + node_modules) |
| `--without-seed` | `migrate:fresh` sans `--seed` |
| `--force` | Sans confirmation (CI / scripts) |

Enchaîne un nettoyage complet type `project:clear --all`.

**Interdit en production.**

### 1.10 `setup` — machine & projet (apt, deps, BDD, clean, refresh)

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

### 1.11 `project:data` — entrée « données DofusDB »

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

**Détail des alias d’entités, interaction `--type` / `--entity`, sync admin web :** [PROJECT_CLI.md](../../docs/operations/README.md).

### 1.12 `project:data:sync` — alias `project:update`

Met à jour les fiches en base avec `auto_update=true` depuis DofusDB. Planifiable :

- `PROJECT_UPDATE_AUTO_ENABLED=true`
- `PROJECT_UPDATE_CRON="..."`

### 1.13 `project:data:import-rules-toc`

Importe la table des matières des règles vers le CMS — **délègue** à `pages:import-rules-toc`. Préférer cette entrée pour le domaine « données ».

### 1.14 `project:init` / `init` — installation complète

Migrations, seeders (dont langues, conditions, traits, pages Création, spécialisations legacy si fichiers présents), types, scrapping, capacités selon options. **`--deps`** enchaîne d’abord `project:deps`. **`--skip-specializations`** pour ignorer l’import HTML des spécialisations (CI / base sans fichiers legacy).

```bash
php artisan project:init --deps --fresh
php artisan project:init -h
```

### 1.15 `project:review` / `review`

Rapport dev Markdown (alias de **`dev:review`**) : soit **profil** positionnel (`tests`, `quality`, `security`, `docs`, `all` — défaut `all`), soit **options par action** (`--pint`, `--tests`, `--test-back`, `--test-front`, `--phpstan`, `--eslint`, `--security`, `--docs`, `--all`, combinables). Sans profil ni option d’action → tout le périmètre (comme `all`). Avec options d’action, le profil positionnel est ignoré. Sortie par défaut `storage/app/dev-reports/review-<timestamp>.md` — joignable à un agent Cursor.

Pint dispose d’options dédiées pour rendre les reviews de feature plus robustes : `--pint-dirty` limite l’analyse aux fichiers Git modifiés, `--pint-timeout=300` règle le timeout, et un fallback par lots est lancé automatiquement si le run global dépasse ce timeout (`--no-pint-batches` le désactive).

```bash
php artisan project:review
php artisan project:review --pint
php artisan project:review --pint --pint-dirty
php artisan project:review --all --pint-timeout=900
php artisan project:review --test-back --eslint
php artisan review tests --report-path=storage/app/dev-reports/rapport.md
```

### 1.16 `project:super-admin`

Création interactive du premier compte **super_admin** si aucun n’existe (hors flux `init`). Partage la logique avec `project:init`.

### 1.17 `project:schedule:sync`

Ajoute en base les entrées du **catalogue de planification** lorsqu’elles manquent (sans écraser vos réglages cron). Utile après mise à jour déployant de nouvelles clés de tâche.

```bash
php artisan project:schedule:sync
```

### 1.18 `project:backup` — sauvegardes locales

Dump BDD (gzip) + archive `storage/app` (tar.gz ou ZIP), rotation selon rétention. Configuration : `config/project-backup.php`, variables `PROJECT_BACKUP_*`.

```bash
php artisan project:backup
php artisan project:backup --no-storage
php artisan project:backup --prune-only --dry-run
```

Voir [PROJECT_CLI.md](../../docs/operations/README.md) (section backup) pour les options complètes.

### 1.19 Flux dev courant

- **`project:dev`** (prepare + optimize + serveurs), **`project:clear`**, **`project:optimize`** pour le quotidien.
- **`server:load`** : alias de **`project:dev`** (section [Development](#9-development--outils-locaux)).
- **PHP + queue + CSS en parallèle :** `composer run dev` (scripts Composer du projet).

---

## 2. Planificateur Laravel (`schedule:run`)

| Source | Rôle |
|--------|------|
| Table **`project_schedule_tasks`** (migrate + lignes seed) | Fréquences et activation **modifiables par le super-admin** (`/admin/project-schedule`). Les commandes exécutées restent whitelistées dans `App\Support\ProjectSchedule\ProjectScheduleCatalog`. |
| `bootstrap/app.php` (`withSchedule`) | Laravel 12+ : enregistrement du planner (`ProjectScheduleRegistrar::register($schedule)`). |
| Secours `.env` | Si la table n’existe pas encore (**migrate** non fait), ancien comportement : mêmes tâches qu’auparavant selon les variables (`PROJECT_*`, `SCRAPPING_*`). Un log `NOTICE` invite à migrer. |
| Une ligne crontab | `* * * * * php artisan schedule:run` (répertoire projet + PHP corrects). |

| Commande / job type | Défaut (voir also BDD) |
|---------------------|-------------------------|
| `media:clean-thumbnails` | `0 0 * * *` |
| `privacy:process-deletion-requests` | `0 2 * * *` |
| `SendNotificationDigestsJob` (daily / weekly / monthly) | `5 0 * * *`, `10 0 * * 1`, `15 0 1 * *` |
| `project:data:sync` | désactivée sauf environnement où la ligne existe activée dans la BDD (ex. anciennement via `PROJECT_UPDATE_AUTO_ENABLED`). |
| `project:backup` | désactivée par défaut (activer ligne + même logique `.env`). |
| `scrapping … resource …` | désactivée par défaut. |

Voir aussi : **`php artisan project:schedule:sync`**, **`php artisan schedule:list`**.

## 3. Scrapping — DofusDB & catalogue

| Commande | Rôle |
|----------|------|
| `scrapping:run` (`scrapping`) | Import principal (monstres, items, sorts, ressources, …) — **`--help`** ; gate pré-import **active par défaut** (désactiver : `--no-quality-gate`) |
| `scrapping:setup` | Socle scrapping (migrations + caractéristiques + mappings) |
| `scrapping:seeders:export` | Export BDD → `database/seeders/data/` |
| `scrapping:types:seed` | Types d’objets API → BDD + seeders (`--only=…`) |
| `scrapping:types:extract` | Extraction types → fichiers seeders |
| `scrapping:types:migrate-items` | Migration `item_type_id` (superTypes) |
| `scrapping:races:seed` | Races monstres DofusDB |
| `scrapping:items:repair-routing` | Rattrapage items mal classés |
| `scrapping:effects:*` | Commandes dans `Commands/Scrapping/Effects/` (namespace `Scrapping\Effects`) |
| `scrapping:effects:map` | Propositions de mapping effets → seeder |
| `scrapping:effects:pipeline` | Import sorts + quality gate effets |
| `scrapping:effects:audit-quality` | Audit qualité pipeline / conversions |
| `scrapping:effects:audit-autre` | Audit sous-effets « autre » |
| `scrapping:effects:reapply-mappings` | Reclasse les `autre` déjà mappés (sans re-import) |
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
| `server:load` | `guardNotProduction` | Alias de `project:dev` |
| `server:prepare` | `guardDevelopmentOnly` | **[Déprécié]** — appelle `project:prepare` |
| `generate:test {name}` | `guardDevelopmentOnly` | Esquisse de test PHPUnit Feature pour un modèle |

---

## 10. Conventions

- **Suffixe `Command`** pour les classes (PSR / Laravel).
- **Préfixes** : `scrapping:`, `project:`, `media:`, etc. pour un `php artisan list` lisible.
- **Production :** `GuardsProductionEnvironment` — `guardDevelopmentOnly()` (local + testing) ou `guardNotProduction()` selon les commandes.
