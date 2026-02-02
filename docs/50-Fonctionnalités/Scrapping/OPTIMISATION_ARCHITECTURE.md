# Architecture du scrapping

Ce document décrit l’architecture actuelle du namespace `App\Services\Scrapping` : structure, config, collecte, pipeline et catalogues.

---

## 1. Structure du namespace

- **Sous-espaces** : Catalog, Config, Constants, Conversion, Core, DataCollect, Http, Media, Registry, plus des fichiers à la racine.
- **Config Laravel** : `config/scrapping.php` (timeouts, cache, images, etc.).

### 1.1 Arborescence

```
app/Services/Scrapping/
├── Core/                    # Pipeline Collect → Conversion → Validation → Intégration
│   ├── Collect/            # CollectService (API DofusDB, encodage Feathers)
│   ├── Config/             # ConfigLoader, CollectAliasResolver
│   ├── Conversion/        # ConversionService, FormatterApplicator, DofusDbConversionFormulas
│   ├── Integration/       # IntegrationService
│   ├── Orchestrator/       # Orchestrator (runOne, runMany)
│   ├── Relation/           # RelationResolutionService
│   └── Validation/        # ValidationService
├── Catalog/                # Référentiels DofusDB (types, races)
│   ├── DofusDbItemTypesCatalogService
│   ├── DofusDbMonsterRacesCatalogService
│   └── DofusDbItemSuperTypeMappingService
├── Config/                 # DofusDbEffectCatalog
├── DataCollect/            # Filtres métier (race, type)
│   ├── ItemEntityTypeFilterService
│   └── MonsterRaceFilterService
├── Http/                   # DofusDbClient (cache, retry)
├── Media/                  # ScrappingImageStorageService
├── Conversion/             # Handlers métier (ex. résistances)
├── Registry/               # TypeRegistryBatchTouchService
├── Constants/              # EntityLimits, DofusDbLimits
└── [racine]                # DofusDbConversionFormulaService, ConversionHandlerRegistry
```

---

## 2. Configuration des entités

- **Arbre unique** : `resources/scrapping/config/sources/dofusdb/`.
- **Loader** : `Core\Config\ConfigLoader` (singleton, base `resources/scrapping/config`).
- **Contenu** : `source.json` (baseUrl, defaultLanguage) et `entities/*.json` (breed, item, monster, spell, etc.) avec endpoints, filters, mapping.
- **Alias** : `CollectAliasResolver` (collect_aliases.json) pour les alias de collecte (class → breed, ressource → item, etc.).

**Exposé à l’UI** : `GET /api/scrapping/config` (ScrappingConfigController) et `GET /api/scrapping/meta` (ScrappingController::meta) s’appuient sur ce ConfigLoader.

---

## 3. Collecte

- **Service unique** : `Core\Collect\CollectService`.
- **Usage** : orchestrateur (import, preview), recherche (ScrappingSearchController), batch (ScrappingCommand).
- **Comportement** : requêtes pilotées par la config (endpoints, pagination), encodage Feathers (tableaux en `key[]=v`) compatible DofusDB, filtres (id, idMin/Max, ids, name, raceId→race, typeId, levelMin/Max, etc.).
- **HTTP** : `DofusDbClient` injecté (cache, retry) ; fallback `Http::get()` si absent.
- **API** : `fetchOne(source, entity, id)`, `fetchMany(source, entity, filters, options)`, `fetchManyResult(source, entity, filters, options)` (retourne items + meta skip/pages/returned).

---

## 4. Pipeline (import / preview)

- **Orchestrator** : `Core\Orchestrator\Orchestrator` (Collect → Conversion → Validation → Intégration).
- **Points d’entrée** : ScrappingController (import/class, import/monster, import/item, import/spell, batch, preview, meta), ScrappingImportController (import générique par entité), CLI `scrapping`.
- **Relations** : RelationResolutionService (sorts, drops, recettes, etc.) après intégration.

---

## 5. Catalogues

- **DofusDbItemTypesCatalogService** : catalogue item-types DofusDB (getCatalog, fetchName, stripDofusdbSuffix, resolveTypeIdsByName, etc.).
- **DofusDbMonsterRacesCatalogService** : catalogue races de monstres (listAll, mapNames, fetchName, findRaceIdByName).
- **DofusDbItemSuperTypeMappingService** : mapping superTypes / types.

---

## 6. Résumé

| Élément        | Ce qui existe |
|----------------|----------------|
| Config entités | Un arbre : `resources/scrapping/config/sources/dofusdb/`, chargé par ConfigLoader. |
| Collecte       | Un service : CollectService (encodage Feathers, DofusDbClient). |
| Pipeline       | Un orchestrateur : Core Orchestrator (Collect → Convert → Validate → Integrate). |
| Recherche / batch | CollectService + ConfigLoader (même config que le pipeline). |
| Catalogues     | Services dédiés (ItemTypes, MonsterRaces, ItemSuperTypeMapping) avec résolution de noms intégrée. |
