# État des lieux du scrapping KrosmozJDR

Ce document décrit **où se trouve le code** scrapping, comment il est utilisé, et les principaux couplages.

---

## 1. Pipeline actuel

```
DofusDB (API)  →  Collect  →  Conversion  →  Validation  →  Intégration  →  KrosmozJDR (DB)
```

- **Collect** : CollectService (config JSON, encodage Feathers, pagination, cache).
- **Conversion** : ConversionService + FormatterApplicator (mapping + formatters, formules BDD pour level/life/attributs/résistances).
- **Validation** : ValidationService (CharacteristicService / BDD).
- **Intégration** : IntegrationService (écriture en base, dry_run, images).
- **Relations** : RelationResolutionService (sorts, drops pour monster, après intégration).

Tout est piloté par les configs JSON dans `resources/scrapping/config/sources/dofusdb/`.

---

## 2. Où se trouve le code

### 2.1 Backend (Laravel)

| Zone | Fichiers / dossiers | Rôle |
|------|---------------------|------|
| **Core** | `app/Services/Scrapping/Core/` | Pipeline principal |
| ├─ Collect/ | CollectService | Collecte HTTP (config, encodage Feathers) |
| ├─ Config/ | ConfigLoader, CollectAliasResolver | Config JSON (source, entities, aliases) |
| ├─ Conversion/ | ConversionService, FormatterApplicator, DofusDbConversionFormulas | Mapping + formatters |
| ├─ Integration/ | IntegrationService | Intégration DB |
| ├─ Orchestrator/ | Orchestrator | Enchaîne Collect → Conversion → Validation → Intégration |
| ├─ Relation/ | RelationResolutionService | Résolution relations monster (sorts, drops) |
| └─ Validation/ | ValidationService | Validation vs CharacteristicService |
| **Catalog** | DofusDbItemTypesCatalogService, DofusDbMonsterRacesCatalogService, DofusDbItemSuperTypeMappingService | Référentiels types/races DofusDB |
| **DataCollect** | ItemEntityTypeFilterService, MonsterRaceFilterService | Filtres métier (race, type) |
| **Http** | DofusDbClient | Client HTTP DofusDB (cache, retry) |
| **Media** | ScrappingImageStorageService | Téléchargement/stockage images |
| **Registry** | TypeRegistryBatchTouchService | Mise à jour registres de types |
| **Constants** | EntityLimits, DofusDbLimits | Limites ID par entité |
| **Controllers** | ScrappingController, ScrappingSearchController, ScrappingConfigController, ResourceTypeRegistryController, etc. | API (meta, config, search, import) |
| **Commands** | ScrappingCommand, ScrappingBackfillImagesCommand | CLI (collect, import, sync-resource-types) |
| **Config** | `config/scrapping.php` | Config globale (timeouts, cache, images, etc.) |

### 2.2 Configs et ressources

| Zone | Rôle |
|------|------|
| `config/scrapping.php` | Config globale scrapping. |
| `config/characteristics.php` | Caractéristiques du jeu (limites, formules). Utilisé par ValidationService et formules de conversion. |
| `resources/scrapping/config/sources/dofusdb/` | source.json, entities/*.json (monster, spell, breed, item, item-type, monster-race, item-super-type), item-super-types.json, item-types.json. |
| `resources/scrapping/config/collect_aliases.json` | Alias CLI (class → breed, ressource → item, etc.). |

### 2.3 Routes et frontend

- **Web** : `routes/services/scrapping.php` → `/scrapping` (dashboard).
- **API** : config, meta, search/{entity}, import/* (class, monster, item, resource, consumable, spell, batch, etc.), registres (resource-types, item-types, consumable-types, monster-races).
- **Frontend** : Pages scrapping (Index.vue), ScrappingDashboard, useScrapping (refreshEntity), entity-actions-config (action refresh), permissions `scrapping`.

---

## 3. Points d’entrée

- **API** : POST /api/scrapping/import/{entity}/{id} (ScrappingController) pour class, monster, spell, item, resource, consumable.
- **CLI** : `php artisan scrapping --collect=... --import=... --save` (ScrappingCommand).
- **Recherche** : GET /api/scrapping/search/{entity} (ScrappingSearchController, CollectService).
- **Config** : GET /api/scrapping/config, GET /api/scrapping/meta (ConfigLoader).

---

## 4. Dépendances principales

- **Orchestrator** : ConfigLoader, CollectService, ConversionService, ValidationService, IntegrationService. Ne résout pas les relations ; RelationResolutionService est appelé séparément si besoin.
- **ConversionService** : ConfigLoader, FormatterApplicator, DofusDbConversionFormulas (formules BDD pour level, life, attributs, résistances).
- **CollectService** : ConfigLoader ; DofusDbClient injecté (cache, retry).
- **Controllers** : Orchestrator, ConfigLoader, CollectService, catalogues selon les endpoints.

---

## 5. Références

- [ETAT_AVANCEMENT.md](./ETAT_AVANCEMENT.md) — État actuel (pipeline, services, configs).
- [OPTIMISATION_ARCHITECTURE.md](../OPTIMISATION_ARCHITECTURE.md) — Structure du namespace Scrapping.
