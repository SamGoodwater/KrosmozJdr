# État actuel du scrapping

Ce document décrit **ce qui existe** : le pipeline, les services, les configs et les points d’entrée du scrapping.

---

## 1. Pipeline

| Élément | Description |
|--------|-------------|
| **Orchestrator** | Enchaîne Collect → Conversion → Validation → Intégration. Utilisé par l’API (`ScrappingController`, `POST /api/scrapping/import/*`), le dashboard et la CLI `php artisan scrapping`. |
| **Formules / limites en BDD** | CharacteristicService (limites), DofusDbConversionFormulaService (formules), DofusDbConversionFormulas (level, life, attributs, ini, résistances). Handlers nommés (ex. résistances) + admin (sélection du handler). Le pipeline transmet un contexte (`entityType`, `lang`) à FormatterApplicator ; les formatters `dofusdb_level`, `dofusdb_life`, `dofusdb_attribute`, `dofusdb_ini` et le batch résistances sont utilisés pour **monster**. |

---

## 2. Services (Core)

| Composant | Fichier | Rôle |
|-----------|---------|------|
| **CollectService** | `app/Services/Scrapping/Core/Collect/CollectService.php` | Collecte pilotée par config JSON (endpoints, pagination, filtres, encodage Feathers). |
| **ConfigLoader** | `app/Services/Scrapping/Core/Config/ConfigLoader.php` | Charge les configs depuis `resources/scrapping/config/sources/dofusdb/`. |
| **ConversionService** | `app/Services/Scrapping/Core/Conversion/ConversionService.php` | Applique le mapping (propriété source → cible) + formatters (FormatterApplicator). |
| **FormatterApplicator** | `app/Services/Scrapping/Core/Conversion/FormatterApplicator.php` | Formatters : toString, pickLang, toInt, clampInt, truncate, dofusdb_level, dofusdb_life, dofusdb_attribute, dofusdb_ini, etc. |
| **ValidationService** | `app/Services/Characteristic/ValidationService.php` | Valide les données converties contre CharacteristicService (BDD). |
| **IntegrationService** | `app/Services/Scrapping/Core/Integration/IntegrationService.php` | Enregistre en base (ou simulation dry_run). |
| **Orchestrator** | `app/Services/Scrapping/Core/Orchestrator/Orchestrator.php` | Enchaîne Collect → Conversion → Validation → Intégration. |
| **RelationResolutionService** | `app/Services/Scrapping/Core/Relation/RelationResolutionService.php` | Résout les relations (sorts, drops) après intégration de l’entité principale (ex. monster). |

---

## 3. Configs

| Ressource | Emplacement | Contenu |
|-----------|-------------|---------|
| **Source** | `resources/scrapping/config/sources/dofusdb/source.json` | baseUrl, langue, http. |
| **Entités** | `resources/scrapping/config/sources/dofusdb/entities/*.json` | monster, spell, breed, item, item-type, monster-race, item-super-type. Endpoints (fetchOne, fetchMany), filtres, mapping (source → cible + formatter). **Classes (breed)** : DofusDB n’expose pas level, life ni attributs — uniquement noms, descriptions, illustrations, specificity, sorts liés (spell-levels), rôles. |
| **Alias collecte** | `resources/scrapping/config/collect_aliases.json` | Alias CLI (ex. classe → breed, ressource → item avec filtre). |
| **Item super types** | `resources/scrapping/config/sources/dofusdb/item-super-types.json`, `item-types.json` | Référence pour les types d’items. |

---

## 4. Points d’entrée

- **CLI** : `php artisan scrapping --import=monster --id=31 [--validate-only] [--dry-run] [--no-validate] [--json]` — utilise `Orchestrator::default()`. Validation activée par défaut ; `--no-validate` pour bypasser.
  - **Sortie** : `--output=raw|raw_useful|converted|verbose|summary` (avec `--json` pour le JSON). **raw** : données brutes DofusDB complètes (`entities[].items`, lourd, pour debug). **raw_useful** : uniquement les champs utiles à Krosmoz (valeurs extraites selon le mapping, sans le brut complet) dans `output_items[].raw_useful` ; `items` est réduit à des références légères (`dofusdb_id`). **converted** : données converties + validation_valid / validation_errors. **verbose** : par propriété Krosmoz, `raw_value`, `converted_value`, `valid`, `existing_value` (si entité en BDD). **summary** : comptes uniquement (collected, converted, validated, integrated).
  - **Filtres** : `--id`, `--ids`, `--name`, `--levelMin`, `--levelMax`, `--raceId`, `--breedId`, `--typeId`, `--limit` (0 = tout), `--start-skip`, `--max-pages`, `--max-items`.
- **API** : **POST /api/scrapping/import/{entity}/{id}** (ScrappingController) pour class, monster, spell, item, panoply. Paramètre `validate=false` pour bypasser la validation (activée par défaut). Import monster : runOne (entité principale Creature + Monster). Les relations (sorts, drops) peuvent être résolues via RelationResolutionService lorsque les données brutes incluent spells/drops (ex. appel explicite avec rawData + creatureId).
- **Tests** : unitaires dans `tests/Unit/Scrapping/Core/`, feature `tests/Feature/Scrapping/ScrappingCommandTest.php` et tests relations/orchestrateur.

---

## 5. Relations

Pour l’import **monster**, après intégration Creature + Monster, le **RelationResolutionService** importe les sorts et items des drops (runOne spell/item) puis synchronise `creature_spell` et `creature_resource`. L’option `include_relations` dans la requête contrôle si les relations sont importées et synchronisées. Voir [RELATIONS.md](./RELATIONS.md).

---

## 6. Références

- [README](./README.md) — Contenu du dossier Architecture.
- [VISION_ET_ARCHITECTURE.md](./VISION_ET_ARCHITECTURE.md) — Vision et chaîne Collect → Conversion → Validation → Intégration.
- [OPTIMISATION_ARCHITECTURE.md](../OPTIMISATION_ARCHITECTURE.md) — Structure du namespace et composants.
- [IMPLICATIONS_NOUVELLE_ARCHI_CARACTERISTIQUES.md](./IMPLICATIONS_NOUVELLE_ARCHI_CARACTERISTIQUES.md) — Formules/limites en BDD.
- [SCHEMA_CONFIG.md](./SCHEMA_CONFIG.md) — Schéma des configs (requêtes + mapping).
