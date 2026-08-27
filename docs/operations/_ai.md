# Operations — IA

> Commandes projet : [../../app/Console/COMMANDS.md](../../app/Console/COMMANDS.md) + `App\Console\CommandGuide`.

Confirmations CLI : `-y` / `--yes` accepte, `--no` refuse. `-n` = `--no-interaction` (Symfony). Helper : `App\Console\YesNoFlags`.

`composer run dev` = `php artisan project:dev --queue`. Super-admin : liste `CommandGuide::forUi()` sur `/admin/recap` (liens vers les pages existantes).

## Fichiers pivots

- `app/Console/COMMANDS.md` — vocabulaire CLI unique
- `app/Console/Commands/Project/ProjectInitCommand.php`
- `app/Console/Commands/Pages/PagesImportRulesTocCommand.php`
- `app/Console/Commands/Project/ProjectClearOrphanFilesCommand.php`
- `app/Console/Commands/Effects/ConditionsRemapCanonicalCommand.php`
- `app/Console/Commands/Scrapping/`
- `app/Services/Media/OrphanPublicMediaCleanupService.php`
- `app/Support/ProjectSchedule/ProjectScheduleCatalog.php`
- `app/Models/ProjectConsoleJob.php`, `app/Services/Project/ProjectConsoleJobTracker.php`

## Chemins importants

- Source règles CMS : `private/game/rules/TABLE_DES_MATIERES.md`.
- UI orphelins : `/admin/orphan-files` (super_admin).
- UI nettoyage caches : `/admin/project-clear` (super_admin).
- UI atelier DofusDB : `/admin/content/dofusdb` (admin). Cron `project_data_sync` inchangé.
- Jobs console admin : un actif max par domaine ; poll `GET /admin/console-jobs/{uuid}` ; annulation `POST /admin/console-jobs/{uuid}/cancel` (file Laravel retirée si encore queued) ; toast fermable ; log filtré.
