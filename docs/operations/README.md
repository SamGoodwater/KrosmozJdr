# Operations

Commandes et opérations de maintenance du projet.

## Commandes importantes

- `php artisan project:init` : initialisation projet.
- `php artisan project:data:import-rules-toc` : importe les règles Markdown depuis `private/game/rules/TABLE_DES_MATIERES.md` vers le CMS.
- `php artisan project:clear-orphan-files` : liste les fichiers publics MediaLibrary sans référence en base. Dry-run par défaut ; ajouter `--delete` pour supprimer réellement les candidats ; `--queue` pour enfiler un job suivi (`MediaCleanupJob`).
- `php artisan project:schedule:sync` : ajoute en base les tâches planifiables manquantes depuis le catalogue sécurisé.
- `php artisan project:backup` : sauvegarde base + `storage/app`, purge les anciennes archives selon la rétention, et notifie les admins sauf `--skip-notify`.
- `php artisan pages:import-rules-toc` : commande bas niveau d'import des règles.
- `php artisan scrapping:setup` : initialise les données nécessaires au scrapping.
- `php artisan scrapping:run` : lance les imports DofusDB.
- `php artisan conditions:remap-canonical` : recolle les sorts sur les états JDR de base (Pesanteur, Empoisonné, …). `--dry-run` pour compter sans écrire.

## Nettoyage Des Fichiers Orphelins

La commande `project:clear-orphan-files` inspecte uniquement les racines publiques MediaLibrary connues (`images/entity`, `images/users`, `images/uploads/entity-placeholders`, `sections`). Elle compare les dossiers de fichiers avec la table `media` et signale les candidats sans référence.

Par sécurité, elle ne supprime rien sans `--delete` :

```bash
php artisan project:clear-orphan-files
php artisan project:clear-orphan-files --delete
php artisan project:clear-orphan-files --queue --delete
```

Les contenus publics légaux, changelog et calendrier sont explicitement ignorés.

### UI admin et job suivi

Les super-admins disposent de `/admin/orphan-files` (menu **Fichiers orphelins**) pour lancer un dry-run ou une suppression via file d’attente, suivre la progression et annuler. Le modèle `MediaCleanupJob` + `ProcessMediaCleanupJob` portent le cycle de vie (`queued` → `running` → `succeeded|failed|cancelled`). À la fin, les admins reçoivent une notification `project_maintenance` (`clear-orphan-files`) avec le nombre de fichiers traités, sauf `--skip-notify`.

Service : `app/Services/Media/OrphanPublicMediaCleanupService.php`. Dispatcher : `app/Services/Media/MediaCleanupDispatcher.php`.

Tâche cron catalogue `media_clear_orphan_files` (`project:clear-orphan-files --queue --delete`), **désactivée par défaut** (`MEDIA_CLEAR_ORPHAN_FILES_ENABLED`). Après déploiement : `php artisan project:schedule:sync`.

## Planification Cron

Le serveur doit lancer `php artisan schedule:run` chaque minute. Les tâches disponibles dans l’interface admin viennent d’un catalogue fixe, pas d’une commande libre. Après déploiement, exécuter `php artisan project:schedule:sync` pour ajouter les nouvelles clés sans écraser les réglages existants.

Tâches principales : digests de notifications, traitement RGPD, sync DofusDB, scrapping ressources autorisées, backup projet, nettoyage fichiers Media orphelins, et clear sûr `project:cron --clear`.

## Notifications De Jobs

Les imports scrapping asynchrones disposent d’un suivi interactif (`ScrappingJob`, cancel API, carte de notification).

Le nettoyage des fichiers orphelins dispose aussi d’un suivi persistant (`MediaCleanupJob`) avec progression et annulation depuis `/admin/orphan-files`.

Les autres jobs projet non interactifs (backup, sync planifiée, digests) utilisent des notifications de résultat admin plutôt qu’une carte persistante annulable.

## Contenus privés utilisés par commandes

- Règles JDR : `private/game/rules/`.
- Ressources de jeu : `private/game/resources/`.
