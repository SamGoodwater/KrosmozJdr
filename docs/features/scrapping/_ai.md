# Scrapping DofusDB — carte IA (degré 1a)

> Import des données depuis l'API DofusDB vers les entités KrosmozJDR. Pipeline config-driven en 4 étapes (Collecte → Conversion → Validation → Intégration), avec preview, jobs asynchrones et registres de types. Réservé aux admins.

## Quand lire ce nœud

- Importer/mettre à jour des entités depuis DofusDB.
- Modifier une règle de mapping (champ source → champ Krosmoz), un formatter, ou un mapping d'effet/caractéristique.
- Travailler sur l’atelier `/admin/content/dofusdb`, les jobs, ou les registres de types.

## Concepts clés

- **Pipeline** : `Collect` (HTTP + cache) → `Conversion` (mapping + formatters) → `Validation` (limites caractéristiques) → `Intégration` (écriture BDD + relations + images). Détail : [README](./README.md#pipeline).
- **Config-driven** : sources JSON dans `resources/scrapping/` ; règles de mapping runtime en **BDD** (`scrapping_entity_mappings` + targets). Détail : [README](./README.md#ou-modifier-le-mapping).
- **Jobs async** : import long via `app/Jobs/ProcessScrappingJob.php` (table `scrapping_jobs`).
- **UI atelier** : `/admin/content/dofusdb` (admin, password.confirm). Anciennes URLs `/scrapping` et `/admin/project-maintenance` redirigent. Preset `project:data sync` (auto_update) disponible aux admins. « Gérer les types » ouvre `/admin/content/types/{kind}` (`show_in_catalog` + `allow_scrap`).
- **Mappings spécialisés** : effets de sorts → `dofusdb_effect_mappings` ; bonus objets → `characteristic_object.dofusdb_characteristic_id`.
- **Conversion paramétrable** : les valeurs numériques passent par `convertCharacteristic` + `characteristic_key` → `conversion_formula` → limites. Les diagnostics conservent les cas à revoir. La réécriture JDR (simplifier un sort, générer un PNJ) n’est **pas** dans ce pipeline : cadrage [IA générative](../../IA/_ai.md).
- **Monstres** : niveau 1–30, caractéristiques principales 6–30, PA 3–14, PM 2–10, PO 0–10 ; résistances relatives par paliers `-100/-50/0/50/100`, sans conversion automatique vers les résistances fixes.
- **Objets** : bonus/malus signés et bornes symétriques (caractéristiques ±6, PA ±5, PM ±2 hors forgemagie) ; résistances relatives converties uniquement sur les panoplies en paliers `-2/-1/0/1/2`.
- **Audit / gates** : `scrapping:audit` valide le socle ; `scrapping:run` active la gate pré-import par défaut (hors simulate / `--no-quality-gate`) ; après import `spell`, `scrapping:effects:quality-gate` (`--allow-empty` si `--id`/`--ids`). Checklist mass scrap : [SERVER_MASS_SCRAP.md](./SERVER_MASS_SCRAP.md).
- **Sécurité** : middleware `role:admin` + `password.confirm` sur `/api/dofusdb/*` (masse ; ancien préfixe `/api/scrapping` redirige en 307). Maj **unitaire** MJ+ : `POST /api/entities/{type}/{id}/dofusdb-refresh` (id local, policy `update`, throttle, pas de password.confirm) ; refusée si le type / la race a `allow_scrap=false`.
- **CLI** : `php artisan scrapping:setup` (socle) puis `scrapping:run` (exploitation). Masse sans `--id`/`--typeId` : `--type-mode=allowed` et `--race-mode=allowed` (défaut, `allow_scrap`). `--type-mode=all` / `--race-mode=all` pour tout récupérer. Liste vide = pas d’appel DofusDB.

## Fichiers pivots

- `app/Services/Scrapping/Core/Orchestrator/Orchestrator.php` + `ScrappingPipelineFactory.php` — assemblage du pipeline.
- `app/Services/Scrapping/Core/Collect/CollectService.php` + `app/Services/Scrapping/Http/DofusDbClient.php` — collecte API.
- `app/Services/Scrapping/Core/Conversion/ConversionService.php` (+ `FormatterApplicator.php`, `ItemEffectsToBonusConverter.php`, `SpellEffects/SpellEffectsConversionService.php`) — conversion.
- `app/Services/Scrapping/Core/Integration/IntegrationService.php` — écriture BDD ; état de sort : jeton `raw` + liaison vers le canon `playable` (`ConditionCanonicalMapper`).
- `app/Services/Scrapping/Core/Config/ConfigLoader.php` + `ScrappingMappingService.php` — config + mapping.
- `app/Jobs/ProcessScrappingJob.php` — exécution asynchrone.
- `routes/api/scrapping.php` — endpoints `/api/dofusdb` (search, preview, jobs, import, registries, catalogues). Noms de routes `scrapping.*`.
- `resources/js/Pages/Admin/Content/DofusdbWorkshop/Index.vue` + `resources/js/Composables/scrapping/*` — UI atelier admin.
- `config/scrapping.php`, `resources/scrapping/config/` — configuration.

## Descendre

- [README humain](./README.md) — pipeline détaillé, où modifier le mapping, commandes, UI.
- Doc existante (L2) : `docs/features/scrapping/README.md` (notamment `PIPELINE_ET_MAPPING.md`, `Architecture/`, `EFFECTS_SYSTEM.md`).
