# État des lieux détaillé — Service de scrapping

**Date :** 2026-03-03  
**Contexte :** [ANALYSE_GLOBALE_REFONTE_SCRAPPING.md](./ANALYSE_GLOBALE_REFONTE_SCRAPPING.md). Ce document inventorie l’existant **par brique** puis **par entité** pour faciliter la liste des besoins et le plan d’amélioration.

---

## 1. État des lieux par brique

### 1.1 Config et chargement (ConfigLoader, ScrappingMappingService)

| Élément | Existant |
|--------|----------|
| **ConfigLoader** | Charge `resources/scrapping/config/sources/{source}/source.json` et `entities/{entity}.json`. Pour chaque entité : si `ScrappingMappingService` est injecté et retourne des règles pour (source, entity), le champ `mapping` de la config est **remplacé** par le mapping BDD ; sinon **fallback** sur le `mapping` du JSON. Pas de fusion partielle (tout BDD ou tout JSON). |
| **ScrappingMappingService** | Lit `scrapping_entity_mappings` + `scrapping_entity_mapping_targets` + relation `characteristic`. Retourne un tableau : `key`, `from.path`, `from.langAware`, `to[]`, `formatters[]`, `characteristic_id`, `characteristic_key`. Méthodes : `getMappingForEntity`, `hasMappingForEntity`, `listEntitiesWithMapping`, `listMappingsForCharacteristic`. |
| **Injection** | ConfigLoader reçoit optionnellement le ScrappingMappingService (dans l’app, via binding / provider). En CLI ou tests sans BDD peuplée, le mapping vient du JSON. |
| **Entités avec mapping BDD** | Breed (6 règles), item (14), monster (26), spell (20). **Panoply** : uniquement dans le JSON d’entité (pas de règles en BDD dans le seeder). |

---

### 1.2 Collecte (CollectService)

| Élément | Existant |
|--------|----------|
| **Rôle** | Exécute les requêtes décrites en config : `fetchOne` (pathTemplate + queryDefaults), `fetchMany` (path, pagination `$limit` / `$skip`, filtres). Désérialise les réponses Feathers (`data`, `total`, `limit`). |
| **Spécificités par entité** | **Spell :** `fetchOne` ne renvoie pas les niveaux ; l’Orchestrator appelle ensuite `fetchSpellLevelsBySpellId` et enrichit `raw['levels']`. **Item :** recette non embarquée ; l’Orchestrator peut appeler `enrichRawWithRecipe` (CollectService ou Orchestrator) pour ajouter `recipe` (ingredientIds, quantities). **Panoply :** `applyCollectStrategy` peut filtrer les panoplies cosmétiques (`filterOutCosmetic`). |
| **Alias** | `CollectAliasResolver` : alias CLI (ex. class → breed) pour résoudre l’entité de config. |
| **Client HTTP** | `DofusDbClient` optionnel ; sinon utilisation directe de Http::get (ou équivalent). Cache possible (skip_cache dans les options). |
| **Filtres** | Les filtres supportés sont décrits dans chaque `entities/*.json` (`filters.supported`). La conversion en paramètres de requête est faite dans CollectService (`filtersToQueryParams`). |

---

### 1.3 Conversion (ConversionService, FormatterApplicator)

| Élément | Existant |
|--------|----------|
| **ConversionService** | Pour chaque règle du mapping : 1) extraction par `getByPath(raw, from.path)` (notation point, indices numériques) ; 2) application en chaîne des formatters (args interpolés avec `lang`) ; 3) écriture dans `out[target.model][target.field]` pour chaque cible. Contexte passé aux formatters : `entityType`, `targetModel`, `mappingRule`, `CONVERTED_OUTPUT`, `RAW`. **Ciblage item :** si `context['targetModel']` est défini (resource, consumables, items), seules les cibles dont `model === targetModel` sont remplies. **Batch résistances :** si `resistanceBatch` dans la config entité et entityType monster/class/item, appel à `DofusConversionService::convertResistancesBatch` et merge dans le bloc creatures/breeds/items selon l’entité. |
| **Formatters (registry)** | **Génériques :** toString, pickLang, toInt, nullableInt, clampInt, mapSizeToKrosmoz, storeScrappedImage, truncate, toJson, extractItemIds. **Caractéristiques :** clampToCharacteristic (args : characteristicId ; entité depuis context). **Dofus (si DofusConversionService injecté) :** dofusdb_level, dofusdb_life, dofusdb_attribute, dofusdb_ini. **Spécifiques :** resolveResourceTypeId, defaultRarityByLevel, recipeIdsToResourceRecipe, recipeToResourceRecipe, **itemEffectsToKrosmozBonus** (effets item → JSON bonus Krosmoz), **zoneDescrToNotation** (zone spell, délègue à SpellEffectsConversionService). |
| **Lien formatter ↔ caractéristique** | `clampToCharacteristic` utilise l’arg `characteristicId` (string, ex. "pa", "level"). Les formatters dofusdb_* utilisent soit une clé déduite (getLevelCharacteristicKey, etc.), soit `_resolvedCharacteristicKey` issu de `mappingRule.characteristic_key` (résolution dans FormatterApplicator). Donc la règle BDD peut porter `characteristic_id` / `characteristic_key`, mais le formatter dofusdb_attribute est le seul à utiliser explicitement `_resolvedCharacteristicKey` pour la conversion par caractéristique. |
| **Profondeur** | Le mapping ne traite que des **chemins plats ou à un niveau** (ex. grades.0.level, spell_global.apCost). Les structures **réellement imbriquées** (effects[] par niveau, effects[] par item) ne sont pas mappées règle par règle : elles passent par un **formatter unique** (itemEffectsToKrosmozBonus) ou un **sous-service** (SpellEffectsConversionService). |

---

### 1.4 Effets de sorts (SpellEffectsConversionService, normaliseur, catalogues)

| Élément | Existant |
|--------|----------|
| **SpellGlobalNormalizer** | Construit `raw['spell_global']` à partir du sort racine + premier niveau (levels[0]) + zone du premier effet, pour exposer des chemins stables (spell_global.apCost, spell_global.minRange, etc.) au mapping spell. |
| **SpellEffectsConversionService** | Entrée : spell brut + liste des spell-levels. Pour chaque niveau : parcours de `effects[]` et `criticalEffect[]` ; pour chaque instance, effectId → **DofusdbEffectMappingService** (mapping effectId → [sub_effect_slug, characteristic_source]). Sous-effets « frapper » (element) utilisent **DofusDbEffectCatalog** (GET /effects/{id}) pour élément ; les autres effectId non mappés vont en sous-effet « autre ». **Valeurs :** SpellEffectConversionFormulaResolver + CharacteristicGetterService (characteristic_spell) pour value_converted, dice_formula, etc. Sortie : SpellEffectsConversionResult (effect_group + effects avec degree, name, slug, sub_effects). |
| **DofusDbEffectMapping** | Constante PHP : tableau effectId → [sub_effect_slug, 'element'|'none']. Seuls quelques effectId (96–100 = frapper élément) sont mappés ; le reste → « autre ». |
| **DofusdbEffectMappingService** | Encapsule DofusDbEffectMapping (getSubEffectForEffectId). Pas de chargement depuis BDD ou JSON pour l’instant. |
| **Intégration** | Les effets convertis sont dans `converted['spell_effects']`. IntegrationService::integrateSpell appelle `integrateSpellEffectsForSpell` : création/mise à jour EffectGroup, Effect (par degree), EffectSubEffect, EffectUsage ; réutilisation d’un Effect existant si signature (sous-effets) identique. |

---

### 1.5 Bonus items / panoplies (formatter + JSON)

| Élément | Existant |
|--------|----------|
| **itemEffectsToKrosmozBonus** | Prend `item.effects` (liste d’objets avec `characteristic`, `from`, `to`, `value`/`min`/`max`). Fichier **dofusdb_characteristic_to_krosmoz.json** : mapping id caractéristique DofusDB → characteristic_key Krosmoz (ex. 15 → strength_object). Pour chaque effet : résolution de la clé, valeur (from/to/mid), puis **DofusConversionService::convertObjectAttribute** pour convertir la valeur. Agrégation des bonus par clé courte (sans _object). Sortie : JSON string pour la colonne effect (items) ou bonus (panoplies). |
| **Panoply** | Même formatter pour `effects` → `panoplies.bonus`. Pas de conversion champ par champ ; un seul formatter pour tout le bloc effects. |
| **Limitation** | Aucune règle dans scrapping_entity_mappings pour « item.effects[i].characteristic » ; tout est dans un formatter dédié + un JSON de mapping id → key. Les caractéristiques (formules, limites) sont bien utilisées via DofusConversionService mais en dehors du flux « règle de mapping + characteristic_id ». |

---

### 1.6 Validation (CharacteristicLimitService)

| Élément | Existant |
|--------|----------|
| **Entrée** | Données converties (structure par modèle : creatures, monsters, spells, items, etc.) + entityType. Alias : breed → class, npc → class. |
| **mergeModels** | Les blocs (creatures, monsters, spells, …) sont fusionnés en un tableau plat (field → value) pour la validation. Un champ présent dans plusieurs modèles écrase (dernier modèle gagnant). |
| **Validation** | Pour chaque champ du merged : getDefinitionByField(field, entity). Si une définition existe (table characteristic_creature/object/spell avec db_column ou key), validateSingle(key, value, entity). Règles : boolean → vrai/faux ; list → valeur dans value_available ; string/int → min/max (getLimits). |
| **Clamp** | clampConvertedData : pour chaque champ numérique ayant une définition avec limites, clamp dans [min, max]. Appliqué **avant** validate dans l’Orchestrator. |
| **Champs validés** | Uniquement les champs pour lesquels le Getter trouve une définition (db_column ou key) pour l’entité. Les champs sans définition (ex. champs techniques, ou sous-structures non exposées en flat) ne sont pas validés. **spell_effects** et **bonus** (JSON) ne sont pas validés champ par champ par le Limit : ce sont des structures imbriquées. |

---

### 1.7 Intégration (IntegrationService)

| Élément | Existant |
|--------|----------|
| **Types supportés** | monster, spell, breed/class, item (resources, consumables, items), panoply. Chaque type a une méthode dédiée (integrateMonster, integrateSpell, integrateBreed, integrateItem, integratePanoply). |
| **Item** | getItemTargetTableFromRaw(raw) détermine si l’item est resource, consumable ou item (équipement) selon typeId / registres. Les données converties ont des cibles multiples (resources, consumables, items) ; au moment de l’intégration, un seul bloc est utilisé selon le type. |
| **Options** | dry_run, force_update, replace_mode (never, always, draft_raw_only), ignore_unvalidated, exclude_from_update, property_whitelist, include_relations, download_images. |
| **Effets de sort** | Intégrés dans integrateSpell via spell_effects (effect_group, effects[]) ; création EffectGroup, Effect, EffectSubEffect, EffectUsage. |
| **Relations** | L’intégration ne gère pas elle-même les relations (ex. creature_spell, drops). C’est **RelationResolutionService** qui, après intégration d’un monster, importe les sorts et items des drops puis synchronise les tables de liaison. |

---

### 1.8 Relations (RelationResolutionService, RelationImportStack)

| Élément | Existant |
|--------|----------|
| **Rôle** | Après intégration d’une entité (ex. monster), résout les relations déclarées dans la config (relations.spells, relations.drops) : soit enregistrement sur une **pile** (RelationImportStack) pour traitement différé, soit **import inline** (runOne pour chaque sort, chaque item des drops) puis synchronisation des tables (creature_spell, creature_resource, etc.). |
| **Monster** | spells[] → import de chaque sort via Orchestrator::runOne('dofusdb', 'spell', id) ; drops[] → import item/resource/consumable selon typeId, puis sync creature_resource (ou tables équivalentes). |
| **Config** | Chaque entité peut avoir un bloc `relations` dans son JSON (extract.path, targetEntity, strategy, filters). |

---

### 1.9 Service Caractéristiques (côté scrapping)

| Élément | Existant |
|--------|----------|
| **CharacteristicGetterService** | getDefinition(key, entity), getLimits(key, entity), getDefinitionByField(field, entity), getConversionFormula, getGroupForEntity. Résolution entity → groupe (creature, object, spell) ; résolution champ → clé via tables characteristic_creature/object/spell (db_column, key). |
| **CharacteristicLimitService** | validate(convertedData, entityType), clampConvertedData, validateSingle, clamp. |
| **DofusConversionService** | convert(key, variables, entityType, valueDofus, context), convertResistancesBatch(raw, entityType), convertObjectAttribute(charKey, value, entityType, context). S’appuie sur Getter (formules, limites), Formula, Limit. |
| **Utilisation** | FormatterApplicator (dofusdb_*, clampToCharacteristic), ConversionService (résistances batch), SpellEffectConversionFormulaResolver (valeurs d’effets), itemEffectsToKrosmozBonus (convertObjectAttribute). **Pas d’API** « convertir/valider un nœud imbriqué » : la profondeur est gérée dans le scrapping. |

---

## 2. État des lieux par entité

### 2.1 Monster

| Aspect | Détail |
|--------|--------|
| **Config** | `entities/monster.json`. Endpoints fetchOne (pathTemplate), fetchMany. Filtres : id, idMin, idMax, ids, name, raceId, raceIds, levelMin, levelMax. resistanceBatch: true. |
| **Structure DofusDB** | Objet avec grades[], spells[], drops[], race, img, size, etc. Mapping sur **grades.0.*** (premier grade uniquement). |
| **Mapping** | ~26 règles en BDD (dofusdb_id, name, description, level, life, strength, intelligence, agility, wisdom, chance, pa, pm, kamas, po, dodge_*, ini, vitality, image, size, race, res_*). Niveaux/vie/attributs/ini via formatters dofusdb_*. Résistances : batch DofusConversionService. |
| **Cibles** | creatures (nom, description, level, life, attributs, pa, pm, ini, image, res_*), monsters (dofusdb_id, size, monster_race_id). |
| **Validation** | Tous les champs creatures/monsters ayant une définition creature (monster) sont clamés puis validés. |
| **Profondeur** | grades[0] uniquement ; spells et drops gérés par les **relations**, pas par le mapping. |
| **Relations** | spells (import + lien creature_spell), drops (import item/resource/consumable + sync). |

---

### 2.2 Spell

| Aspect | Détail |
|--------|--------|
| **Config** | `entities/spell.json`. fetchOne /spells/{id}, fetchMany /spells. Pas de spell-levels dans la réponse ; ils sont récupérés à part. |
| **Structure DofusDB** | Spell racine (id, name, description, img, spellLevels[]) ; spell-levels (grade, apCost, minRange, range, effects[], criticalEffect[], zoneDescr, etc.). |
| **Normalisation** | SpellGlobalNormalizer produit spell_global (id, name, description, img, apCost, minRange, range, grade, maxCastPerTurn, maxCastPerTarget, castTestLos, elementId, categoryId, area, rangeCanBeBoosted, etc.) pour un mapping à chemins plats. |
| **Mapping (champs plats)** | ~20 règles en BDD : dofusdb_id, name, description, image, pa, spell_po_min, spell_po_max, level, cast_per_turn, cast_per_target, sight_line, element, category, area (zoneDescrToNotation), po_editable. Cibles : spells.*. |
| **Effets** | **Hors mapping** : SpellEffectsConversionService sur raw + levels. Produit spell_effects (effect_group + effects par degree avec sub_effects). effectId → sous-effet via DofusDbEffectMapping (PHP) ; valeurs via characteristic_spell. |
| **Validation** | Champs plats du modèle spells validés (getDefinitionByField pour entity spell). spell_effects n’est pas validé structurellement par le Limit (structure imbriquée). |
| **Intégration** | Spell créé/mis à jour ; integrateSpellEffectsForSpell écrit EffectGroup, Effect, EffectSubEffect, EffectUsage. |

---

### 2.3 Item (resources, consumables, items / équipements)

| Aspect | Détail |
|--------|--------|
| **Config** | `entities/item.json`. Un seul mapping pour les trois cibles (resources, consumables, items) ; au moment de l’intégration, targetModel choisit un seul bloc selon typeId. |
| **Structure DofusDB** | id, name, description, level, price, img, typeId, rarity, effects[], recipe, realWeight, etc. |
| **Mapping** | ~14 règles en BDD. Champs communs vers resources, consumables, items (dofusdb_id, name, description, level, price, image, type_id, rarity). Spécifiques : resource_type_id, weight (resources), recipe_ingredients (resources), effect et bonus (items uniquement dans les cibles, mais from.path = effects pour les deux). |
| **Conversion effect/bonus** | effect : formatter itemEffectsToKrosmozBonus (conversion via dofusdb_characteristic_to_krosmoz.json + DofusConversionService). bonus : toJson(effects) brut. |
| **Ciblage** | context.targetModel = resource | consumables | items selon le type d’item ; ConversionService ne remplit que les cibles dont model === targetModel (y compris pour le batch résistances). |
| **Validation** | Champs plats (level, name, etc.) validés pour l’entité object (item/resource/consumable). effect et bonus (JSON) non validés comme structures. |
| **Profondeur** | effects[] traité en bloc par un formatter, pas de règle par effet. |

---

### 2.4 Breed (class)

| Aspect | Détail |
|--------|--------|
| **Config** | `entities/breed.json`. fetchOne /breeds/{id}, fetchMany /breeds. DofusDB n’expose pas level, life ni attributs pour les classes. |
| **Mapping** | ~6 règles en BDD : dofusdb_id, name, description, short_description, specificity, img. Cibles : breeds.*. |
| **Relations** | Sorts liés via spell-levels (breedId) ; peuvent être résolus séparément (RelationResolutionService pour breed si implémenté). |
| **Validation** | Champs breeds validés pour entity class (breed). |

---

### 2.5 Panoply

| Aspect | Détail |
|--------|--------|
| **Config** | `entities/panoply.json`. fetchOne /item-sets/{id}, fetchMany item-sets. collectStrategy.filterOutCosmetic : true. |
| **Mapping** | **Uniquement dans le JSON** (pas de règles en BDD dans le seeder) : dofusdb_id, name, description, bonus (itemEffectsToKrosmozBonus sur effects), item_dofusdb_ids (extractItemIds sur items). |
| **Validation** | Champs panoplies pour entity panoply (groupe object). |
| **Profondeur** | effects → bonus via le même formatter que pour les items. |

---

## 3. Synthèse des écarts et points d’attention

| Thème | Constat |
|-------|--------|
| **Source du mapping** | BDD pour breed, item, monster, spell ; JSON seul pour panoply. Comportement « tout BDD ou tout JSON » par entité, pas de merge. |
| **Profondeur** | Sorts : deux flux (mapping plat + SpellEffectsConversionService). Items/Panoply : un formatter par bloc effects. Aucun modèle « règle de mapping par nœud imbriqué » avec characteristic_id. |
| **characteristic_id** | Présent en BDD (scrapping_entity_mappings) et exposé par ScrappingMappingService ; utilisé de façon limitée dans les formatters (clé résolue pour dofusdb_attribute). Pas encore de chaînage systématique « règle.characteristic_id → Getter/Conversion ». |
| **Validation** | Uniquement champs plats ayant une définition dans characteristic_creature/object/spell. Pas de validation des sous-structures (spell_effects, bonus JSON). |
| **EffectId / bonus items** | Mapping effectId → sous-effet en PHP (DofusDbEffectMapping). Mapping characteristic id → key pour les bonus items dans un JSON séparé. Pas dans scrapping_entity_mappings. |

Ce document servira de base pour établir la **liste des besoins** et le **plan d’amélioration** (voir ANALYSE_GLOBALE_REFONTE_SCRAPPING.md § 6).
