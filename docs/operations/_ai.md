# Operations — IA

> Commandes projet : [../../app/Console/COMMANDS.md](../../app/Console/COMMANDS.md) + `App\Console\CommandGuide`.

Confirmations CLI : `-y` / `--yes` accepte, `--no` refuse. `-n` = `--no-interaction` (Symfony). Helper : `App\Console\YesNoFlags`.

`composer run dev` = `php artisan project:dev --queue`. Super-admin : liste `CommandGuide::forUi()` sur `/admin/recap` (liens vers les pages existantes).

## Fichiers pivots

- `app/Console/COMMANDS.md` — vocabulaire CLI unique
- `php artisan ia:equipment-grid` — grille d’équipements JDR (rapport ; `--write` = trous `draft`)
- `php artisan ia:convert {spell|encounter|npc|item|consumable}` — conversion IA → `auto` (clé Anthropic, Http fake en tests). Alias rencontre : `ia:convert-encounter`.
- `php artisan ia:npc-kit-catalog` — pré-filtre compact PNJ (équipement, classes/spés, sorts par classe, gabarit 5.1.2)
- `php artisan ia:creation-guides` — fiches de bonne pratique Création (prompt conversion), sans LLM
- `php artisan breeds:sync-images` — aligne les visuels des classes sur `storage/app/public/images/breeds/{slug}/`
- `php artisan entities:recalculate-prices {items|consumables}` — réécrit `price_calculated`, vide `price_custom` (consommables `playable` exclus) ; bouton admin sur `/admin/content`
- `php artisan items:seeder-export` / `items:seeder-import` — aller-retour CLI étalons d’équipement base ↔ `database/seeders/data/entities/items/` (pas d’UI) ; export défaut `auto`, `--versioned` = auto + déjà versionnés ; inclus dans `project:backup` hors prod
- `app/Console/Commands/Project/ProjectInitCommand.php`
- `app/Console/Commands/Pages/PagesImportRulesTocCommand.php`
- `app/Console/Commands/Rules/RulesCompileDownloadsCommand.php`
- `app/Services/Rules/`
- `app/Console/Commands/Project/ProjectClearOrphanFilesCommand.php`
- `app/Console/Commands/Effects/ConditionsRemapCanonicalCommand.php`
- `app/Console/Commands/Scrapping/`
- `app/Services/Media/OrphanPublicMediaCleanupService.php`
- `app/Support/ProjectSchedule/ProjectScheduleCatalog.php`
- `app/Models/ProjectConsoleJob.php`, `app/Services/Project/ProjectConsoleJobTracker.php`

## Chemins importants

- Disque public versionné : `storage/app/public/` sauf `images/entity/`, `images/users/` et `downloads/generated/`. Lien web : `php artisan storage:link` (`public/storage` non versionné). Fichier public manquant sous `/storage/…` → route `storage.local` (disque `private`, `serve: true`) → **403**, pas 404.
- Source règles CMS : `private/game/rules/TABLE_DES_MATIERES.md`. Chapitre 5 → menu **Pour les MJ** (`read_level` MJ). `rules:compile-downloads` : PDF/ODT joueur (ch. 1–4) + **L’Essentiel** (seed `essential-pages.php`, public) + atelier MJ (ch. 5, disque `local` privé — pas `/storage/…`). Changelog hors livre (page Informations). Bouton admin `/admin/content` : file `rules-downloads` + worker ponctuel.
- UI orphelins : `/admin/orphan-files` (super_admin).
- UI nettoyage caches : `/admin/project-clear` (super_admin).
- Backup : `project:backup` → ZIP v2 horodaté (BDD + `storage/app` + `private/game` + manifeste/checksums) ; CLI `project:backup:list|delete|restore` ; (hors prod) export seeders optionnel (`SeederDataExportService`) ; rétention 30 j ; cron `project_backup` off par défaut + `withoutOverlapping` ; UI `/admin/backup` (liste/suppr/restore + panneau progression) ; file `backup` timeout 7200 s ; verrou fichier global ; restore auto sécurisé (secours + maintenance) ; poll `admin/backup/restore-status` exempté du mode maintenance ; `ProjectBackupRecovery` réconcilie crash/reboot (PID mort → `interrupted`, cleanup, `artisan up`) — [README.md](README.md#sauvegardes-projectbackup).
- Seed mode : `App\Support\Seeder\SeedMode` + `config/seeders.php` — défaut création seule ; `project:seed --overwrite` / `SEED_OVERWRITE=true` pour réappliquer les fichiers.
- Files : `App\Support\Queue\ProjectQueues` — `notifications`, `backup`, `scrapping`, `ia`, `rules-downloads`, `maintenance`, `privacy`, `media`. Un `JobQueued` démarre un worker de cette file (`ProjectConsoleQueueKicker`), sans `queue:listen` permanent. `project:dev --queue` écoute toute la liste.
- Secrets push : [SECRET_SCAN.md](SECRET_SCAN.md) — `.githooks/pre-push`, `pnpm run secrets:scan`, CI gitleaks.- UI atelier DofusDB : `/admin/content/dofusdb` (admin, 3 modes Récupérer / Mettre à jour / Compléter, `?mode=`). Cartes + IA sur `/admin/content`. Cron `project_data_sync` inchangé.
- Jobs console admin : un actif max par domaine ; un `queued` sans démarrage > 15 min est abandonné ; poll `GET /admin/console-jobs/{uuid}` ; annulation `POST /admin/console-jobs/{uuid}/cancel` (file Laravel retirée si encore queued) ; toast fermable ; log filtré. Files : backup `backup`, sync DofusDB `scrapping`, clear/deps/review/prix/orphelins `maintenance`, règles `rules-downloads`.
