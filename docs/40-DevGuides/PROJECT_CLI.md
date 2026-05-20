# Interface CLI unifiée du projet (`project:*`)

Objectif : **un vocabulaire stable** (dépendances, dev, données, bootstrap) via les commandes **`project:*`** et le service **`ProjectRunService`**, avec `setup` / `scrapping:*` pour les domaines concernés.

## Ancienne commande `run` (supprimée)

L’ancienne commande **`php artisan run`** a été **retirée**. Tout passe par les commandes **`project:*`** et **`App\Services\Project\ProjectRunService`** (voir tableau de correspondance en fin de fichier).

## Principes

| Préfixe | Rôle |
|---------|------|
| `project:deps` | Mise à jour **Composer** + **pnpm**, puis `project:optimize` (option `--with-system` pour apt). |
| `project:prepare` | CSS, caches, doc, migrations — avant un `project:dev`. |
| `project:dev` | `project:prepare` + `project:optimize` par défaut, puis serveurs PHP / Vite. |
| `project:refresh` | Repartir de zéro (libs optionnelles + `migrate:fresh`). |
| `project:data` | Données DofusDB (sync, init catalogue, guide fill). |
| `project:data:sync` | Mise à jour des entités déjà en base avec `auto_update=true`. |
| `project:init` | Pipeline complet d’installation (migrations, seeders, types, scrapping, capacités). |
| `project:init:verify` | Contrôle post-init (pages critiques, types, caractéristiques, mappings) — option `--verify` sur `project:init`. |
| `project:seed` | Seeders et données locales **sans** DofusDB (`project:init` + `--skip-scrapping` + `--skip-types`). |
| `project:super-admin` | Création interactive du premier super_admin (hors `init`). |
| `project:backup` | Dump BDD (gzip) + archive `storage/app` (tar.gz, ZIP si besoin), purge > N jours. |

La logique commune vit dans **`App\Services\Project\ProjectRunService`** ; les commandes **`project:*`** sont les **entrées** pour le dev local.

**Historique :** l’option **`run --regenerate`** avait été renommée **`run --prepare`** ; l’équivalent actuel est **`project:prepare`** (ou **`project:dev`** qui l’enchaîne par défaut).

**Collision résolue :** l’ancien `project:update` (sync auto_update) devient la commande canonique **`project:data:sync`**. L’alias **`project:update`** est conservé (scheduler, scripts, habitude).

### Repérer une commande Artisan obsolète ou inutilisée

1. **`php artisan list`** — inventaire des commandes enregistrées.
2. **Rechercher les références** : `rg "ma:commande" --type php` et dans `docs/`, `composer.json`, CI.
3. **Usages dynamiques** : `routes/console.php`, jobs (`Artisan::call`), seeders, tests.
4. **Sans référence** + non documentée + doublon d’une commande `project:*` → candidat à **alias déprécié** ou suppression après une version.

---

## `project:deps` — dépendances projet

Par défaut (aucune option) : **`composer update`** + **`pnpm up`**, puis **`project:optimize`**.

| Option | Effet |
|--------|--------|
| *(défaut)* / `--all` | `composer update` + `pnpm up` + `project:optimize` |
| `--with-system` | Avec le mode par défaut : exécute aussi `setup --update` (apt / outils). |
| `--apt` | `setup --update` uniquement |
| `--composer` | `composer update` |
| `--pnpm` | `pnpm up` |
| `--css`, `--docs`, `--dump`, `--migrate` | Délégation inchangée au `ProjectRunService` (rebuild CSS, doc, autoload, `setup --db`). |
| `--optimize` | Enchaîne `project:optimize` après les autres cibles (hors enchaînement déjà inclus dans le mode par défaut). |

**Exemples**

```bash
php artisan project:deps
php artisan project:deps --with-system
php artisan project:deps --pnpm --css --optimize
```

**Init + deps :** `php artisan project:init --deps` enchaîne `project:deps` puis le pipeline `init`.

---

## `project:prepare` — prêt pour le dev

Rebuild CSS (`pnpm run css`), vide caches applicatifs et vues, régénère la doc, migrations (`setup --db`). Utilisé seul ou appelé automatiquement par **`project:dev`**.

| Option | Effet |
|--------|--------|
| `--clear` | Avant la préparation : supprime les artefacts de tests (cache PHPUnit, `.phpunit.result.cache`, dossier `coverage/`, contenu de `storage/framework/testing/`) — sans `migrate:fresh` ni vendor |
| `--dev` | Après la préparation : enchaîne `project:optimize` puis les serveurs (équivalent à `project:dev` sans refaire deux fois `project:prepare`) |

```bash
php artisan project:prepare
php artisan project:prepare --clear
php artisan project:prepare --clear --dev
```

---

## `project:clear` — caches et artefacts

Voir aussi **`app/Console/README.md`**. Options courantes : `--all` (large), `--kill` (ports dev), **`--test`** (uniquement artefacts PHPUnit / coverage / `storage/framework/testing`).

```bash
php artisan project:clear --test
php artisan project:clear --all
```

---

## `project:optimize` — IDE & caches Laravel

Pipeline fixe : **`optimize:clear`** → **IDE Helper** (models, generate, eloquent, meta) → **`composer dump-autoload`** → **`optimize`**.

| Option | Effet |
|--------|--------|
| *(défaut)* | Pipeline complet |
| `--clear-only` | `optimize:clear` uniquement |
| `--ide-only` | IDE Helper + dump-autoload |

---

## `project:dev` — développement

| Option | Effet |
|--------|--------|
| *(défaut)* | `project:prepare` + `project:optimize` + serveurs PHP + Vite |
| `--no-prepare` | Ne pas exécuter `project:prepare` |
| `--no-optimize` | Ne pas exécuter `project:optimize` |
| `--prepare` | Exécuter uniquement `project:prepare` puis quitter |
| `--clear` | Passe `--clear` à `project:prepare` (nettoyage artefacts de tests avant préparation) |
| `--migrate` | Migrations uniquement (`setup --db`). |
| `--watch` | Watch CSS (équiv. ancien `run --dev:watch`). |

```bash
php artisan project:dev --prepare --clear
```

---

## `project:refresh` — reset local

| Option | Effet |
|--------|--------|
| `--hard` | `setup --refresh` (reinstall vendor + node après clean). |
| `--without-seed` | Transmet `--skip-seeders` à `project:init`. |
| `--fast` | Raccourci `--skip-scrapping` + `--skip-types` (réinit locale sans appels DofusDB). |
| `--skip-scrapping` / `--skip-types` / `--noimage` | Transmis à `project:init`. |
| `--force` | Pas de confirmation (CI). |

Enchaîne `project:init --fresh` puis les clears type `project:cron --clear`.

**Attention :** destructif sur la base (sauf si vous annulez la confirmation).

---

## `project:data` — données DofusDB

| Action | Commande cible | Description |
|--------|----------------|-------------|
| `sync` ou `updates` | `project:data:sync` (+ catalogue optionnel) | Met à jour les fiches **déjà en base** avec `auto_update=true`, ou rafraîchit types / races avant. |
| `init` | `project:init` | Pipeline complet d’init (voir options `project:init`). |
| `fill` ou `upgrade` | *(guide)* | Import des **manquants** : pas encore automatisé ; utiliser `scrapping:run` sans `--skip-existing` ou filtres pagination, puis éventuellement `project:data sync`. |

### `project:data sync` — entités (`--entity`)

Liste **séparée par des virgules**. Alias acceptés :

| Saisie utilisateur | Entité interne |
|--------------------|----------------|
| `breed`, `nbreed`, `classe` | `class` (races jouables DofusDB) |
| `spell`, `monster`, `panoply`, `resource`, `item`, `consumable` | inchangé |

Exemple :

```bash
php artisan project:data sync --entity=breed,spell,monster,panoply,resource,item,consumable
```

**Panoplie :** la table `panoplies` n’a pas encore de colonne `auto_update` ; le sync entité `panoply` est ignoré avec un avertissement tant que la colonne n’existe pas.

### `project:data sync` — catalogue (`--type`, `--races`)

Synchronise les **référentiels** (types d’objets DofusDB, races monstres, types de sorts métier).

| Valeur `--type` | Effet |
|-----------------|--------|
| `all` | `scrapping:types:seed` (les 3 familles) + `scrapping:races:seed` + `SpellTypeSeeder` |
| `monster` | Races monstres (`scrapping:races:seed`) — **`--races` est un raccourci pour `--type=monster`** |
| `resource`, `consumable`, `item`, `equipment` | Sous-ensemble des types item (`equipment` = même seeder que `item`) via `scrapping:types:seed --only=…` |
| `spell` | `SpellTypeSeeder` uniquement (référentiel sorts, pas les fiches sort) |

Exemples :

```bash
php artisan project:data sync --type=all
php artisan project:data sync --type=monster,resource,item,consumable
php artisan project:data sync --races
```

Options **`--skip-cache`** et **`--lang`** s’appliquent aux commandes `scrapping:types:seed` et `scrapping:races:seed`.

### Catalogue **et** sync entités

Si tu passes **`--type` ou `--races`**, le sync des entités (`project:data:sync`) **ne s’exécute pas** tant que tu ne précises pas aussi **`--entity=…`**.

| Commande | Résultat |
|----------|----------|
| `project:data sync` | Sync **toutes** les entités éligibles (comportement historique). |
| `project:data sync --type=all` | **Uniquement** le catalogue (pas de sync entités). |
| `project:data sync --type=all --entity=monster` | Catalogue complet, puis sync **monster** uniquement. |

### `scrapping:types:seed --only=`

Utilisable seul : `--only` liste `resource`, `consumable`, `item` ou `equipment` (virgules), ou `all` / vide pour les trois. Réutilise l’extraction API puis les seeders ciblés.

**Exemples généraux**

```bash
php artisan project:data sync --entity=monster
php artisan project:data init --fresh --noimage
```

Pour le détail des flags : `php artisan project:init -h`, `php artisan project:data:sync -h`, `php artisan project:data -h`.

### Interface web — espace `/admin`

Le **tableau de bord** (`GET /admin`, `admin.dashboard.index`) regroupe la navigation vers les outils (sync, sauvegarde, mise à jour stack, utilisateurs, scrapping, caractéristiques, effets, mappings). **Accès** : meneur de jeu et rôles supérieurs (`admin.area`). Les actions **super admin** (sync, backup, deps) sont listées dans la barre latérale seulement pour ce rôle.

#### Sync données DofusDB (`project:data sync`)

Une page **Inertia** lance l’équivalent de **`project:data sync`** **sans bloquer le navigateur** : le travail est mis en file (`RunProjectDataSyncJob` → `Artisan::call('project:data', …)`).

| Élément | Détail |
|--------|--------|
| **URL** | `/admin/project-maintenance` (`admin.project-maintenance.index`, POST `admin.project-maintenance.sync`) |
| **Accès** | **super_admin** uniquement ; menu compte → **Espace administration** → entrée latérale **Sync données** |
| **Mot de passe** | Comme **`/scrapping`** : **GET** sans `password.confirm` obligatoire ; formulaire avec **`ConfirmPasswordModal`**. Le **POST** applique **`password.confirm`** + throttle |
| **POST** | `StoreProjectDataSyncRequest`, journalisation `admin.project_maintenance.sync_dispatched` |
| **Prérequis** | Worker de file (`php artisan queue:work`) ; verrou cache contre deux syncs massives parallèles |

Les options du formulaire suivent les mêmes règles que la CLI ci-dessus.

#### Sauvegarde (`project:backup`)

| Élément | Détail |
|--------|--------|
| **URL** | `/admin/backup` — POST `admin.backup.run` |
| **Job** | `RunProjectBackupJob` → `Artisan::call('project:backup', …)` ; verrou cache dédié |
| **Accès** | **super_admin** ; `password.confirm` sur le POST |

#### Mise à jour stack (`project:deps`)

| Élément | Détail |
|--------|--------|
| **URL** | `/admin/project-update` — POST `admin.project-update.run` |
| **Job** | `RunProjectDepsJob` → `Artisan::call('project:deps', …)` ; **refus si `APP_ENV=production`** (contrôleur + job) |
| **Accès** | **super_admin** ; `password.confirm` sur le POST |

---

## `project:data:sync` (alias `project:update`)

Planifiable via `.env` :

- `PROJECT_UPDATE_AUTO_ENABLED=true`
- `PROJECT_UPDATE_CRON="..."`

Le scheduler appelle **`project:data:sync`**.

---

## `project:backup` — sauvegardes locales

| Sortie | Contenu |
|--------|---------|
| `…_mysql.sql.gz` | Dump **MySQL / MariaDB** (`mysqldump` + `gzip`) ou copie **SQLite** compressée |
| `…_storage.tar.gz` | Arborescence **`storage/app`** sans **`app/backups`** (évite récursion) ; **ZIP** si `tar` indisponible (ex. Windows) |

**Rotation :** à chaque exécution (sauf `--no-prune`), suppression des fichiers du même préfixe plus vieux que **`PROJECT_BACKUP_RETENTION_DAYS`** (défaut **30** ≈ 1 mois). Noms reconnus : `*_mysql.sql.gz`, `*_storage.tar.gz`, `*_storage.zip`.

| Option | Effet |
|--------|--------|
| `--no-database` / `--no-storage` | Cibler une seule partie |
| `--path=` | Répertoire de sortie (défaut `storage/app/backups` ou `PROJECT_BACKUP_PATH`) |
| `--retention-days=` | Surcharge de la rétention |
| `--no-prune` | Ne pas supprimer les anciennes sauvegardes |
| `--prune-only` | Uniquement la purge |
| `--dry-run` | Purge simulée (liste des fichiers qui seraient supprimés) |

**Configuration :** `config/project-backup.php`, variables `.env` :

- `PROJECT_BACKUP_ENABLED` — si `true`, le scheduler exécute `project:backup` selon `PROJECT_BACKUP_CRON` (défaut `0 4 * * *`).
- `PROJECT_BACKUP_PATH`, `PROJECT_BACKUP_RETENTION_DAYS`, `PROJECT_BACKUP_MYSQLDUMP_PATH`, `PROJECT_BACKUP_PREFIX`.

**Prérequis :** binaire **`mysqldump`** sur le serveur (client MySQL) pour les connexions mysql/mariadb. Les dumps contiennent des données sensibles : protéger le répertoire de sauvegarde et les copies hors site.

**Exemples**

```bash
php artisan project:backup
php artisan project:backup --no-storage
php artisan project:backup --retention-days=14
php artisan project:backup --prune-only --dry-run
```

---

## `project:init` — bootstrap complet

Inchangé fonctionnellement, avec en plus :

- **`--deps`** : lance d’abord `project:deps` (stack complète).

Super admin : toujours via le flux seed + prompt (logique partagée avec `project:super-admin`, trait `PromptsPrimarySuperAdmin`).

### Données couvertes par `project:init` (sans `--skip-*`)

**Ordre d’exécution** : d’abord socle BDD / seeders / règles / capacités (fichier local), **puis** appels DofusDB (types API, scrapping entités — les plus longs), pour pouvoir arrêter l’init après les phases utiles aux tests.

| Étape | Contenu |
|--------|---------|
| Migrations | `migrate` ou `migrate:fresh` (`--fresh`) |
| `scrapping:setup` | Types (`TypeSeeder`), caractéristiques, pivots, mappings DofusDB / scrapping, effets |
| Seeders pages | `UserSeeder`, `CriticalPagesSeeder`, `NavMenuSeeder`, `PageSeeder`, `SectionSeeder`, `SubEffectSeeder` |
| Référentiels jeu | `LanguageSeeder`, `ConditionSeeder`, `CreatureTraitSeeder`, `CreationPagesSeeder` |
| Spécialisations | `SpecializationSeeder` (HTML sous `database/seeders/data/legacy-specializations/`) — désactivable avec `--skip-specializations` |
| Import règles | `project:data:import-rules-toc` |
| Capacités | `capabilities:import-legacy` sur `database/seeders/data/capability.json` (absent = ignoré) — **avant** les appels DofusDB lourds |
| Types DofusDB | `scrapping:types:seed`, `scrapping:races:seed`, `SpellTypeSeeder` |
| Scrapping entités | classes, sorts, monstres, ressources, consommables, items, panoplies (`scrapping:run`) — **en dernier** avant le scheduler |

Pour un `db:seed` classique global, voir aussi {@see \Database\Seeders\DatabaseSeeder} : il reprend une partie des mêmes seeders ; `project:init` ajoute scrapping, types API, import legacy spécialisations et TOC.

| Option | Effet |
|--------|--------|
| `--verify` | En fin de pipeline : `project:init:verify` (code 1 si socle incomplet). |
| `--verify-with-rules` | Comme `--verify` + contrôle des pages CMS `regles-*`. |

---

## `project:init:verify` — contrôle du socle seedé

Vérifie qu’une base initialisée (ou `project:seed`) est **utilisable sans scrapping complet**. Les **avertissements** (super_admin, breeds, sorts sous seuil) n’échouent pas la commande ; les **échecs** renvoient le code 1.

| Contrôle | Échec si… |
|----------|-----------|
| Tables `pages`, `users`, `item_types`, `characteristics`, `dofusdb_effect_mappings` | table absente |
| Pages `accueil`, `legales`, `cgu`, `changelog` | slug manquant |
| Page accueil | sans section, `in_menu=false`, ou non `playable` |
| Menu | `config/nav_menu.php` → `bibliotheques` vide ou entrée sans `route` / `entity_key` |
| Types équipement | &lt; 5 lignes `item_types` |
| Caractéristiques | &lt; 50 maîtres ou &lt; 50 pivots par groupe (creature / object / spell) |
| Mappings effets | table `dofusdb_effect_mappings` vide → `DofusdbEffectMappingSeeder` |
| `--with-rules` | &lt; 5 pages slug `regles-%` (import TOC) |
| `--min-spells=N` | (avertissement) sorts &lt; N |
| super_admin / breeds | (avertissement) optionnel selon scrapping |

| Option | Effet |
|--------|--------|
| `--with-rules` | Exige au moins 5 pages `regles-*` (import TOC). |
| `--min-spells=N` | Avertit si moins de N sorts en base. |
| `--json` | Sortie JSON (CI). |

```bash
php artisan project:init:verify
php artisan project:init:verify --with-rules --json
php artisan project:init --skip-scrapping --skip-types --verify
```

---

## `project:seed` — données locales sans DofusDB

Équivalent à **`project:init --skip-scrapping --skip-types`** : migrations, `scrapping:setup`, seeders pages/référentiels, import TOC règles, capacités legacy, sync pages bibliothèque — **sans** `scrapping:types:seed`, `scrapping:races:seed` ni `scrapping:run`.

| Option | Effet |
|--------|--------|
| `--fresh` | `migrate:fresh` avant seed |
| `--skip-migrate` | Ne pas migrer |
| `--skip-capabilities` | Pas d’import `capability.json` |
| `--skip-specializations` | Pas de `SpecializationSeeder` |
| `--init-scheduler` | Affiche la ligne cron (comme `project:init`) |

```bash
php artisan project:seed
php artisan project:seed --fresh
php artisan project:refresh --fast --force   # réinit locale rapide (migrate:fresh + seed local)
```

---

## `project:cron` — tâches planifiées

Point d’entrée pour le scheduler ou un cron système. **Sans option** : échec explicite (cron « vide » sûr).

| Option | Effet |
|--------|--------|
| `--clear` | Caches légers + rapports review + cache PHPStan |
| `--backup` | `project:backup` (voir options `backup-*`) |
| `--backup-prune-only` | Purge des anciennes sauvegardes |
| `--update` | `project:data:sync` (entités `auto_update`) |
| `--update-entity=` | Limite le sync (virgules) |
| `--update-dry-run` | Simulation sans écriture |

```bash
php artisan project:cron --clear
php artisan project:cron --backup
php artisan project:cron --clear --update --update-entity=monster
```

---

## `project:review` / `review` — rapport Markdown pour agent

Alias de **`dev:review`**. Le rapport est écrit en Markdown sous `storage/app/dev-reports/` par défaut.

### Mode profil (sans options d’action)

Argument optionnel : `tests`, `quality`, `security`, `docs`, `all` (défaut **`all`** si rien n’est passé).

- **`tests`** : PHPUnit + Vitest (`pnpm run test:run`).
- **`quality`** : PHPStan, Pint (`--test`), ESLint.
- **`security`** / **`docs`** : inchangés.
- **`all`** : tout le périmètre ci-dessus + audit Composer + contrôles doc.

### Mode actions (options, combinables)

Dès qu’une de ces options est présente, **l’argument profil est ignoré** (avertissement terminal).

| Option | Action |
|--------|--------|
| `--all` | Tout : tests back + front, PHPStan, Pint, ESLint, audit Composer, doc |
| `--tests` | `php artisan test` + `pnpm run test:run` |
| `--test-back` | PHPUnit uniquement |
| `--test-front` | Vitest uniquement (`pnpm run test:run`) |
| `--pint` | Laravel Pint en `--test` ; avec **`--fix-pint`**, application Pint (écriture) après la section Pint |
| `--pint-dirty` | Limite Pint aux fichiers modifiés Git (`pint --dirty`) : recommandé pendant une branche de fonctionnalité |
| `--pint-timeout=300` | Timeout Pint en secondes ; en cas de timeout, fallback automatique par lots de dossiers |
| `--no-pint-batches` | Désactive le fallback Pint par lots si le run global dépasse le timeout |
| `--phpstan` | Larastan / PHPStan |
| `--eslint` | `pnpm run lint` |
| `--security` | `composer audit` |
| `--docs` | Contrôles sur `docs/` (index JSON, fichiers d’entrée) |

**Interface super-admin (planification & reviews)** : après connexion en **super-admin interactif**, `/admin/project-schedule` pilote l’activation et les expressions cron (table `project_schedule_tasks`) ; `/admin/project-review` liste les rapports Markdown et enfile un job `RunProjectReviewJob` (worker requis). Commande `php artisan project:schedule:sync` pour injecter de nouvelles clés de tâches sans écraser les réglages existants.

`--test-back`, `--tests` et le profil **`tests`** lancent PHPUnit via **`php artisan test`** (suite entière si backend seul). Pour le périmètre *compte système (`is_system`) / super-admin interactif*, les fichiers à connaître sont regroupés dans le tableau *Tests PHPUnit* de [`app/Console/README.md`](../../app/Console/README.md). Ciblage rapide : `php artisan test --group=security`.

**Sans argument de profil et sans aucune de ces options d’action** : comportement identique au profil **`all`** (équivalent pratique à `--all`).

Options générales : `--report-path`, `--no-cursor-prompts`, `--fix-pint`, `--cursor-agent`.

### Workflow recommandé pour une nouvelle fonctionnalité

1. **Pendant le développement** : lancer une review ciblée sur les fichiers modifiés pour éviter que la dette historique Pint masque les vrais problèmes de la branche.

```bash
php artisan project:review --pint --pint-dirty --test-front --eslint
```

2. **Avant de demander une review complète** : lancer le périmètre large. Si Pint est trop long, le fallback par lots produit un rapport exploitable au lieu d’un simple timeout.

```bash
php artisan project:review --all --pint-timeout=900
```

3. **Pour corriger le style PHP de la branche uniquement** : appliquer Pint en mode dirty, puis relancer `--pint --pint-dirty`.

```bash
php artisan project:review --pint --pint-dirty --fix-pint
```

```bash
php artisan project:review
php artisan project:review --pint
php artisan project:review --pint --pint-dirty
php artisan project:review --pint --pint-timeout=900
php artisan project:review --test-back --phpstan
php artisan project:review --tests
php artisan project:review --all
php artisan review tests --report-path=storage/app/dev-reports/dernier-run.md
```

---

## `project:super-admin`

Crée le **premier** super_admin interactif si aucun super_admin humain n’existe. Utile hors `project:init`.

---

## Roadmap / améliorations possibles

1. **`project:data fill`** : implémenter un service « catalogue DofusDB vs `dofusdb_id` » par entité (pagination API, batch `scrapping:run`), avec option `--update` pour enchaîner un `sync`.
2. ~~**`run`**~~ : commande supprimée — équivalents **`project:*`** (voir tableau ci-dessous).
3. **Tests** : UI admin sync / backup / mise à jour stack — `tests/Feature/Admin/ProjectMaintenanceControllerTest.php`, `ProjectBackupWebControllerTest.php`, `ProjectDepsWebControllerTest.php` ; smoke `project:clear` — `tests/Feature/Console/ProjectClearCommandTest.php` ; service — `tests/Unit/Services/Project/ProjectRunServiceTest.php`.

---

## Table de correspondance (ancien → nouveau)

| Ancien usage | Préféré |
|--------------|---------|
| `project:update` | `project:data:sync` ou `project:data sync` |
| `run --update:all --migrate` (stack + migrate) | `project:deps` puis `project:prepare` si besoin de migrate/CSS/doc |
| `run --prepare` puis `run --dev` | `project:dev` (prepare + optimize + serveurs) ou `project:prepare` seul |
| `run --clear:*` / `--kill` | `project:clear` |
| `run --optimise:*` | `project:optimize` |
| `run --reset:*` | `project:reset` |
| `run --update:privilege=` | `project:fix-permissions {user}` |
| Effets (quality / pipeline) | `project:effects` |
| Création super admin seul | `project:super-admin` |

Référence complète des commandes Artisan : `app/Console/README.md`.
