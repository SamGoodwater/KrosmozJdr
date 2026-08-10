# Operations — IA

> Commandes projet, imports et maintenance.

## Fichiers pivots

- `app/Console/Commands/Project/ProjectInitCommand.php`
- `app/Console/Commands/Project/ProjectDataImportRulesTocCommand.php`
- `app/Console/Commands/Project/ProjectClearOrphanFilesCommand.php`
- `app/Console/Commands/Pages/PagesImportRulesTocCommand.php`
- `app/Console/Commands/Scrapping/`
- `app/Services/Media/OrphanPublicMediaCleanupService.php`
- `app/Services/Media/MediaCleanupDispatcher.php`
- `app/Models/MediaCleanupJob.php`
- `app/Jobs/ProcessMediaCleanupJob.php`
- `app/Http/Controllers/Admin/ProjectOrphanFilesWebController.php`

## Chemins importants

- Source règles CMS : `private/game/rules/TABLE_DES_MATIERES.md`.
- Scrap massif serveur : `docs/features/scrapping/SERVER_MASS_SCRAP.md`.
- UI orphelins : `/admin/orphan-files` (super_admin).
- Cron : tâche `media_clear_orphan_files` (off par défaut).
