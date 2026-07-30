# Operations

Commandes et opérations de maintenance du projet.

## Commandes importantes

- `php artisan project:init` : initialisation projet.
- `php artisan project:data:import-rules-toc` : importe les règles Markdown depuis `private/game/rules/TABLE_DES_MATIERES.md` vers le CMS.
- `php artisan project:clear-orphan-files` : liste les fichiers publics MediaLibrary sans référence en base. Dry-run par défaut ; ajouter `--delete` pour supprimer réellement les candidats.
- `php artisan project:schedule:sync` : ajoute en base les tâches planifiables manquantes depuis le catalogue sécurisé.
- `php artisan project:backup` : sauvegarde base + `storage/app`, purge les anciennes archives selon la rétention, et notifie les admins sauf `--skip-notify`.
- `php artisan pages:import-rules-toc` : commande bas niveau d'import des règles.
- `php artisan scrapping:setup` : initialise les données nécessaires au scrapping.
- `php artisan scrapping:run` : lance les imports DofusDB.

## Nettoyage Des Fichiers Orphelins

La commande `project:clear-orphan-files` inspecte uniquement les racines publiques MediaLibrary connues (`images/entity`, `images/users`, `images/uploads/entity-placeholders`, `sections`). Elle compare les dossiers de fichiers avec la table `media` et signale les candidats sans référence.

Par sécurité, elle ne supprime rien sans `--delete` :

```bash
php artisan project:clear-orphan-files
php artisan project:clear-orphan-files --delete
```

Les contenus publics légaux, changelog et calendrier sont explicitement ignorés.

## Planification Cron

Le serveur doit lancer `php artisan schedule:run` chaque minute. Les tâches disponibles dans l’interface admin viennent d’un catalogue fixe, pas d’une commande libre. Après déploiement, exécuter `php artisan project:schedule:sync` pour ajouter les nouvelles clés sans écraser les réglages existants.

Tâches principales : digests de notifications, traitement RGPD, sync DofusDB, scrapping ressources autorisées, backup projet, et clear sûr `project:cron --clear`.

## Notifications De Jobs

En v1, les notifications persistantes de jobs suivent uniquement les imports scrapping asynchrones, car ce sont les jobs interactifs utiles à surveiller depuis l’UI. Le modèle `ScrappingJob`, l’endpoint `/api/scrapping/jobs/{jobId}/cancel` et `ScrappingJobNotificationCard` couvrent les états `running`, `cancelling`, `success`, `error`, `cancelled`.

Les jobs projet non interactifs (backup, sync planifiée, digests) utilisent des notifications de résultat admin plutôt qu’une carte persistante annulable.

## Contenus privés utilisés par commandes

- Règles JDR : `private/game/rules/`.
- Ressources de jeu : `private/game/resources/`.
