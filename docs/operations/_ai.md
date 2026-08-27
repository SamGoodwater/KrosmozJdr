# Operations — IA

> Commandes projet : [../../app/Console/COMMANDS.md](../../app/Console/COMMANDS.md) + `App\Console\CommandGuide`.

Confirmations CLI : `-y` / `--yes` accepte, `--no` refuse. `-n` = `--no-interaction` (Symfony). Helper : `App\Console\YesNoFlags`.

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
- Planning : `/admin/project-schedule` (commande + lien page thématique).
- Jobs console admin : un actif max par domaine ; poll `GET /admin/console-jobs/{uuid}` ; log filtré.
