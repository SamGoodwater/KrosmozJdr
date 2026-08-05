# Scrapping DofusDB — carte IA (degré 1a)

> Import des données depuis l'API DofusDB vers les entités KrosmozJDR. Pipeline config-driven en 4 étapes (Collecte → Conversion → Validation → Intégration), avec preview, jobs asynchrones et registres de types. Réservé aux admins.

## Quand lire ce nœud

- Importer/mettre à jour des entités depuis DofusDB.
- Modifier une règle de mapping (champ source → champ Krosmoz), un formatter, ou un mapping d'effet/caractéristique.
- Travailler sur le dashboard `/scrapping`, les jobs, ou les registres de types.

## Concepts clés

- **Pipeline** : `Collect` (HTTP + cache) → `Conversion` (mapping + formatters) → `Validation` (limites caractéristiques) → `Intégration` (écriture BDD + relations + images). Détail : [README](./README.md#pipeline).
- **Config-driven** : sources JSON dans `resources/scrapping/` ; règles de mapping runtime en **BDD** (`scrapping_entity_mappings` + targets). Détail : [README](./README.md#ou-modifier-le-mapping).
- **Jobs async** : import long via `app/Jobs/ProcessScrappingJob.php` (table `scrapping_jobs`).
- **Mappings spécialisés** : effets de sorts → `dofusdb_effect_mappings` ; bonus objets → `characteristic_object.dofusdb_characteristic_id`.
- **Conversion paramétrable** : les valeurs numériques passent par `convertCharacteristic` + `characteristic_key` → `conversion_formula` → limites. Les diagnostics conservent les cas à revoir.
- **Monstres** : niveau 1–30, caractéristiques principales 6–30, PA 3–14, PM 2–10, PO 0–10 ; résistances relatives par paliers `-100/-50/0/50/100`, sans conversion automatique vers les résistances fixes.
- **Objets** : bonus/malus signés et bornes symétriques (caractéristiques ±6, PA ±5, PM ±2 hors forgemagie) ; résistances relatives converties uniquement sur les panoplies en paliers `-2/-1/0/1/2`.
- **Audit** : `php artisan scrapping:audit` valide le socle sans écriture ; `runMany()` retourne des résultats partiels par entité.
- **Sécurité** : middleware `role:admin` + `password.confirm` sur toutes les routes ; porte `ConfirmPasswordModal` sur la page.
- **CLI** : `php artisan scrapping:setup` (socle) puis `scrapping:run` (exploitation).

## Fichiers pivots

- `app/Services/Scrapping/Core/Orchestrator/Orchestrator.php` + `ScrappingPipelineFactory.php` — assemblage du pipeline.
- `app/Services/Scrapping/Core/Collect/CollectService.php` + `app/Services/Scrapping/Http/DofusDbClient.php` — collecte API.
- `app/Services/Scrapping/Core/Conversion/ConversionService.php` (+ `FormatterApplicator.php`, `ItemEffectsToBonusConverter.php`, `SpellEffects/SpellEffectsConversionService.php`) — conversion.
- `app/Services/Scrapping/Core/Integration/IntegrationService.php` — écriture en BDD.
- `app/Services/Scrapping/Core/Config/ConfigLoader.php` + `ScrappingMappingService.php` — config + mapping.
- `app/Jobs/ProcessScrappingJob.php` — exécution asynchrone.
- `routes/api/scrapping.php` — endpoints (search, preview, jobs, import, registries, catalogues).
- `resources/js/Pages/Pages/scrapping/Index.vue` + `resources/js/Composables/scrapping/*` — UI admin.
- `config/scrapping.php`, `resources/scrapping/config/` — configuration.

## Descendre

- [README humain](./README.md) — pipeline détaillé, où modifier le mapping, commandes, UI.
- Doc existante (L2) : `docs/features/scrapping/README.md` (notamment `PIPELINE_ET_MAPPING.md`, `Architecture/`, `EFFECTS_SYSTEM.md`).
