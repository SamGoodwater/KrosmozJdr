# Operations

Commandes métier hors recettes CLI quotidiennes. Vocabulaire Artisan : [app/Console/COMMANDS.md](../../app/Console/COMMANDS.md).

## Grille d’équipements JDR

`php artisan ia:equipment-grid` dresse la couverture niveau × slot × voie. `--write` crée les cases vides en `draft` (interdit en production, jamais `playable`). Config : `resources/ia/equipment-grid.json`. Détail : [docs/IA/CATALOGUE.md](../IA/CATALOGUE.md).

## Recalcul des prix (kamas)

`php artisan entities:recalculate-prices {items|consumables}` réécrit `price_calculated` et vide `price_custom` (consommables jouables exclus). Bouton admin sur `/admin/content` (cartes Équipements et Consommables). Les ressources ne sont pas concernées (prix Dofus).

## Étalons d’équipement versionnés

Les objets relus à la main vivent en JSON sous `database/seeders/data/entities/items/` (un fichier par item), pour pouvoir reconstruire le socle jouable sur n’importe quelle base.

```bash
php artisan items:seeder-export            # base → fichiers (playable, dev uniquement)
php artisan items:seeder-export --all --prune
php artisan items:seeder-import --dry-run  # fichiers → base
php artisan items:seeder-import
```

`Database\Seeders\Entity\ItemSeeder` rejoue ces fichiers dans `project:seed` / `project:init`. Upsert sur `dofusdb_id` (`official_id` à défaut), type résolu par `item_type_dofus_id`, `image` exclu. Les ressources des recettes passent en `playable` (`ResourceSeeder`, plancher 1 kama). `ConsumableSeeder` rejoue l’échelle de soins hors combat, les parchemins de caractéristique (respec) et les utilitaires. `SpellSeeder` rejoue le kit Iop niveau 1. Boutons super administrateur : `/admin/content/ia-generation`. Format : [database/seeders/data/README.md](../../database/seeders/data/README.md).

## Import des règles CMS

`php artisan pages:import-rules-toc` importe `private/game/rules/TABLE_DES_MATIERES.md` vers les pages règles. Appelé par `project:init` / `project:seed`. `--compile-downloads` enchaîne la compilation PDF/ODT.

```bash
php artisan pages:import-rules-toc --dry-run
php artisan rules:compile-downloads
```

Le livre compilé vit dans `storage/app/public/downloads/generated/` (non versionné). Téléchargement public : `/telechargements/{key}`. Page CMS **Ressources** (`ressources-de-jeu`) dans le menu Règles. Bouton admin : `/admin/content` (file dédiée `rules-downloads` ; un worker ponctuel est lancé avec le bouton, un `queue:listen` persistant n’est pas requis).

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
- `downloads/generated/` — PDF/ODT du livre de règles (régénérés par `rules:compile-downloads`)

Le lien web `public/storage` n’est pas versionné : le recréer avec `php artisan storage:link`.

Un fichier **absent** de ce lien n’est pas servi en 404. La requête atteint la route framework `storage.local` (`GET storage/{path}`), qui lit le disque **privé** (`storage/app/private`, `serve: true` dans `config/filesystems.php`) et répond 403. Les URL `/storage/…` du front (favicon, fonds, icônes) doivent donc cibler des fichiers réellement présents.

## Notifications de jobs

Jobs Artisan admin (review, clear, deps, backup, `project:data sync`) : table `project_console_jobs`, poll `GET /admin/console-jobs/{id}`, toast animé + log filtré sur la page. Un seul job actif par domaine. Imports scrapping et nettoyage orphelins : suivi persisté (progression, annulation). Backup / sync planifiée : notification de résultat admin en plus du suivi live.

## Planification

Le serveur lance `php artisan schedule:run` chaque minute. Les tâches viennent d’un catalogue fixe (`ProjectScheduleCatalog`), pas d’une commande libre. Réglages : `/admin/project-schedule` (commande Artisan affichée + lien vers la page thématique).

Tâches : digests, RGPD, `project:data sync` (`/admin/content/dofusdb`), scrapping ressources, `project:backup` (`/admin/backup`), orphelins Media (`/admin/orphan-files`), `project:clear --safe` (`/admin/project-clear`).
