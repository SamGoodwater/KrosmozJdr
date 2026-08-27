# Commandes projet

Source unique (CLI et UI super_admin sur le récapitulatif). Les blocs `yaml` sont lus par `App\Console\CommandGuide`.  
`ui: true` = listé sur `/admin/recap` (liens vers les pages existantes, pas un second lanceur). `ui: false` = local / bootstrap, jamais servi par l’UI.
`admin:` = chemin interne `/admin/...` de la page thématique.

Entrée officielle serveur : `php artisan project:dev`.  
Liste brute : `php artisan list` · aide : `php artisan <cmd> -h`.

Confirmations (IDE Helper, apt, refresh, permissions) :

- `-y` / `--yes` : accepter (écrase les modèles pour `ide-helper:models`).
- `--no` : refuser (écrit `_ide_helper_models.php`, annule apt / refresh / permissions).
- `-n` / `--no-interaction` : réservé par Symfony. Sans `-y`, IDE Helper n’écrase pas les modèles (équivalent du défaut `[no]`).

---

## `project:prepare`

```yaml
signature: project:prepare
domain: development
ui: false
cron: false
```

Rebuild CSS, vide les caches applicatifs et les vues, régénère les index Atomic et la doc, migrations (`setup --db`), puis pipeline IDE Helper + `optimize`.

```bash
php artisan project:prepare
php artisan project:prepare --clear
php artisan project:prepare -y
php artisan project:prepare --no
```

`--clear` : artefacts de tests (PHPUnit, coverage) avant la préparation. `-y` : écrase les PHPDoc des modèles. `--no` : écrit `_ide_helper_models.php`. Interdit en production.

---

## `project:dev`

```yaml
signature: project:dev
domain: development
ui: false
cron: false
```

`project:prepare` puis serveur Laravel (8000) + Vite. Option `--queue` : file `queue:listen` en plus. `--no-prepare` : serveurs seuls. `--watch` : watch CSS à la place de Vite. `--clear`, `-y` et `--no` : transmis à prepare.

```bash
php artisan project:dev
php artisan project:dev --queue
php artisan project:dev --no-prepare
php artisan project:dev -y
```

Interdit en production. `composer run dev` lance `php artisan project:dev --queue` (même entrée, file incluse). `--no-prepare` pour les serveurs seuls. `composer run dev:network` reste le helper 0.0.0.0.

---

## `project:deps`

```yaml
signature: project:deps
domain: software
ui: true
cron: false
admin: /admin/project-update
```

Met à jour Composer et pnpm, puis le pipeline IDE / `optimize`. `--with-system` : apt via `setup --update` avant. Cibles : `--composer`, `--pnpm`, `--apt`. Défaut / `--all` : composer + pnpm + optimize. `-y` / `--no` : confirmations apt et IDE Helper.

```bash
php artisan project:deps
php artisan project:deps --with-system
php artisan project:deps --composer
php artisan project:deps -y
```

Interdit en production. L’admin `/admin/project-update` enfile un job (hors prod, super_admin + mot de passe).

---

## `project:review`

```yaml
signature: project:review
domain: tests
ui: true
cron: false
admin: /admin/project-review
```

Rapport Markdown (tests, Pint, PHPStan, ESLint, audit Composer, doc). Profil : `tests`, `quality`, `security`, `docs`, `all`. Ou flags `--pint`, `--tests`, `--test-back`, `--test-front`, `--phpstan`, `--eslint`, `--security`, `--docs`, `--all`.

```bash
php artisan project:review
php artisan project:review --test-back --phpstan
php artisan project:review tests
```

Interdit en production. Rapport sous `storage/app/dev-reports/`. Admin : `/admin/project-review`.

---

## `project:data`

```yaml
signature: project:data
domain: data
ui: true
cron: true
admin: /admin/content/dofusdb
```

Synchronise le catalogue DofusDB (types / races) et les fiches déjà en base avec `auto_update=true`. N’importe pas de nouvelles fiches : pour ça, `scrapping:run` ou `project:init`.

```bash
php artisan project:data sync
php artisan project:data sync --entity=monster
php artisan project:data sync --type=all
php artisan project:data sync --type=all --entity=monster
php artisan project:data sync --dry-run --noimage
```

Sans `--type` / `--races` : sync des entités. Avec catalogue seul : pas de sync entités tant que `--entity` n’est pas passé. Cron : clé `project_data_sync`. Atelier : `/admin/content/dofusdb`.

---

## `project:init`

```yaml
signature: project:init
domain: bootstrap
ui: false
cron: false
```

Pipeline d’installation : migrations, seeders, import règles (`pages:import-rules-toc`), capacités, types DofusDB, scrapping. `--fresh`, `--skip-scrapping`, `--skip-types`, `--verify`, `--deps`. `-y` / `--no` : transmis à `project:deps` (IDE Helper, apt).

```bash
php artisan project:init
php artisan project:init --skip-scrapping --skip-types --verify
php artisan project:init --deps -y
```

Interdit en production.

---

## `project:seed`

```yaml
signature: project:seed
domain: bootstrap
ui: false
cron: false
```

Données locales sans DofusDB : `project:init --skip-scrapping --skip-types`.

```bash
php artisan project:seed
php artisan project:seed --fresh
```

---

## `project:refresh`

```yaml
signature: project:refresh
domain: bootstrap
ui: false
cron: false
```

Grand ménage local puis `project:init --fresh`. `--fast` : sans types ni scrapping. `--hard` : wipe vendor/node (`setup --refresh`) avant. `-y` : comme `--force` (pas de confirmation). `--no` : annule.

```bash
php artisan project:refresh --fast --force
php artisan project:refresh --fast -y
php artisan project:refresh --no
```

Destructif. Interdit en production.

---

## `project:init:verify`

```yaml
signature: project:init:verify
domain: bootstrap
ui: false
cron: false
```

Contrôle le socle après init/seed (pages, types, caractéristiques, mappings). `--with-rules`, `--json`.

```bash
php artisan project:init:verify --with-rules
```

---

## `project:clear`

```yaml
signature: project:clear
domain: cleanup
ui: true
cron: true
admin: /admin/project-clear
```

Caches et artefacts. `--safe` : caches Laravel + rapports review + cache PHPStan (preset cron / prod). `--all` : en local, nettoyage large (CSS généré, queue, debugbar) ; en prod, identique à `--safe`. Flags granulaires : `--cache`, `--config`, `--route`, `--view`, `--test`, `--reviews`, `--logs`, `--phpstan-cache`, `--backups`, `--kill`, `--css`, `--queue`, `--debugbar`, `--schedule`, `--event`, `--optimize`.

```bash
php artisan project:clear --safe
php artisan project:clear --cache
php artisan project:clear --all
```

Admin : `/admin/project-clear` (super_admin + mot de passe). Cron : clé `project_clear_safe` → `project:clear --safe`.

---

## `project:clear-orphan-files`

```yaml
signature: project:clear-orphan-files
domain: cleanup
ui: true
cron: true
admin: /admin/orphan-files
```

Fichiers publics MediaLibrary sans ligne `media`. Dry-run par défaut. `--delete` pour supprimer. `--queue` pour un job suivi.

```bash
php artisan project:clear-orphan-files
php artisan project:clear-orphan-files --delete --queue
```

Admin : `/admin/orphan-files`. Cron : `media_clear_orphan_files` (off par défaut).

---

## `project:backup`

```yaml
signature: project:backup
domain: backup
ui: true
cron: true
admin: /admin/backup
```

Dump BDD (gzip) + archive `storage/app`, purge selon rétention.

```bash
php artisan project:backup
php artisan project:backup --no-storage
php artisan project:backup --prune-only --dry-run
```

Admin : `/admin/backup`. Cron : `project_backup`.

---

## `project:schedule:sync`

```yaml
signature: project:schedule:sync
domain: backup
ui: false
cron: false
```

Ajoute en base les tâches du catalogue manquantes, sans écraser les réglages. Après déploiement : `php artisan project:schedule:sync`. Planning : `/admin/project-schedule` (commande Artisan + lien vers la page thématique).

---

## `project:super-admin`

```yaml
signature: project:super-admin
domain: bootstrap
ui: false
cron: false
```

Crée le premier super_admin humain si aucun n’existe (hors flux `project:init`).

---

## `project:fix-permissions`

```yaml
signature: project:fix-permissions
domain: development
ui: false
cron: false
```

`chown`/`chmod` du dépôt pour un utilisateur système. Interdit en production. `-y` : continuer si l’utilisateur cible n’est pas le compte courant. `--no` : annuler.

```bash
php artisan project:fix-permissions nom_utilisateur
php artisan project:fix-permissions nom_utilisateur -y
```

---

## `scrapping:run`

```yaml
signature: scrapping:run
domain: data
ui: false
cron: true
```

Import DofusDB (nouvelles fiches ou remplacement selon `--update-mode`). Le cron ressources autorisées appelle cette commande.

```bash
php artisan scrapping:run --entity=monster --limit=50
```

Qualité effets : `scrapping:effects:quality-gate`, `scrapping:effects:pipeline`. Socle types : `scrapping:setup`, `scrapping:types:seed`, `scrapping:races:seed`.

---

## `pages:import-rules-toc`

```yaml
signature: pages:import-rules-toc
domain: data
ui: false
cron: false
```

Importe `private/game/rules/TABLE_DES_MATIERES.md` vers les pages CMS. Appelé par `project:init` / `project:seed`.

```bash
php artisan pages:import-rules-toc --dry-run
```

---

## `media:clean-thumbnails`

```yaml
signature: media:clean-thumbnails
domain: cleanup
ui: false
cron: true
```

Supprime les vignettes Media Library trop anciennes. Cron : `media_clean_thumbnails`.

---

## `privacy:process-deletion-requests`

```yaml
signature: privacy:process-deletion-requests
domain: cleanup
ui: false
cron: true
```

Traite les demandes RGPD de suppression. Cron quotidien.
