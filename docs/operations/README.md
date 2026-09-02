# Operations

Commandes métier hors recettes CLI quotidiennes. Vocabulaire Artisan : [app/Console/COMMANDS.md](../../app/Console/COMMANDS.md).

## Import des règles CMS

`php artisan pages:import-rules-toc` importe `private/game/rules/TABLE_DES_MATIERES.md` vers les pages règles. Appelé par `project:init` / `project:seed`. `--compile-downloads` enchaîne la compilation PDF/ODT.

```bash
php artisan pages:import-rules-toc --dry-run
php artisan rules:compile-downloads
```

Le livre compilé vit dans `storage/app/public/downloads/generated/` (non versionné). Téléchargement public : `/telechargements/{key}`. Page CMS **Ressources** (`ressources-de-jeu`) dans le menu Règles. Bouton admin : `/admin/content`.

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

## Notifications de jobs

Jobs Artisan admin (review, clear, deps, backup, `project:data sync`) : table `project_console_jobs`, poll `GET /admin/console-jobs/{id}`, toast animé + log filtré sur la page. Un seul job actif par domaine. Imports scrapping et nettoyage orphelins : suivi persisté (progression, annulation). Backup / sync planifiée : notification de résultat admin en plus du suivi live.

## Planification

Le serveur lance `php artisan schedule:run` chaque minute. Les tâches viennent d’un catalogue fixe (`ProjectScheduleCatalog`), pas d’une commande libre. Réglages : `/admin/project-schedule` (commande Artisan affichée + lien vers la page thématique).

Tâches : digests, RGPD, `project:data sync` (`/admin/content/dofusdb`), scrapping ressources, `project:backup` (`/admin/backup`), orphelins Media (`/admin/orphan-files`), `project:clear --safe` (`/admin/project-clear`).
