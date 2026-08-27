# Operations

Commandes métier hors recettes CLI quotidiennes. Vocabulaire Artisan : [app/Console/COMMANDS.md](../../app/Console/COMMANDS.md).

## Import des règles CMS

`php artisan pages:import-rules-toc` importe `private/game/rules/TABLE_DES_MATIERES.md` vers les pages règles. Appelé par `project:init` / `project:seed`.

```bash
php artisan pages:import-rules-toc --dry-run
```

## Nettoyage des fichiers orphelins

`project:clear-orphan-files` inspecte les racines publiques MediaLibrary (`images/entity`, `images/users`, `images/uploads/entity-placeholders`, `sections`). Dry-run par défaut.

```bash
php artisan project:clear-orphan-files
php artisan project:clear-orphan-files --delete
php artisan project:clear-orphan-files --queue --delete
```

UI : `/admin/orphan-files` (super_admin). Service : `app/Services/Media/OrphanPublicMediaCleanupService.php`. Cron catalogue `media_clear_orphan_files` (off par défaut). Après déploiement : `php artisan project:schedule:sync`.

## Notifications de jobs

Jobs Artisan admin (review, clear, deps, backup, `project:data sync`) : table `project_console_jobs`, poll `GET /admin/console-jobs/{id}`, toast animé + log filtré sur la page. Un seul job actif par domaine. Imports scrapping et nettoyage orphelins : suivi persisté (progression, annulation). Backup / sync planifiée : notification de résultat admin en plus du suivi live.

## Planification

Le serveur lance `php artisan schedule:run` chaque minute. Les tâches viennent d’un catalogue fixe (`ProjectScheduleCatalog`), pas d’une commande libre. Réglages : `/admin/project-schedule` (commande Artisan affichée + lien vers la page thématique).

Tâches : digests, RGPD, `project:data sync` (`/admin/content/dofusdb`), scrapping ressources, `project:backup` (`/admin/backup`), orphelins Media (`/admin/orphan-files`), `project:clear --safe` (`/admin/project-clear`).
