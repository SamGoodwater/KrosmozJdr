# Operations

Commandes métier hors recettes CLI quotidiennes. Vocabulaire Artisan : [app/Console/COMMANDS.md](../../app/Console/COMMANDS.md).

## Grille d’équipements JDR

`php artisan ia:equipment-grid` dresse la couverture niveau × slot × voie. `--write` crée les cases vides en `draft` (interdit en production, jamais `playable`). Config : `resources/ia/equipment-grid.json`. Détail : [docs/IA/CATALOGUE.md](../IA/CATALOGUE.md).

## Recalcul des prix (kamas)

`php artisan entities:recalculate-prices {items|consumables}` réécrit `price_calculated` et vide `price_custom` (consommables jouables exclus). Bouton admin sur `/admin/content` (cartes Équipements et Consommables). Les ressources ne sont pas concernées (prix Dofus).

## Étalons d’équipement versionnés

Les objets relus à la main vivent en JSON sous `database/seeders/data/entities/items/` (un fichier par item), pour pouvoir reconstruire le socle `auto` sur n’importe quelle base.

```bash
php artisan items:seeder-export            # base → fichiers (auto, dev uniquement)
php artisan items:seeder-export --all --prune
php artisan items:seeder-import --dry-run  # fichiers → base
php artisan items:seeder-import
```

`Database\Seeders\Entity\ItemSeeder` rejoue ces fichiers dans `project:seed` / `project:init`. Upsert sur `dofusdb_id` (`official_id` à défaut), type résolu par `item_type_dofus_id`, `image` exclu. Les ressources des recettes passent en `playable` (`ResourceSeeder`, plancher 1 kama). `ConsumableSeeder` rejoue l’échelle de soins hors combat, les parchemins de caractéristique (respec) et les utilitaires. `ClassBreedSeeder` pose les 19 classes (avant les sorts). `SpellSeeder` rejoue les 24 sorts de classe des 19 classes (`*-level-1.json` + `*-progression.json`). Export/import : CLI uniquement ; hors prod, `project:backup` (UI `/admin/backup`) réécrit aussi les seeders data. Format : [database/seeders/data/README.md](../../database/seeders/data/README.md).

## Import des règles CMS

`php artisan pages:import-rules-toc` importe `private/game/rules/TABLE_DES_MATIERES.md` vers les pages règles. Appelé par `project:init` / `project:seed`. Le chapitre 5 (équilibrage) va dans **Pour les MJ** (`read_level` MJ). `--compile-downloads` enchaîne la compilation PDF/ODT.

```bash
php artisan pages:import-rules-toc --dry-run
php artisan rules:compile-downloads
```

Le livre joueur compilé vit dans `storage/app/public/downloads/generated/` (non versionné). L’atelier MJ (`read_level` MJ) est sur le disque privé `storage/app/private/downloads/generated/` : `/storage/…` ne le sert pas. PDF A4, police resserrée, un saut de page par grande partie (pas par fiche). Deux livres : **joueur** (ch. 1–4, public) et **atelier MJ** (ch. 5, rôle MJ). Blocs Sources / Contenu et changelog exclus. Téléchargement : `/telechargements/{key}`. Pages CMS **Ressources** (`ressources-de-jeu`, menu Règles) et **Ressources MJ** (`ressources-mj`, Pour les MJ). Bouton admin : `/admin/content` (file dédiée `rules-downloads` ; un worker ponctuel est lancé avec le bouton, un `queue:listen` persistant n’est pas requis).

## Nettoyage des fichiers orphelins

`project:clear-orphan-files` inspecte les racines publiques MediaLibrary (`images/entity`, `images/users`, `images/uploads/entity-placeholders`, `sections`). Dry-run par défaut.

```bash
php artisan project:clear-orphan-files
php artisan project:clear-orphan-files --delete
php artisan project:clear-orphan-files --queue --delete
```

UI : `/admin/orphan-files` (super_admin). Service : `app/Services/Media/OrphanPublicMediaCleanupService.php`. Cron catalogue `media_clear_orphan_files` (off par défaut). Après déploiement : `php artisan project:schedule:sync`.

Le récapitulatif `/admin/recap` liste les commandes `ui: true` pour le super-admin, avec un lien vers chaque page thématique (pas un second lanceur).

## Disque public (`storage/app/public`)

Le contenu de `storage/app/public` est versionné (icônes, fonds, logos, légal, changelog, fonts…). Deux dossiers restent locaux :

- `images/entity/` — illustrations d’entités (scrapping, médias générés)
- `images/users/` — fichiers utilisateur
- `downloads/generated/` — PDF/ODT du **livre joueur** (régénérés par `rules:compile-downloads`). L’atelier MJ est sur le disque privé.

Le lien web `public/storage` n’est pas versionné : le recréer avec `php artisan storage:link`.

Un fichier **absent** de ce lien n’est pas servi en 404. La requête atteint la route framework `storage.local` (`GET storage/{path}`), qui lit le disque **privé** (`storage/app/private`, `serve: true` dans `config/filesystems.php`) et répond 403. Les URL `/storage/…` du front (favicon, fonds, icônes) doivent donc cibler des fichiers réellement présents.

## Notifications de jobs

Jobs Artisan admin (review, clear, deps, backup, `project:data sync`) : table `project_console_jobs`, poll `GET /admin/console-jobs/{id}`, toast animé + log filtré sur la page. Un seul job actif par domaine. Imports scrapping et nettoyage orphelins : suivi persisté (progression, annulation). Backup / sync planifiée : notification de résultat admin en plus du suivi live.

Chaque type de travail a sa file (`App\Support\Queue\ProjectQueues`) : `notifications`, `backup`, `scrapping`, `ia`, `rules-downloads`, `maintenance`, `privacy`, `media`. Déposer un job démarre un worker de cette file seule. Les notifications (maintenance, connexion, fiche modifiée) n’apparaissent dans le centre qu’après ce worker : avant, elles sont seulement dans la table `jobs`. `project:dev --queue` écoute toutes ces files.

## Sauvegardes (`project:backup`)

Archive ZIP unique horodatée (BDD + `storage/app` hors backups + `private/game`), manifeste v2 avec checksums SHA-256, inventaire / suppression / restauration (CLI + UI), rotation, cron. Hors production : peut aussi réécrire les fichiers de seed versionnés depuis la base. Vocabulaire CLI : [COMMANDS.md — project:backup](../../app/Console/COMMANDS.md#projectbackup). Services : `ProjectBackupService`, `ProjectBackupRestoreService`, `SeederDataExportService`. Config : `config/project-backup.php`.

**Périmètre** : base MySQL/MariaDB (dump SQL gzip) ou SQLite (copie compressée), médias et fichiers sous `storage/app`, contenu `private/game`. **Jamais inclus** : `.env`, code applicatif, `storage/framework`, `storage/logs`, ni `storage/app/backups` (même si `PROJECT_BACKUP_PATH` pointe ailleurs).

La base est la source de vérité ; les JSON sous `database/seeders/data/` en sont une copie. Au démarrage (`project:dev` / `project:prepare`), le seed ne crée que les lignes absentes. Pour réappliquer volontairement les fichiers : `project:seed --overwrite`.

Voir aussi : [SECRET_SCAN.md](./SECRET_SCAN.md) (hook pre-push + CI gitleaks).

| Élément | Détail |
|--------|--------|
| Fichier | `{prefix}_YYYY-MM-DD_HH-mm-ss_xxxx.zip` (ex. `project-backup_2026-10-05_04-00-00_1234.zip`) |
| Contenu ZIP | `manifest.json`, `database/mysql.sql.gz` ou `database/sqlite.db.gz`, `storage/app/…`, `private/game/…` |
| Seeders (hors prod) | `scrapping:seeders:export` + `items:seeder-export --versioned` ; `--no-seeder-data` pour skip (écritures dépôt, hors ZIP) |
| Répertoire | `PROJECT_BACKUP_PATH` (sous la racine projet) ou défaut `storage/app/backups` |
| Rétention | `PROJECT_BACKUP_RETENTION_DAYS` (défaut **30** j) ; purge par archive complète |
| Cron | clé catalogue `project_backup` ; seed `.env` : `PROJECT_BACKUP_ENABLED=false`, `PROJECT_BACKUP_CRON="0 4 * * *"` ; `withoutOverlapping(180)` |
| UI | `/admin/backup` : lancer, lister, supprimer, restaurer (super_admin + mot de passe) ; panneau de suivi restauration (fichier local, poll même pendant `artisan down`) |
| Crash / reboot | `ProjectBackupRecovery` : si le PID de restauration a disparu, statut `interrupted`, nettoyage `.staging_*` / `.restore_*`, levée auto du mode maintenance orphelin |
| Mémoire (WSL) | Pas de copie staging de `storage/app` / `private/game` : checksums + `ZipArchive::addFile` depuis la source ; staging limité au dump BDD + manifeste. Toujours exclure `storage/app/backups`. Les tests restore isolent `useStoragePath` hors du storage réel |
| CLI | `project:backup`, `project:backup:list`, `project:backup:delete`, `project:backup:restore` |
| Prérequis | `mysqldump` / `mysql` (MySQL/MariaDB) ; `schedule:run` chaque minute pour le cron ; protéger le répertoire |
| Legacy | anciennes paires `*_mysql.sql.gz` / `*_storage.*` encore listables et purgables ; **restauration auto réservée au ZIP v2** |

```bash
php artisan project:backup
php artisan project:backup --no-storage --no-game
php artisan project:backup --no-seeder-data
php artisan project:backup --prune-only --dry-run
php artisan project:backup:list
php artisan project:backup:delete project-backup_2026-10-05_04-00-00_1234.zip --yes
php artisan project:backup:restore project-backup_2026-10-05_04-00-00_1234.zip --yes
```

### Restauration

**Recommandé (ZIP v2)** : `project:backup:restore {name} --yes` ou bouton Restaurer sur `/admin/backup` (retaper le nom exact). Enchaîne : vérification manifeste/checksums → sauvegarde de secours → mode maintenance → restauration BDD + `storage/app` (répertoire backups préservé) + `private/game` → sortie maintenance. Verrou global fichier partagé avec les sauvegardes.

Après restore : `php artisan storage:link` si besoin, `project:clear --safe`, redémarrer queue / scheduler.

**Legacy (manuel)** — paires pré-ZIP, non restaurées par la commande :

```bash
gunzip -c BACKUP_DIR/project-backup_YYYYMMDD_HHMMSS_xxxx_mysql.sql.gz \
  | mysql -u"$DB_USERNAME" -p"$DB_PASSWORD" -h"$DB_HOST" "$DB_DATABASE"
tar -xzf BACKUP_DIR/project-backup_YYYYMMDD_HHMMSS_xxxx_storage.tar.gz -C storage/
```

## Planification

Le serveur lance `php artisan schedule:run` chaque minute. Les tâches viennent d’un catalogue fixe (`ProjectScheduleCatalog`), pas d’une commande libre. Réglages : `/admin/project-schedule` (commande Artisan affichée + lien vers la page thématique).

Tâches : digests, RGPD, `project:data sync` (`/admin/content/dofusdb`), scrapping ressources, `project:backup` (`/admin/backup`), orphelins Media (`/admin/orphan-files`), `project:clear --safe` (`/admin/project-clear`).
