# Commandes Artisan Krosmoz-JDR

Les commandes sont chargées **récursivement** depuis `app/Console/Commands/` (`App\Console\Kernel::commands()`). Chaque sous-dossier correspond à une **thématique** ; le namespace PHP est `App\Console\Commands\{Thématique}`.

## Planificateur (`Kernel::schedule`)

| Commande / job | Fréquence |
|----------------|-----------|
| `media:clean-thumbnails` | Quotidien |
| `privacy:process-deletion-requests` | Quotidien 02:00 |
| `SendNotificationDigestsJob` (daily / weekly / monthly) | Voir horaires dans `Kernel` |
| `project:data:sync` | Si `PROJECT_UPDATE_AUTO_ENABLED=true` (cron `PROJECT_UPDATE_CRON`) ; alias `project:update` |
| `scrapping` (alias de `scrapping:run`) | Si `SCRAPPING_RESOURCES_AUTO_SYNC=true` |

---

## Project — outillage projet & maintenance

**Spécification détaillée :** [docs/40-DevGuides/PROJECT_CLI.md](../../docs/40-DevGuides/PROJECT_CLI.md)

| Commande | Classe | Rôle |
|----------|--------|------|
| `project:deps` | `ProjectDepsCommand` | Stack : apt / composer / pnpm / CSS / doc / migrate (délègue à `run`). |
| `project:dev` | `ProjectDevCommand` | Préparation + serveurs dev (délègue à `run`). |
| `project:refresh` | `ProjectRefreshCommand` | `migrate:fresh`, option `setup --refresh`, clear caches. |
| `project:data` | `ProjectDataCommand` | `sync` \| `init` \| `fill` (orchestration données). |
| `project:data:sync` | `ProjectUpdateCommand` | Entités `auto_update=true` ; **alias** `project:update`. |
| `project:init` / `init` | `ProjectInitCommand` | Pipeline complet ; option `--deps` pour enchaîner `project:deps`. |
| `project:super-admin` | `ProjectSuperAdminCommand` | Création du premier super_admin (trait partagé avec `init`). |
| `setup` | `SetupCommand` | Install / update système (apt), deps, base MySQL, clean, refresh. |
| `run` | `ProjectRunCommand` | Boîte à outils bas niveau (kill, clear, update, dev, pipelines effets, …). |

**Exemples :**

```bash
php artisan project:deps
php artisan project:dev --prepare && php artisan project:dev
php artisan project:data sync --entity=monster
php artisan project:init --deps --fresh
php artisan project:super-admin
php artisan setup --db
php artisan run --update:all
```

---

## Scrapping — DofusDB & données catalogue

| Commande | Classe | Rôle |
|----------|--------|------|
| `scrapping:run` (`scrapping`) | `ScrappingRunCommand` | Import principal (monstres, items, sorts, ressources, etc.). Voir `--help`. |
| `scrapping:setup` | `ScrappingSetupCommand` | Socle scrapping (migrations + caractéristiques + mappings). |
| `scrapping:seeders:export` | `ScrappingSeedersExportCommand` | Export BDD → `database/seeders/data/`. |
| `scrapping:types:seed` | `ScrappingTypesSeedCommand` | Types item depuis l’API → BDD + seeders (`--only=resource,consumable,item`). |
| `scrapping:types:extract` | `ScrappingTypesExtractCommand` | Extraction types vers fichiers seeders. |
| `scrapping:types:migrate-items` | `ScrappingTypesMigrateItemsCommand` | Migration `item_type_id` (superTypes). |
| `scrapping:races:seed` | `ScrappingRacesSeedCommand` | Races monstres depuis DofusDB. |
| `scrapping:items:repair-routing` | `ScrappingRepairItemRoutingCommand` | Rattrapage items mal classés (resource/consumable). |
| `scrapping:effects:map` | `ScrappingEffectsMapCommand` | Propositions de mapping effets DofusDB → seeder. |
| `scrapping:effects:pipeline` | `ScrappingEffectsPipelineCommand` | Import sorts + quality gate effets. |
| `scrapping:effects:audit-quality` | `ScrappingEffectsQualityAuditCommand` | Audit qualité pipeline effets / conversions. |
| `scrapping:effects:audit-autre` | `ScrappingEffectsAutreAuditCommand` | Audit sous-effets « autre ». |
| `scrapping:effects:quality-gate` | `ScrappingEffectsQualityGateCommand` | Seuils CI (couverture, mappings manquants). |
| `scrapping:effects:report-missing-characteristics` | `ScrappingEffectsMissingCharacteristicsReportCommand` | Rapport mappings sans `characteristic_key`. |
| `scrapping:effects:backfill-characteristics` | `ScrappingEffectsBackfillCharacteristicsCommand` | Backfill `characteristic_key` sur mappings. |

**Note DRY :** les audits effets et la quality gate partagent le même domaine métier ; la gate s’appuie sur la logique d’audit (pas de duplication massive de code métier, plutôt des rapports CLI distincts).

---

## Effects — modèle effets Krosmoz (hors pipeline scrapping pur)

| Commande | Classe | Rôle |
|----------|--------|------|
| `effects:rebuild-signatures` | `EffectsRebuildSignaturesCommand` | Recalcule `config_signature` des degrés d’effet. |

---

## Media — Spatie Media Library & assets

| Commande | Classe | Rôle |
|----------|--------|------|
| `media:fix-storage-paths` | `MediaFixStoragePathsCommand` | Migration des chemins médias vers `MEDIA_PATH` des modèles. |
| `media:clean-thumbnails` | `CleanThumbnailsCommand` | Supprime les thumbnails anciens (`--older-than`, secondes). |
| `generate:IconsGenerator` | `GenerateIconsJsonCommand` | JSON des liens d’icônes (dossiers configurés). **Local / testing uniquement** (`GuardsProductionEnvironment`). |

---

## Privacy — RGPD

| Commande | Classe | Rôle |
|----------|--------|------|
| `privacy:process-deletion-requests` | `ProcessPrivacyDeletionRequestsCommand` | Traite les suppressions après délai de rétractation. |

---

## Pages — contenu CMS

| Commande | Classe | Rôle |
|----------|--------|------|
| `pages:import-rules-toc` | `PagesImportRulesTocCommand` | Crée / met à jour pages et sections depuis la TOC des règles. |

---

## Characteristics — caractéristiques & import legacy

| Commande | Classe | Rôle |
|----------|--------|------|
| `characteristics:generate-color-css` | `GenerateCharacteristicColorCssCommand` | Génère le CSS des couleurs (classes `.color-{key}`). |
| `characteristics:extract-creature-samples` | `ExtractCreatureConversionSamplesCommand` | Extrait samples conversion creature (monstres DofusDB) → JSON. |
| `characteristics:extract-object-samples` | `ExtractObjectConversionSamplesCommand` | Idem pour objets (équipements). |
| `capabilities:import-legacy` | `ImportLegacyCapabilitiesCommand` | Import capacités depuis export JSON PHPMyAdmin (ancienne base). |

---

## Development — outils locaux

Toutes utilisent `GuardsProductionEnvironment` : **refusées en production**.

| Commande | Classe | Rôle |
|----------|--------|------|
| `server:load` | `LoadDevelopmentServersCommand` | Lance les serveurs PHP / Node de dev. |
| `server:prepare` | `PrepareProjectCommand` | Migrations, autoload, ide-helper, meta IDE. |
| `generate:test {name}` | `GenerateTestCommand` | Esquisse de test PHPUnit Feature pour un nom de modèle. |
| `generate:fullmap` | `FusionMapsCommand` | Mosaïque `public/images/*.jpg` → `storage/app/public/maps/fullmap.jpg`. |

---

## Conventions

- **Suffixe `Command`** pour les classes (PSR / Laravel).
- **Signatures** : préfixes thématiques (`scrapping:`, `project:`, `media:`, etc.) pour `php artisan list` lisible.
- **Sécurité prod** : `App\Console\Concerns\GuardsProductionEnvironment` pour les commandes dev-only.

Pour la liste à jour : `php artisan list`.
