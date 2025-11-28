# Audit complet des entités KrosmozJDR

**Date** : 2025-11-27  
**Objectif** : Vérifier que toutes les entités ont leurs fichiers CRUD, factories, tests, etc. complets et fonctionnels.

## 📋 Liste des entités à vérifier

### Entités principales (15)
1. Attribute
2. Campaign
3. Capability
4. Classe
5. Consumable
6. Creature
7. Item
8. Monster
9. Npc
10. Panoply
11. Resource
12. Scenario
13. Shop
14. Specialization
15. Spell

### Types (6)
1. ConsumableType
2. ItemType
3. MonsterRace
4. ResourceType
5. ScenarioLink
6. SpellType

## ✅ Matrice d'audit

| Entité | Model | Controller | Policy | StoreRequest | UpdateRequest | Factory | Seeder | Tests | Migration | Relations |
|--------|-------|------------|--------|--------------|---------------|---------|--------|-------|-----------|-----------|
| **Attribute** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **Campaign** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **Capability** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **Classe** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **Consumable** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **Creature** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **Item** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **Monster** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **Npc** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **Panoply** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **Resource** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **Scenario** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **Shop** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **Specialization** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **Spell** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **ConsumableType** | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ✅ | ✅ |
| **ItemType** | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ✅ | ✅ |
| **MonsterRace** | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ✅ | ✅ |
| **ResourceType** | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ✅ | ✅ |
| **ScenarioLink** | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ✅ | ✅ |
| **SpellType** | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ✅ | ✅ |

**Légende** :
- ✅ : Existe et semble complet
- ❌ : N'existe pas
- ❓ : À vérifier

## 🔍 Détails par entité

### 1. Attribute
- **Model** : ✅ `app/Models/Entity/Attribute.php`
- **Controller** : ✅ `app/Http/Controllers/Entity/AttributeController.php` (CRUD complet)
- **Policy** : ✅ `app/Policies/Entity/AttributePolicy.php` (permissions complètes)
- **Requests** : ✅ Store/Update avec validation
- **Factory** : ✅ Complète
- **Tests** : ✅ `tests/Feature/Entity/AttributeModelTest.php` (5 tests)
- **Seeder** : ❌ Non nécessaire (données manuelles)
- **Migration** : ✅ `2025_06_01_100200_entity_attributes_table.php`
- **Relations** : ✅ `creatures()` (many-to-many via `attribute_creature`)

### 2. Campaign
- **Model** : ✅ `app/Models/Entity/Campaign.php`
- **Controller** : ✅ `app/Http/Controllers/Entity/CampaignController.php`
- **Policy** : ✅ `app/Policies/Entity/CampaignPolicy.php`
- **Requests** : ✅ Store/Update
- **Factory** : ✅ Complète
- **Tests** : ✅ `tests/Feature/Entity/AttributeModelTest.php` (5 tests)
- **Seeder** : ❌ Non nécessaire
- **Migration** : ✅ `2025_06_01_100240_entity_campaigns_table.php`
- **Relations** : ✅ Nombreuses relations (items, monsters, spells, panoplies, etc.)

### 3. Capability
- **Model** : ✅ `app/Models/Entity/Capability.php`
- **Controller** : ✅ `app/Http/Controllers/Entity/CapabilityController.php`
- **Policy** : ✅ `app/Policies/Entity/CapabilityPolicy.php`
- **Requests** : ✅ Store/Update
- **Factory** : ✅ Complète
- **Tests** : ✅ `tests/Feature/Entity/AttributeModelTest.php` (5 tests)
- **Seeder** : ❌ Non nécessaire
- **Migration** : ✅ `2025_06_01_100100_entity_capabilities_table.php`
- **Relations** : ✅ `creatures()`, `specializations()` (pivots)

### 4. Classe
- **Model** : ✅ `app/Models/Entity/Classe.php`
- **Controller** : ✅ `app/Http/Controllers/Entity/ClasseController.php`
- **Policy** : ✅ `app/Policies/Entity/ClassePolicy.php`
- **Requests** : ✅ Store/Update
- **Factory** : ✅ Complète
- **Tests** : ✅ `tests/Feature/Entity/ClasseModelTest.php` (5 tests)
- **Seeder** : ❌ Non nécessaire
- **Migration** : ✅ `2025_06_01_100110_entity_classes_table.php`
- **Relations** : ✅ `spells()` (many-to-many via `class_spell`), `npcs()`

### 5. Consumable
- **Model** : ✅ `app/Models/Entity/Consumable.php`
- **Controller** : ✅ `app/Http/Controllers/Entity/ConsumableController.php`
- **Policy** : ✅ `app/Policies/Entity/ConsumablePolicy.php`
- **Requests** : ✅ Store/Update
- **Factory** : ✅ Complète
- **Tests** : ✅ `tests/Feature/Entity/AttributeModelTest.php` (5 tests)
- **Seeder** : ❌ Non nécessaire
- **Migration** : ✅ `2025_06_01_100170_entity_consumables_table.php`
- **Relations** : ✅ `resources()` (many-to-many via `consumable_resource`)

### 6. Creature
- **Model** : ✅ `app/Models/Entity/Creature.php`
- **Controller** : ✅ `app/Http/Controllers/Entity/CreatureController.php`
- **Policy** : ✅ `app/Policies/Entity/CreaturePolicy.php`
- **Requests** : ✅ Store/Update
- **Factory** : ✅ Complète
- **Tests** : ✅ `tests/Feature/Entity/CreatureModelTest.php` (4 tests)
- **Seeder** : ❌ Non nécessaire
- **Migration** : ✅ `2025_06_01_100130_entity_creatures_table.php`
- **Relations** : ✅ Nombreuses relations (spells, resources, attributes, capabilities, etc.)

### 7. Item
- **Model** : ✅ `app/Models/Entity/Item.php`
- **Controller** : ✅ `app/Http/Controllers/Entity/ItemController.php`
- **Policy** : ✅ `app/Policies/Entity/ItemPolicy.php`
- **Requests** : ✅ Store/Update
- **Factory** : ✅ Complète
- **Tests** : ✅ `tests/Feature/Entity/ItemModelTest.php` (3 tests)
- **Seeder** : ❌ Non nécessaire
- **Migration** : ✅ `2025_06_01_100160_entity_items_table.php`
- **Relations** : ✅ `resources()`, `panoplies()`, `itemType()`

### 8. Monster
- **Model** : ✅ `app/Models/Entity/Monster.php`
- **Controller** : ✅ `app/Http/Controllers/Entity/MonsterController.php`
- **Policy** : ✅ `app/Policies/Entity/MonsterPolicy.php`
- **Requests** : ✅ Store/Update
- **Factory** : ✅ Complète
- **Tests** : ✅ `tests/Feature/Entity/AttributeModelTest.php` (5 tests)
- **Seeder** : ❌ Non nécessaire
- **Migration** : ✅ `2025_06_01_100220_entity_monsters_table.php`
- **Relations** : ✅ `creature()` (belongsTo)

### 9. Npc
- **Model** : ✅ `app/Models/Entity/Npc.php`
- **Controller** : ✅ `app/Http/Controllers/Entity/NpcController.php`
- **Policy** : ✅ `app/Policies/Entity/NpcPolicy.php`
- **Requests** : ✅ Store/Update
- **Factory** : ✅ Complète
- **Tests** : ✅ `tests/Feature/Entity/AttributeModelTest.php` (5 tests)
- **Seeder** : ❌ Non nécessaire
- **Migration** : ✅ `2025_06_01_100140_entity_npcs_table.php`
- **Relations** : ✅ `classe()`, `specialization()`

### 10. Panoply
- **Model** : ✅ `app/Models/Entity/Panoply.php`
- **Controller** : ✅ `app/Http/Controllers/Entity/PanoplyController.php`
- **Policy** : ✅ `app/Policies/Entity/PanoplyPolicy.php`
- **Requests** : ✅ Store/Update
- **Factory** : ✅ Complète
- **Tests** : ✅ `tests/Feature/Entity/PanoplyModelTest.php` (6 tests)
- **Seeder** : ❌ Non nécessaire
- **Migration** : ✅ `2025_06_01_100210_entity_panoplies_table.php` + `2025_11_27_153036_add_dofusdb_id_to_panoplies_table.php`
- **Relations** : ✅ `items()` (many-to-many via `item_panoply`)

### 11. Resource
- **Model** : ✅ `app/Models/Entity/Resource.php`
- **Controller** : ✅ `app/Http/Controllers/Entity/ResourceController.php`
- **Policy** : ✅ `app/Policies/Entity/ResourcePolicy.php`
- **Requests** : ✅ Store/Update
- **Factory** : ✅ Complète
- **Tests** : ✅ `tests/Feature/Entity/AttributeModelTest.php` (5 tests)
- **Seeder** : ❌ Non nécessaire
- **Migration** : ✅ `2025_06_01_100180_entity_resources_table.php`
- **Relations** : ✅ `creatures()`, `items()`, `consumables()` (pivots)

### 12. Scenario
- **Model** : ✅ `app/Models/Entity/Scenario.php`
- **Controller** : ✅ `app/Http/Controllers/Entity/ScenarioController.php`
- **Policy** : ✅ `app/Policies/Entity/ScenarioPolicy.php`
- **Requests** : ✅ Store/Update
- **Factory** : ✅ Complète
- **Tests** : ✅ `tests/Feature/Entity/AttributeModelTest.php` (5 tests)
- **Seeder** : ❌ Non nécessaire
- **Migration** : ✅ `2025_06_01_100230_entity_scenarios_table.php`
- **Relations** : ✅ Nombreuses relations (items, monsters, spells, panoplies, etc.)

### 13. Shop
- **Model** : ✅ `app/Models/Entity/Shop.php`
- **Controller** : ✅ `app/Http/Controllers/Entity/ShopController.php`
- **Policy** : ✅ `app/Policies/Entity/ShopPolicy.php`
- **Requests** : ✅ Store/Update
- **Factory** : ✅ Complète
- **Tests** : ✅ `tests/Feature/Entity/AttributeModelTest.php` (5 tests)
- **Seeder** : ❌ Non nécessaire
- **Migration** : ✅ `2025_06_01_100150_entity_shops_table.php`
- **Relations** : ✅ Nombreuses relations (items, consumables, resources, panoplies)

### 14. Specialization
- **Model** : ✅ `app/Models/Entity/Specialization.php`
- **Controller** : ✅ `app/Http/Controllers/Entity/SpecializationController.php`
- **Policy** : ✅ `app/Policies/Entity/SpecializationPolicy.php`
- **Requests** : ✅ Store/Update
- **Factory** : ✅ Complète
- **Tests** : ✅ `tests/Feature/Entity/AttributeModelTest.php` (5 tests)
- **Seeder** : ❌ Non nécessaire
- **Migration** : ✅ `2025_06_01_100120_entity_specializations_table.php`
- **Relations** : ✅ `capabilities()`, `npcs()`

### 15. Spell
- **Model** : ✅ `app/Models/Entity/Spell.php`
- **Controller** : ✅ `app/Http/Controllers/Entity/SpellController.php`
- **Policy** : ✅ `app/Policies/Entity/SpellPolicy.php`
- **Requests** : ✅ Store/Update
- **Factory** : ✅ Complète
- **Tests** : ✅ `tests/Feature/Entity/SpellModelTest.php` (5 tests)
- **Seeder** : ❌ Non nécessaire
- **Migration** : ✅ `2025_06_01_100190_entity_spells_table.php`
- **Relations** : ✅ `classes()`, `creatures()`, `monsters()`, `spellType()`

## 📊 Types (à vérifier)

### ConsumableType, ItemType, MonsterRace, ResourceType, ScenarioLink, SpellType
- **Models** : ✅ Tous existent (`app/Models/Type/`)
- **Controllers** : ❌ **MANQUANTS** - Pas de CRUD pour les types (acceptable si gestion via migrations/seeders)
- **Policies** : ❌ **MANQUANTS** (non critique si pas de CRUD)
- **Requests** : ❌ **MANQUANTS** (non critique si pas de CRUD)
- **Factories** : ✅ Toutes existent et complétées (`database/factories/Type/`)
- **Tests** : ✅ Tous créés (`tests/Feature/Type/` - 20 tests, 58 assertions)
- **Seeders** : ✅ Tous créés (`database/seeders/Type/` - 5 seeders + 1 seeder principal)
- **Migrations** : ✅ Toutes existent

## 🎯 Actions prioritaires

### Priorité 1 : Tests manquants (10 entités)
1. ✅ Classe - **FAIT**
2. ✅ Creature - **FAIT**
3. ✅ Item - **FAIT**
4. ✅ Panoply - **FAIT**
5. ✅ Spell - **FAIT**
6. ❌ Attribute - **À FAIRE**
7. ❌ Campaign - **À FAIRE**
8. ❌ Capability - **À FAIRE**
9. ❌ Consumable - **À FAIRE**
10. ❌ Monster - **À FAIRE**
11. ❌ Npc - **À FAIRE**
12. ❌ Resource - **À FAIRE**
13. ❌ Scenario - **À FAIRE**
14. ❌ Shop - **À FAIRE**
15. ❌ Specialization - **À FAIRE**

### Priorité 2 : Vérifier les factories des Types
- Vérifier si les factories existent pour tous les types
- Créer si manquantes

### Priorité 3 : Seeders pour les Types (optionnel)
- Créer des seeders pour les types de base si nécessaire

### Priorité 4 : CRUD pour les Types (optionnel)
- Créer controllers, policies, requests pour les types si nécessaire pour l'administration

## 📝 Notes

- Les **Seeders** pour les entités principales ne sont pas nécessaires car les données sont créées manuellement ou via scrapping
- Les **Seeders** pour les **Types** pourraient être utiles pour initialiser les données de base (ex: types d'items, races de monstres)
- Les **Tests** sont essentiels pour garantir la stabilité du système
- Les **Types** n'ont pas de CRUD actuellement, ce qui peut être acceptable si la gestion se fait uniquement via migrations/seeders

## 🔗 Tables pivot (45 migrations)

Toutes les tables pivot sont présentes et correctement configurées avec :
- ✅ Foreign keys avec `cascadeOnDelete()`
- ✅ Primary keys composites
- ✅ Colonnes pivot supplémentaires quand nécessaire (`quantity`, `price`, `comment`, etc.)

**Liste complète des pivots** :
- `attribute_creature` - Attributes ↔ Creatures
- `capability_creature` - Capabilities ↔ Creatures
- `capability_specialization` - Capabilities ↔ Specializations
- `campaign_panoply` - Campaigns ↔ Panoplies
- `campaign_page` - Campaigns ↔ Pages
- `campaign_scenario` - Campaigns ↔ Scenarios
- `campaign_shop` - Campaigns ↔ Shops
- `campaign_spell` - Campaigns ↔ Spells
- `campaign_user` - Campaigns ↔ Users
- `class_spell` - Classes ↔ Spells
- `consumable_campaign` - Consumables ↔ Campaigns
- `consumable_creature` - Consumables ↔ Creatures
- `consumable_resource` - Consumables ↔ Resources
- `consumable_scenario` - Consumables ↔ Scenarios
- `consumable_shop` - Consumables ↔ Shops
- `creature_item` - Creatures ↔ Items
- `creature_resource` - Creatures ↔ Resources
- `creature_spell` - Creatures ↔ Spells
- `file_campaign` - Files ↔ Campaigns
- `file_scenario` - Files ↔ Scenarios
- `file_section` - Files ↔ Sections
- `item_campaign` - Items ↔ Campaigns
- `item_panoply` - Items ↔ Panoplies
- `item_resource` - Items ↔ Resources
- `item_scenario` - Items ↔ Scenarios
- `item_shop` - Items ↔ Shops
- `monster_campaign` - Monsters ↔ Campaigns
- `monster_scenario` - Monsters ↔ Scenarios
- `npc_campaign` - NPCs ↔ Campaigns
- `npc_panoply` - NPCs ↔ Panoplies
- `npc_scenario` - NPCs ↔ Scenarios
- `page_user` - Pages ↔ Users
- `panoply_shop` - Panoplies ↔ Shops
- `resource_campaign` - Resources ↔ Campaigns
- `resource_scenario` - Resources ↔ Scenarios
- `resource_shop` - Resources ↔ Shops
- `scenario_link` - Scenarios ↔ Scenarios (liens entre scénarios)
- `scenario_page` - Scenarios ↔ Pages
- `scenario_shop` - Scenarios ↔ Shops
- `scenario_spell` - Scenarios ↔ Spells
- `scenario_user` - Scenarios ↔ Users
- `section_user` - Sections ↔ Users
- `spell_invocation` - Spells ↔ Monsters (invocations)
- `spell_type` - Spells ↔ SpellTypes

**Toutes les relations sont correctement implémentées dans les modèles** ✅

## 🔄 Prochaines étapes

### Priorité 1 : Tests manquants (10 entités principales) ✅ **TERMINÉ**
1. ✅ Attribute - **FAIT** (`AttributeModelTest.php` - 5 tests)
2. ✅ Campaign - **FAIT** (`CampaignModelTest.php` - 5 tests)
3. ✅ Capability - **FAIT** (`CapabilityModelTest.php` - 4 tests)
4. ✅ Consumable - **FAIT** (`ConsumableModelTest.php` - 4 tests)
5. ✅ Monster - **FAIT** (`MonsterModelTest.php` - 3 tests)
6. ✅ Npc - **FAIT** (`NpcModelTest.php` - 5 tests)
7. ✅ Resource - **FAIT** (`ResourceModelTest.php` - 5 tests)
8. ✅ Scenario - **FAIT** (`ScenarioModelTest.php` - 4 tests)
9. ✅ Shop - **FAIT** (`ShopModelTest.php` - 5 tests)
10. ✅ Specialization - **FAIT** (`SpecializationModelTest.php` - 4 tests)

**Total : 47 tests passent (118 assertions)**

### Priorité 2 : Tests pour les Types (6 types) ✅ **TERMINÉ**
1. ✅ ConsumableType - **FAIT** (`ConsumableTypeModelTest.php` - 3 tests)
2. ✅ ItemType - **FAIT** (`ItemTypeModelTest.php` - 3 tests)
3. ✅ MonsterRace - **FAIT** (`MonsterRaceModelTest.php` - 5 tests)
4. ✅ ResourceType - **FAIT** (`ResourceTypeModelTest.php` - 3 tests)
5. ✅ ScenarioLink - **FAIT** (`ScenarioLinkModelTest.php` - 3 tests)
6. ✅ SpellType - **FAIT** (`SpellTypeModelTest.php` - 3 tests)

**Total : 20 tests passent (58 assertions)**

### Priorité 3 : Seeders pour les Types ✅ **TERMINÉ**
- ✅ `ItemTypeSeeder` - 17 types d'items créés (Armes, Accessoires, Équipements)
- ✅ `ConsumableTypeSeeder` - 9 types de consommables créés
- ✅ `MonsterRaceSeeder` - 14 races de monstres créées (avec hiérarchie)
- ✅ `ResourceTypeSeeder` - 14 types de ressources créés
- ✅ `SpellTypeSeeder` - 8 types de sorts créés (avec couleurs et descriptions)
- ✅ `TypeSeeder` - Seeder principal qui appelle tous les seeders de types
- ✅ Intégré dans `DatabaseSeeder`

### Priorité 4 : CRUD pour les Types (optionnel)
- Décider si un CRUD est nécessaire pour les Types (actuellement géré via migrations/seeders)

