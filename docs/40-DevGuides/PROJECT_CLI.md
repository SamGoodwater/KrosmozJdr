# Interface CLI unifiée du projet (`project:*`)

Objectif : **un vocabulaire stable** (dépendances, dev, données, bootstrap) tout en **réutilisant** les implémentations existantes (`run`, `setup`, `project:init`, `scrapping:*`) pour rester **DRY**.

## Principes

| Préfixe | Rôle |
|---------|------|
| `project:deps` | Outils et librairies (apt, composer, pnpm), CSS, doc, migrations. |
| `project:dev` | Préparation locale + serveurs PHP / Vite. |
| `project:refresh` | Repartir de zéro (libs optionnelles + `migrate:fresh`). |
| `project:data` | Données DofusDB (sync, init catalogue, guide fill). |
| `project:data:sync` | Mise à jour des entités déjà en base avec `auto_update=true`. |
| `project:init` | Pipeline complet d’installation (migrations, seeders, types, scrapping, capacités). |
| `project:super-admin` | Création interactive du premier super_admin (hors `init`). |
| `project:backup` | Dump BDD (gzip) + archive `storage/app` (tar.gz, ZIP si besoin), purge > N jours. |

La commande historique **`run`** reste la **moteur bas niveau** (nombreuses options). Les `project:*` la **orchestrent** pour un usage quotidien plus lisible.

**Collision résolue :** l’ancien `project:update` (sync auto_update) devient la commande canonique **`project:data:sync`**. L’alias **`project:update`** est conservé (scheduler, scripts, habitude).

---

## `project:deps` — stack & build

Met à jour l’environnement de développement.

| Option | Effet (délègue à `run`) |
|--------|-------------------------|
| *(aucune option)* | Équivalent **`--all`** : apt + composer + pnpm + CSS + doc + dump-autoload + **migrate**. |
| `--all` | Idem. |
| `--apt` | `--update:system` |
| `--composer` | `--update:composer` |
| `--pnpm` | `--update:pnpm` |
| `--css` | `--update:css` |
| `--docs` | `--update:docs` |
| `--dump` | `--dump` |
| `--migrate` | `--migrate` |
| `--ide` | `--optimise:ide` |
| `--laravel-clear` | `--optimise:laravel` |

**Exemples**

```bash
php artisan project:deps              # tout (défaut)
php artisan project:deps --pnpm --css # ciblé
```

**Init + deps :** `php artisan project:init --deps` enchaîne `project:deps` puis le pipeline `init`.

---

## `project:dev` — développement

| Option | Effet |
|--------|--------|
| *(défaut)* | `run --dev` (serveur PHP + Vite optimisé). |
| `--prepare` | Prépare l’environnement (nettoyage, deps de base, optimisations, **migrate**) — en interne : `run --regenerate`. |
| `--migrate` | `run --migrate` uniquement. |
| `--watch` | `run --dev:watch` (watch CSS). |

---

## `project:refresh` — reset local

| Option | Effet |
|--------|--------|
| `--hard` | `setup --refresh` (reinstall vendor + node après clean). |
| `--without-seed` | `migrate:fresh` **sans** `--seed`. |
| `--force` | Pas de confirmation (CI). |

Enchaîne ensuite `run --clear:all`.

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

### Interface web (super admin)

Une page **Inertia** permet de lancer l’équivalent de **`project:data sync`** **sans bloquer le navigateur** : le travail est mis en file (`RunProjectDataSyncJob` → `Artisan::call('project:data', …)`).

| Élément | Détail |
|--------|--------|
| **URL** | `/admin/project-maintenance` (routes nommées `admin.project-maintenance.index` et `admin.project-maintenance.sync`) |
| **Accès** | Rôle **super_admin** uniquement ; entrée menu compte : **Sync données (DofusDB)** |
| **Mot de passe** | Middleware **`password.confirm`** sur **toute la zone** (GET + POST), même logique que les routes API scrapping (`RequirePasswordWithInactivity`, ré-vérification après timeout dans `config/auth.php`) |
| **POST `/sync`** | Validation stricte (`StoreProjectDataSyncRequest`), **throttle** dédié, journalisation `admin.project_maintenance.sync_dispatched` (identifiant utilisateur, IP, options sans secrets) |
| **Prérequis** | Un **worker de file** doit tourner (`php artisan queue:work`) ; un verrou cache évite deux syncs massives en parallèle |

Les options du formulaire (catalogue, entités, `--dry-run`, etc.) suivent les mêmes règles que la CLI décrites ci-dessus.

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

---

## `project:super-admin`

Crée le **premier** super_admin interactif si aucun super_admin humain n’existe. Utile hors `project:init`.

---

## Roadmap / améliorations possibles

1. **`project:data fill`** : implémenter un service « catalogue DofusDB vs `dofusdb_id` » par entité (pagination API, batch `scrapping:run`), avec option `--update` pour enchaîner un `sync`.
2. **Réduire `run`** : migrer progressivement les options rares vers des commandes dédiées, garder `run` comme compatibilité.
3. **Tests** : étendre les feature tests aux autres commandes `project:*` si besoin (voir `tests/Feature/Admin/ProjectMaintenanceControllerTest.php` pour l’UI sync).

---

## Table de correspondance (ancien → nouveau)

| Ancien usage | Préféré |
|--------------|---------|
| `project:update` | `project:data:sync` ou `project:data sync` |
| Mise à jour stack + migrate via `run --update:all --migrate` | `project:deps` |
| Préparer dev + serveurs via `run --regenerate` puis `run --dev` | `project:dev --prepare` puis `project:dev` |
| Création super admin seul | `project:super-admin` |

Référence complète des commandes Artisan : `app/Console/README.md`.
