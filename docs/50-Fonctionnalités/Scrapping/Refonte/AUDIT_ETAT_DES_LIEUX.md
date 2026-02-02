# Audit — État des lieux du scrapping KrosmozJDR

Document d’audit réalisé pour préparer la refonte du scrapping. Il décrit où se trouve le code, comment il est utilisé, et quels sont les couplages et points de complexité.

---

## 1. Vue d’ensemble du pipeline actuel

Le scrapping suit un pipeline en trois étapes :

```
DofusDB (API)  →  Collect  →  Conversion  →  Intégration  →  KrosmozJDR (DB)
```

- **Collect** : récupération des données brutes (HTTP, pagination, cache).
- **Conversion** : mapping des champs + formatters (config JSON ou legacy selon les cas).
- **Intégration** : écriture en base, relations, images.

Deux modes coexistent :
- **Config-driven** : configs JSON dans `resources/scrapping/sources/dofusdb/entities/*.json` + `formatters/registry.json`.
- **Legacy** : méthodes dédiées par entité dans `DataCollectService`, `DataConversionService`, `DataIntegrationService`, avec usage de `config('characteristics')` pour la conversion.

---

## 2. Où se trouve le code scrapping

### 2.1 Backend (Laravel)

| Zone | Fichiers / dossiers | Rôle |
|------|---------------------|------|
| **Services** | `app/Services/Scrapping/` | Cœur métier |
| ├─ Catalog/ | DofusDbItemTypesCatalogService, DofusDbItemSuperTypeMappingService, DofusDbMonsterRacesCatalogService, *NameResolver | Résolution de noms/types DofusDB → Krosmoz |
| ├─ Config/ | ScrappingConfigLoader, FormatterRegistry, ConfigDrivenConverter, DofusDbEffectCatalog | Config JSON, conversion pilotée par config |
| ├─ Constants/ | DofusDbLimits, EntityLimits | Limites ID par entité |
| ├─ DataCollect/ | DataCollectService, ConfigDrivenDofusDbCollector, ItemEntityTypeFilterService, MonsterRaceFilterService | Collecte HTTP + filtrage par type |
| ├─ DataConversion/ | DataConversionService | Conversion legacy (utilise `config('characteristics')`) |
| ├─ DataIntegration/ | DataIntegrationService | Intégration DB + images |
| ├─ Http/ | DofusDbClient | Client HTTP DofusDB |
| ├─ Media/ | ScrappingImageStorageService | Téléchargement/stockage images |
| ├─ Orchestrator/ | ScrappingOrchestrator | Orchestration collect → convert → integrate |
| └─ Registry/ | TypeRegistryBatchTouchService | Mise à jour des registres de types |
| **Controllers** | `app/Http/Controllers/Scrapping/` | API et UI |
| | ScrappingController, ScrappingDashboardController, ScrappingSearchController, ScrappingConfigController | Endpoints principaux (meta, config, search, preview, import) |
| | DataCollectController | Routes de test collect (class, monster, item, spell, effect, etc.) |
| | ResourceTypeRegistryController, ItemTypeRegistryController, ConsumableTypeRegistryController | Registres typeId (allowed/blocked/pending) |
| | DofusDbItemTypesCatalogController, DofusDbMonsterRacesCatalogController | Catalogues pour l’UI |
| **Commands** | `app/Console/Commands/ScrappingCommand.php`, ScrappingBackfillImagesCommand | CLI : collect/search/import, backfill images |
| **Models** | `app/Models/Scrapping/PendingResourceTypeItem.php` | Items “pending” pour les resource types |
| **Models (domaine)** | `app/Models/Type/MonsterRace.php` | Méthode liée au scrapping (race DofusDB) |
| **Config** | `config/scrapping.php` | Config globale scrapping (orchestrateur, collect, images, conversion, intégration, timeouts, retry, logs, notifications, webhooks, métriques) |

### 2.2 Configs et ressources

| Zone | Rôle |
|------|------|
| `config/scrapping.php` | Toute la config scrapping (très volumineux). |
| `config/characteristics.php` | Caractéristiques du jeu (limites, formules, validation). Utilisé par **DataConversionService** (legacy). |
| `resources/scrapping/sources/dofusdb/` | source.json, item-super-types.json, entities/*.json (class, consumable, effect, equipment, item, monster, panoply, resource, spell). |
| `resources/scrapping/formatters/registry.json` | Registry des formatters pour la conversion config-driven. |

### 2.3 Routes

- **Web** : `routes/services/scrapping.php` → `/scrapping` (dashboard).
- **API** : `routes/api.php` :
  - Préfixe `scrapping/test` : tests collect (class, monster, item, spell, effect, items-by-type, clear-cache).
  - Préfixe `scrapping` : config, search/{entity}, meta, preview/{type}/{id}, import/* (class, monster, item, resource, consumable, spell, panoply, batch, range, all), import-with-merge.
  - Préfixes dédiés : `scrapping/resource-types`, `scrapping/item-types`, `scrapping/consumable-types`, `scrapping/monster-races`, `scrapping/dofusdb/item-types`.

### 2.4 Frontend (Vue)

| Zone | Rôle |
|------|------|
| **Pages** | `resources/js/Pages/Pages/scrapping/Index.vue` (affiche ScrappingDashboard). |
| **Organismes** | ScrappingDashboard, ScrappingSection, ScrappingSearchTableSection, ScrappingModal, CompareModal. |
| **Composables** | `useScrapping.js` : `refreshEntity(entityType, entityId, options)` → appelle `POST /api/scrapping/import/{type}/{id}`. |
| **Entity Index** | Presque toutes les pages d’entité (resource, item, spell, consumable, classe, monster, panoply, campaign, capability, npc, attribute, creature, specialization, scenario, shop) importent `useScrapping` et utilisent `refreshEntity` (bouton “Rafraîchir”). |
| **Entity types** | resource-type, item-type, consumable-type : URLs list/bulk/delete vers `/api/scrapping/resource-types`, etc. |
| **Navigation** | LoggedHeaderContainer : lien “Scrapping” si `canAccess('scrapping')`. |
| **Actions** | entity-actions-config.js : action `refresh` (Rafraîchir via scrapping). |
| **Permissions** | config/access-permissions.php : `scrapping` → manageAny sur resources et resource-types. |

### 2.5 Tests

- Feature : ScrappingControllerTest, ScrappingOrchestratorTest, ScrappingSearchControllerTest, ScrappingConfigControllerTest, ScrappingPreviewConfigConversionTest, ScrappingResourceConversionTest, ScrappingRelationsTest, ScrappingCommandTest, ResourceTypeIdDetectionTest.
- Unit : ConfigDrivenDofusDbCollectorTest, DofusDbClientTest, ScrappingConfigLoaderTest, DataCollectServiceTest, DataConversionServiceTest, DataIntegrationServiceTest.

---

## 3. Dépendances et couplage

### 3.1 Orchestrateur

`ScrappingOrchestrator` :

- **Injecté** : DataCollectService, DataConversionService, DataIntegrationService, ScrappingConfigLoader, ItemEntityTypeFilterService.
- **Utilisé en interne** (dans `convertUsingConfigOrLegacy`) : instanciation manuelle de DofusDbClient, DofusDbEffectCatalog, ConfigDrivenConverter, DofusDbItemTypesCatalogService, DofusDbItemSuperTypeMappingService.

Conséquences : l’orchestrateur connaît à la fois le pipeline et les détails de la conversion config-driven (loader, converter, effects, catalogs). Il mélange coordination et choix d’implémentation.

### 3.2 DataCollectService

- Dépend de : DofusDbClient, ConfigDrivenDofusDbCollector, `config('scrapping.data_collect')`.
- Contient aussi du code legacy : méthodes par entité (collectClass, collectMonster, collectItem, etc.) avec `fetchEntityFromConfigOrFallback` et des appels directs (ex. fetchFromDofusDb pour spell-levels).
- Utilise PendingResourceTypeItem (enregistrement des typeId vus).

Donc : collecte “config-driven” et logique métier spécifique (sorts de classe, pagination custom, etc.) coexistent dans le même service.

### 3.3 DataConversionService

- Dépend de : `config('scrapping.data_conversion')` et **`config('characteristics')`**.
- Méthodes par entité : convertClass, convertMonster, convertItem, etc., avec lecture directe de `characteristics` (life, level, attributes, size, rarity, price, cost, range, area, effect_types, required_fields, etc.).

Le scrapping est donc fortement couplé au domaine “caractéristiques” du jeu, qui sert à la fois au jeu et à la conversion legacy.

### 3.4 DataIntegrationService

- Charge sa propre config : `require __DIR__ . '/config.php'`.
- Dépend de : ScrappingImageStorageService, et de nombreux modèles Eloquent (Classe, Creature, Monster, Item, Consumable, Resource, Spell, Panoply, ItemType, ConsumableType, ResourceType, SpellType, Attribute, Capability).
- Connaît la structure des entités Krosmoz (creatures, monsters, items, etc.) et les relations.

Un seul service porte toute l’intégration pour toutes les entités → fichier très long et peu découpé par domaine.

### 3.5 Contrôleurs

- **ScrappingController** : Orchestrator + ScrappingConfigLoader ; utilise aussi EntityLimits en fallback pour meta.
- **ResourceTypeRegistryController** : Orchestrator, DataCollectService, ItemEntityTypeFilterService, DofusDbClient, DofusDbItemTypeNameResolver (et PendingResourceTypeItem, ResourceType). Mélange registry, résolution de noms et orchestration.
- **ItemTypeRegistryController**, **ConsumableTypeRegistryController** : patterns proches (BulkDecisionUpdateTrait, catalogues, etc.).

Les registres “types” répètent un schéma similaire avec des dépendances multiples (orchestrateur, collect, filtres, client HTTP, resolvers).

### 3.6 Configs éclatées

- **Scrapping** : config/scrapping.php (très gros) + configs locales dans DataCollect, DataConversion, DataIntegration, Orchestrator (config.php dans chaque sous-dossier).
- **Caractéristiques** : config/characteristics.php (chargé depuis config/characteristics.php qui agrège des JSON) — utilisé par DataConversionService.
- **Sources** : resources/scrapping/sources/dofusdb/ + formatters/registry.json.

Pour faire évoluer un comportement (ex. conversion), il faut souvent toucher à plusieurs endroits (config scrapping, characteristics, JSON d’entités, formatters).

---

## 4. Points d’entrée et flux

### 4.1 Entrées utilisateur

1. **Dashboard** : page `/scrapping` → ScrappingDashboard (meta, config, search, import batch, preview, onglets registres resource/item/consumable types, monster-races).
2. **Rafraîchir une entité** : depuis une Index d’entité (resource, item, spell, etc.) → `useScrapping().refreshEntity(type, id)` → `POST /api/scrapping/import/{type}/{id}`.
3. **Registres** : pages entity resource-type, item-type, consumable-type qui appellent les API scrapping (list, bulk, decision, pending, replay).

### 4.2 Entrées techniques

1. **API** : config, meta, search, preview, import (unitaire, batch, range, all), import-with-merge, registres, catalogues.
2. **CLI** : `php artisan scrapping` (collect, import, batch, sync-resource-types, compare, etc.).
3. **Commande** : ScrappingBackfillImagesCommand.

### 4.3 Flux type “import unitaire”

1. Client appelle `POST /api/scrapping/import/monster/31`.
2. ScrappingController → Orchestrator->importMonster(31, options).
3. Orchestrator : DataCollectService->collectMonster(31) → raw.
4. Orchestrator : convertUsingConfigOrLegacy('monster', raw) → ConfigDrivenConverter si config OK, sinon DataConversionService->convertMonster(raw).
5. Orchestrator : DataIntegrationService->integrateMonster(converted, options).
6. Réponse JSON avec succès / erreur et éventuelles relations.

Tout passe par l’orchestrateur, qui décide quelle conversion utiliser et appelle les trois couches.

---

## 5. Synthèse des problèmes

| Problème | Détail |
|----------|--------|
| **Services non indépendants** | L’orchestrateur instancie des convertisseurs/catalogs ; DataConversionService lit `characteristics` ; DataIntegrationService connaît tous les modèles et la config images. |
| **Code dispersé** | Scrapping présent dans ~115 fichiers : Services, Controllers, Config, Routes, Vue (pages, organismes, composables, entity Index, permissions, actions). |
| **Double système** | Conversion “config-driven” (JSON + FormatterRegistry) et “legacy” (méthodes par entité + characteristics) coexistent ; la logique de choix est dans l’orchestrateur. |
| **Couplage characteristics** | DataConversionService dépend fortement de `config('characteristics')` ; évolution des caractéristiques ou du scrapping peut impacter l’autre. |
| **Registres dupliqués** | ResourceType, ItemType, ConsumableType : trois contrôleurs et flux UI proches, avec des dépendances similaires (orchestrator, collector, filters, resolvers). |
| **Config monolithique** | config/scrapping.php gère tout (orchestrateur, collect, images, conversion, intégration, timeouts, retry, logs, notifications, webhooks, métriques) ; difficile à lire et à découper. |
| **Frontend** | useScrapping + “refresh” présents sur beaucoup d’Index d’entités ; normalisation des types (classe → class, etc.) dupliquée côté JS et implicite côté API. |

---

## 6. Inventaire des fichiers “scrapping” (extrait)

- **Backend** : ~25 fichiers dans app/Services/Scrapping, ~10 contrôleurs Scrapping, 2 commandes, 1 model Scrapping, config/scrapping.php.
- **Frontend** : 1 composable (useScrapping), 1 page scrapping, 5 organismes scrapping, ~15 pages entity Index qui utilisent refreshEntity, entity-actions-config, access-permissions, LoggedHeaderContainer, resource-type Show.
- **Config / resources** : config/scrapping.php, config/characteristics.php (utilisé par scrapping), resources/scrapping/ (sources, formatters).
- **Routes** : routes/api.php (nombreuses routes scrapping), routes/services/scrapping.php.
- **Tests** : ~15 fichiers Feature/Unit liés au scrapping.

---

## 7. Pistes pour la refonte (à détailler dans ce dossier)

- **Découpler les couches** : Collect, Conversion, Integration avec des contrats (interfaces) clairs ; l’orchestrateur ne fait qu’enchaîner des appels, sans instancier les convertisseurs.
- **Une seule source de vérité pour la conversion** : soit 100 % config (JSON + formatters), soit un seul service de conversion qui s’appuie sur une couche “caractéristiques” explicite, pas les deux en parallèle.
- **Regrouper la config** : découper config/scrapping.php par domaine (collect, conversion, integration, media, orchestration) ou par “module” scrapping.
- **Registres** : factoriser le comportement commun (resource/item/consumable types) pour éviter la duplication de contrôleurs et de dépendances.
- **Frontend** : un seul point d’entrée pour “rafraîchir via scrapping” (composable ou store) et une liste explicite des types supportés (alignée avec l’API).
- **Documentation** : garder l’API (Orchestrateur/API.md, Data-collect/API.md) à jour et lier la refonte à ces endpoints pour un remplacement progressif.

Ce document pourra être complété par des schémas d’architecture cible et un plan de migration (ordre des remplacements, compatibilité API, tests).
