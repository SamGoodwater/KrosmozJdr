# Inventaire des JSON scrapping et évaluation migration BDD + UI

Ce document inventorie les fichiers JSON utilisés par le scrapping, décrit leur rôle et les clés lues par le code, puis évalue la faisabilité d’un transfert en base de données avec une interface admin éditable.

---

## 1. Inventaire des fichiers JSON

| Fichier | Rôle principal |
|--------|-----------------|
| **sources/dofusdb/source.json** | Config globale de la source DofusDB : baseUrl, langue, HTTP, sécurité. |
| **sources/dofusdb/entities/*.json** | Par entité : endpoints, filtres, target, mapping (champs DofusDB → Krosmoz), relations, meta. |
| **collect_aliases.json** | Alias UI/CLI (spell, monster, class, resource, consumable, equipment) → source + entity + label + filtres par défaut. |
| **sources/dofusdb/dofusdb_characteristic_to_krosmoz.json** | Mapping ID caractéristique DofusDB (effets d’item) → clé caractéristique Krosmoz (ex. `intel_object`). |
| **sources/dofusdb/item-super-types.json** | Groupes métier (resource, consumable, equipment), superTypeIds par groupe, excludedTypeIds, référence superTypes. |
| **sources/dofusdb/item-types.json** | Référence des 232 item-types DofusDB (snapshot pour validation/mapping). |
| **sources/dofusdb/dofusdb_monster_grade_to_creature.json** | Champ grade monstre DofusDB → clé Krosmoz (utilisé par commandes d’extraction d’échantillons). |
| **sources/krosmoz/*.json** | Échantillons Krosmoz (samples) pour tests / comparaison, pas config opérationnelle. |

Les **entités** dans `entities/` sont : `monster`, `spell`, `breed`, `item`, `item-type`, `item-super-type`, `panoply`, `monster-race`.

---

## 2. Rôle détaillé et clés utilisées par le code

### 2.1 source.json

- **Lu par** : `ConfigLoader::loadSource()`, `CollectService` (baseUrl, defaultLanguage).
- **Clés** : `version`, `source`, `label`, `baseUrl`, `defaultLanguage`, `http` (timeoutSeconds, retryAttempts, userAgent), `security.allowedHosts`.
- **Rôle** : Une seule source « dofusdb » ; définit où et comment appeler l’API.

### 2.2 entities/{entity}.json

- **Lu par** : `ConfigLoader::loadEntity()`, `CollectService`, `ConversionService`, `Orchestrator`, `IntegrationService` (indirect via type), `RelationResolutionService` (relations en dur pour monster/spell/breed/item), `ScrappingConfigController`, `ScrappingPreviewBuilder`, `FormatterApplicator` (mapping pour clampToCharacteristic).
- **Blocs et clés** :
  - **meta** : `maxId`, `totalCount`, `pagination`, `catalogOnly`, `description`, `notes`, `collectStrategy` (groupBy, outputShape, sourcePath).
  - **endpoints** : `fetchOne.pathTemplate`, `fetchOne.queryDefaults`, `fetchMany.path`, `fetchMany.queryDefaults` — requis par ConfigLoader.
  - **filters.supported** : `key`, `type`, `max` — pour construire les paramètres de requête (CollectService).
  - **target** : `krosmozEntity`, `primaryKey` (sourceField, targetField), `autoUpdateField` ; optionnel `catalogOnly`.
  - **mapping** : tableau d’entrées `key`, `from.path` (et `from.langAware`), `to[]` (model, field), `formatters[]` (name, args). Requis par ConfigLoader. Utilisé pour conversion, preview, et pour `clampToCharacteristic` (FormatterApplicator lit le mapping pour résoudre characteristicId → key).
  - **resistanceBatch** : booléen (monster) pour conversion des résistances en lot.
  - **relations** : par nom (ex. spells, drops) — `enabledByDefault`, `extract.path`, `extract.idPath`, `extract.quantityPath`, `targetEntity`, `filters` (ex. resourceTypeIds.defaultFromDb). L’orchestrateur et RelationResolutionService utilisent ces infos pour savoir quelles relations importer (structure lue surtout pour l’UI et la pile d’import ; le comportement monster est en partie codé en dur).

### 2.3 collect_aliases.json

- **Lu par** : `CollectAliasResolver`, `ScrappingConfigController` (pour label et entity réelle).
- **Clés** : `aliases.{alias}` → `source`, `entity`, `label`, optionnel `filterByRace`, `filterByType`, `defaultFilter` (ex. superTypeGroup).
- **Rôle** : Faire correspondre un libellé UI/CLI (ex. « Ressources », « Classes ») à une entité technique (item, breed) et à des filtres par défaut.

### 2.4 dofusdb_characteristic_to_krosmoz.json

- **Lu par** : `FormatterApplicator` (formatter itemEffectsToKrosmozBonus) — chemin en dur dans le code.
- **Clés** : `mapping` (id DofusDB → characteristic_key Krosmoz), `keywords_by_id` (documentation).
- **Rôle** : Convertir les effets d’objets DofusDB (characteristic id) en clés de caractéristiques Krosmoz pour le groupe object.

### 2.5 item-super-types.json

- **Lu par** : `DofusDbItemSuperTypeMappingService`, `ItemEntityTypeFilterService`, `ExtractObjectConversionSamplesCommand`, `ScrappingRunCommand` (pour filtrage par type).
- **Clés** : `excludedTypeIds`, `excludedTypeIdsNotes`, `superTypesReference[]` (id, nameFr, krosmozCategory), `groups` (resource, consumable, equipment) avec strategy, superTypeIds ou excludeSuperTypeIds, notes.
- **Rôle** : Définir quels typeIds/superTypeIds appartiennent à resource/consumable/equipment et quels typeIds sont exclus de la collecte.

### 2.6 item-types.json

- **Rôle** : Snapshot de référence des item-types (id, superTypeId, categoryId, nameFr). Utilisé pour validation et mapping ; les listes dynamiques passent par l’API DofusDB ou le catalogue `DofusDbItemTypesCatalogService`. Peut rester en JSON ou être remplacé par un cache/table dérivée de l’API.

### 2.7 dofusdb_monster_grade_to_creature.json

- **Rôle** : Export / échantillons (ExtractCreatureConversionSamplesCommand). Pas dans le flux principal collecte → conversion → intégration.

---

## 3. Évaluation : transfert en BDD + UI

### 3.1 source.json → BDD + UI

| Critère | Évaluation |
|--------|------------|
| Structure | Plate : quelques champs (baseUrl, defaultLanguage, timeout, etc.). |
| Volume | Une seule source (dofusdb). |
| Tables possibles | `scrapping_sources` (id, slug, label, base_url, default_language, http_config JSON, security_config JSON, timestamps). |
| UI | Formulaire simple : champs texte (URL, langue), nombres (timeout, retries), liste (allowedHosts). |
| Difficulté | **Facile**. Lecture unique au démarrage ou par run ; migration simple. |

### 3.2 collect_aliases.json → BDD + UI

| Critère | Évaluation |
|--------|------------|
| Structure | Liste d’alias → (source, entity, label, defaultFilter, filterByRace/Type). |
| Volume | ~10 alias. |
| Tables possibles | `scrapping_collect_aliases` (id, alias, source, entity, label, default_filter JSON, filter_by_race, filter_by_type, sort_order). |
| UI | Liste/grille d’alias avec formulaire par ligne : alias, source, entity, label, filtres par défaut (clé/valeur ou JSON). |
| Difficulté | **Facile**. Structure plate, peu de relations. |

### 3.3 entities/*.json (par entité) → BDD + UI

| Critère | Évaluation |
|--------|------------|
| Structure | Imbriquée : endpoints (objets), filters.supported (tableau), target (objet), mapping (tableau d’objets avec from/to/formatters), relations (objet), meta (objet). |
| Volume | ~8 entités ; mapping monster/spell/item contient beaucoup d’entrées. |
| Tables possibles | `scrapping_entity_configs` (id, source_id, entity_slug, label, meta JSON, target JSON, resistance_batch, catalog_only). `scrapping_entity_endpoints` (id, entity_config_id, kind fetchOne/fetchMany, path_template/path, method, query_defaults JSON). `scrapping_entity_filters` (id, entity_config_id, key, type, max). `scrapping_entity_mappings` (id, entity_config_id, key, from_path, from_lang_aware, to_model, to_field, formatters JSON). `scrapping_entity_relations` (id, entity_config_id, relation_name, enabled_by_default, extract_path, id_path, quantity_path, target_entity, filters JSON). |
| UI | Écran par entité : onglets ou sections (Métadonnées, Endpoints, Filtres, Cible, Mapping, Relations). Mapping = grille avec lignes (key, path, model, field, formatters) + éditeur pour formatters (liste name/args). Relations = grille ou formulaire par relation. |
| Difficulté | **Moyenne à élevée**. Beaucoup de champs et de relations ; formatters avec args variables (characteristicId, levelPath, etc.). Une UI complète pour tout éditer est lourde ; une UI « lien caractéristique ↔ DofusDB » (path + formatter + args) par champ serait un bon premier pas. |

### 3.4 dofusdb_characteristic_to_krosmoz.json → BDD + UI

| Critère | Évaluation |
|--------|------------|
| Structure | mapping : id numérique → clé string ; keywords_by_id id → string (doc). |
| Volume | ~25 entrées de mapping. |
| Tables possibles | `scrapping_characteristic_mappings` (id, source_id, dofusdb_characteristic_id, characteristic_key, keyword_comment). Ou une table `characteristics` existante + colonne `dofusdb_characteristic_id` (déjà lien avec BDD). |
| UI | Grille : ID DofusDB, clé Krosmoz (select ou texte), optionnel commentaire. |
| Difficulté | **Facile**. Structure plate ; lien naturel avec les caractéristiques Krosmoz en BDD. |

### 3.5 item-super-types.json → BDD + UI

| Critère | Évaluation |
|--------|------------|
| Structure | excludedTypeIds (liste), superTypesReference (liste id, nameFr, krosmozCategory), groups (resource, consumable, equipment) avec strategy, superTypeIds ou excludeSuperTypeIds. |
| Volume | Exclusions ~15, superTypes ~30, 3 groupes. |
| Tables possibles | `scrapping_item_super_type_ref` (id, dofusdb_super_type_id, name_fr, krosmoz_category). `scrapping_item_type_exclusions` (id, type_id, reason). `scrapping_item_super_type_groups` (id, slug, strategy, super_type_ids JSON ou table de liaison, exclude_super_type_ids JSON). |
| UI | Liste des super-types avec catégorie (equipment/resource/consumable/excluded) ; liste des typeIds exclus avec motif ; par groupe (resource, consumable, equipment) : stratégie + liste de superTypeIds ou exclusions. |
| Difficulté | **Moyenne**. Plusieurs listes et règles ; UI avec onglets ou sections claires reste faisable. |

### 3.6 item-types.json

- Peut rester un snapshot fichier ou être remplacé par une table remplie depuis l’API DofusDB (catalogue). Pas prioritaire pour une UI d’édition.

---

## 4. Plan de migration proposé

### Phase 1 — Rapide et à fort impact

1. **Lien caractéristique DofusDB ↔ Krosmoz**  
   Stocker en BDD le mapping **dofusdb_characteristic_to_krosmoz** (et idéalement le lien avec les formatters qui utilisent une characteristicId).  
   - Tables : soit étendre `characteristics` avec `dofusdb_characteristic_id` / source, soit table dédiée `scrapping_characteristic_mappings`.  
   - UI : écran « Mapping caractéristiques DofusDB » (grille ID DofusDB → clé Krosmoz).

2. **Source DofusDB**  
   Migrer **source.json** vers une table `scrapping_sources`.  
   - UI : formulaire « Source DofusDB » (URL, langue, timeout, allowedHosts).

3. **Alias de collecte**  
   Migrer **collect_aliases.json** vers une table `scrapping_collect_aliases`.  
   - UI : liste d’alias avec édition (alias, entity, label, defaultFilter).

### Phase 2 — Données métier éditable

4. **Item super types et exclusions**  
   Migrer **item-super-types.json** (groupes, exclusions, superTypesReference) en BDD.  
   - UI : écran « Types d’objets » : super-types par catégorie, typeIds exclus, groupes resource/consumable/equipment.

### Phase 3 — Optionnel, plus lourd

5. **Config entités (endpoints + mapping)**  
   Garder les **entities/*.json** en fichier pour l’instant ; introduire une **couche de lecture** qui peut charger depuis BDD si une config existe (override), sinon depuis le fichier.  
   - Commencer par exposer en UI les **métadonnées** (label, meta.maxId, meta.catalogOnly) et **endpoints** (path, queryDefaults) pour une entité, sans toucher au mapping.  
   - Plus tard : éditer le mapping (key, path, model, field, formatters) en BDD avec UI grille + éditeur formatters.

### Ce qui reste en JSON (recommandation)

- **entities/*.json** : restent la source par défaut du pipeline (endpoints, mapping, relations). La BDD peut servir d’override ou de copie éditable (sauvegarde depuis UI → écriture en JSON ou en BDD selon stratégie).
- **item-types.json** : référence statique ou cache ; pas prioritaire en UI.
- **dofusdb_monster_grade_to_creature.json** : outillage d’export ; peut rester en fichier.

---

## 5. Récapitulatif

| Fichier / bloc | Rôle | Migration BDD | UI prévue |
|----------------|------|----------------|-----------|
| source.json | BaseUrl, langue, HTTP | Facile — table sources | Formulaire simple |
| collect_aliases.json | Alias → entity + filtres | Facile — table aliases | Grille + formulaire |
| entities/*.json (meta, endpoints, filters, target, mapping, relations) | Collecte + conversion + intégration | Moyenne à lourde | Par phase : meta/endpoints d’abord, mapping ensuite |
| dofusdb_characteristic_to_krosmoz.json | ID carac. DofusDB → clé Krosmoz | Facile — table ou extension characteristics | Grille mapping |
| item-super-types.json | Groupes + exclusions + ref superTypes | Moyenne | Écran types + exclusions + groupes |
| item-types.json | Référence item-types | Optionnel (cache API) | Non prioritaire |

En commençant par **caractéristiques DofusDB ↔ Krosmoz**, **source** et **alias**, on obtient une première valeur métier (édition des liens et des paramètres de source) sans toucher au cœur du pipeline (mapping d’entités). L’ajout de l’édition des **item-super-types** et, plus tard, des **configs d’entité** (au moins meta + endpoints) complète une UI admin cohérente pour le scrapping.
