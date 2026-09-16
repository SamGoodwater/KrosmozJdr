# Operations — IA

> Commandes projet : [../../app/Console/COMMANDS.md](../../app/Console/COMMANDS.md) + `App\Console\CommandGuide`.

Confirmations CLI : `-y` / `--yes` accepte, `--no` refuse. `-n` = `--no-interaction` (Symfony). Helper : `App\Console\YesNoFlags`.

`composer run dev` = `php artisan project:dev --queue`. Super-admin : liste `CommandGuide::forUi()` sur `/admin/recap` (liens vers les pages existantes).

## Fichiers pivots

- `app/Console/COMMANDS.md` — vocabulaire CLI unique
- `php artisan ia:equipment-grid` — grille d’équipements JDR (rapport ; `--write` = trous `draft`)
- `php artisan ia:convert {spell|encounter|npc|item|consumable}` — conversion IA → `auto` (clé Anthropic, Http fake en tests). Alias rencontre : `ia:convert-encounter`.
- `php artisan ia:npc-kit-catalog` — pré-filtre compact PNJ (équipement, sorts, gabarit 5.1.2)
- `php artisan ia:creation-guides` — fiches de bonne pratique Création (prompt conversion), sans LLM
- `php artisan breeds:sync-images` — aligne les visuels des classes sur `storage/app/public/images/breeds/{slug}/`
- `php artisan entities:recalculate-prices {items|consumables}` — réécrit `price_calculated`, vide `price_custom` (consommables `playable` exclus) ; bouton admin sur `/admin/content`
- `php artisan items:seeder-export` / `items:seeder-import` — aller-retour étalons d’équipement base ↔ `database/seeders/data/entities/items/` ; même code que `Database\Seeders\Entity\ItemSeeder` ; l’import marque aussi jouables les ressources des recettes (plancher 1 kama) ; `ConsumableSeeder` rejoue l’échelle de soins hors combat, les parchemins de caractéristique (respec) et les utilitaires (`utility-playable.json`) ; `ClassBreedSeeder` pose les 19 classes avant `CapabilitySeeder` / `MonsterSeeder` / `SpellSeeder` (24 sorts / classe : `*-level-1.json` + `*-progression.json` ; 19 invocations `class-summons.json` ; 28 monstres JDR `incarnam.json` + `amakna.json`) puis `SpecializationSeeder` et `NpcSeeder` (5 PNJ Incarnam) ; boutons super_admin sur `/admin/content/ia-generation`
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
- Source règles CMS : `private/game/rules/TABLE_DES_MATIERES.md`. Livre PDF/ODT : `php artisan rules:compile-downloads` (bouton admin `/admin/content` : file `rules-downloads` + worker ponctuel, pas besoin d’un `queue:listen` déjà lancé).
- UI orphelins : `/admin/orphan-files` (super_admin).
- UI nettoyage caches : `/admin/project-clear` (super_admin).
- UI atelier DofusDB : `/admin/content/dofusdb` (admin). Cron `project_data_sync` inchangé.
- Jobs console admin : un actif max par domaine ; un `queued` sans démarrage > 15 min est abandonné ; poll `GET /admin/console-jobs/{uuid}` ; annulation `POST /admin/console-jobs/{uuid}/cancel` (file Laravel retirée si encore queued) ; toast fermable ; log filtré.
