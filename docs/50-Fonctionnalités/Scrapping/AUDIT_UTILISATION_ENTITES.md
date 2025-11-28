# Audit : Utilisation des entités et fonctionnalités dans le scrapping

**Date** : 2025-01-27

## 📋 Objectif

Vérifier que le système de scrapping utilise bien toutes les entités et toutes les nouvelles fonctionnalités disponibles dans KrosmozJDR.

## ✅ Entités supportées par le scrapping

### 1. Classes (`Classe`)
- ✅ **Supporté** : `importClass()`
- ✅ **Relations** : Sorts (`class_spell`)
- ✅ **Champs utilisés** : `dofusdb_id`, `name`, `description`, `life`, `life_dice`, `specificity`
- ⚠️ **Champs manquants** : Aucun (tous les champs importants sont utilisés)

### 2. Monstres (`Monster` / `Creature`)
- ✅ **Supporté** : `importMonster()`
- ✅ **Relations** : Sorts (`creature_spell`), Ressources (`creature_resource`)
- ✅ **Champs utilisés** : `dofusdb_id`, `name`, `level`, `life`, stats, `size`
- ✅ **Types utilisés** : `monster_race_id` (déjà assigné)
- ⚠️ **Champs manquants** : Aucun (tous les champs importants sont utilisés)

### 3. Items (`Item`)
- ✅ **Supporté** : `importItem()`
- ✅ **Relations** : Ressources de recette (`item_resource`)
- ✅ **Champs utilisés** : `dofusdb_id`, `name`, `description`, `level`, `price`, `rarity`
- ❌ **Types NON utilisés** : `item_type_id` n'est **PAS assigné** lors de l'intégration
- ⚠️ **Champs manquants** : `image`, `effect`, `bonus`, `recipe` (champs optionnels mais présents dans le modèle)

### 4. Consommables (`Consumable`)
- ✅ **Supporté** : `importItem()` (routé vers `integrateConsumable()`)
- ✅ **Relations** : Ressources (`consumable_resource`)
- ✅ **Champs utilisés** : `name`, `description`, `level`, `price`, `rarity`
- ❌ **Types NON utilisés** : `consumable_type_id` n'est **PAS assigné** lors de l'intégration
- ⚠️ **Champs manquants** : `dofusdb_id`, `image`, `effect`, `recipe` (champs optionnels mais présents dans le modèle)

### 5. Ressources (`Resource`)
- ✅ **Supporté** : `importItem()` (routé vers `integrateResource()`)
- ✅ **Relations** : Items (`item_resource`), Consommables (`consumable_resource`), Créatures (`creature_resource`)
- ✅ **Champs utilisés** : `name`, `description`, `level`, `price`, `rarity`
- ❌ **Types NON utilisés** : `resource_type_id` n'est **PAS assigné** lors de l'intégration
- ⚠️ **Champs manquants** : `dofusdb_id`, `image`, `weight` (champs optionnels mais présents dans le modèle)

### 6. Sorts (`Spell`)
- ✅ **Supporté** : `importSpell()`
- ✅ **Relations** : Monstres invoqués (`spell_invocation`), Classes (`class_spell`), Créatures (`creature_spell`)
- ✅ **Champs utilisés** : `name`, `description`, `pa`, `po`, `area`
- ❌ **Types NON utilisés** : `spellTypes` (many-to-many) n'est **PAS correctement assigné** - la méthode `integrateSpellLevels()` ne fait que logger
- ⚠️ **Champs manquants** : `dofusdb_id`, `image`, `effect`, `level`, `element`, `category`, `is_magic`, `powerful`, etc. (beaucoup de champs optionnels non utilisés)

### 7. Panoplies (`Panoply`)
- ✅ **Supporté** : `importPanoply()`
- ✅ **Relations** : Items (`item_panoply`)
- ✅ **Champs utilisés** : `dofusdb_id`, `name`, `description`, `bonus`, `usable`, `is_visible`
- ⚠️ **Champs manquants** : Aucun (tous les champs importants sont utilisés)

## 🔍 Problèmes identifiés

### 1. ❌ Types non assignés

#### ItemType (`item_type_id`)
- **Problème** : Le `typeId` de DofusDB est converti en `type` et `category` (ex: 'weapon', 'ring', 'amulet'), mais n'est **jamais mappé vers un `ItemType`** dans la base de données.
- **Impact** : Les items importés n'ont pas de type assigné, ce qui limite les fonctionnalités de filtrage et de recherche.
- **Solution** : Créer un mapping `typeId` → `ItemType` et assigner `item_type_id` dans `integrateGenericItem()`.

#### ConsumableType (`consumable_type_id`)
- **Problème** : Les consommables sont identifiés par `typeId` 12, 13, 14, mais le `consumable_type_id` n'est **jamais assigné**.
- **Impact** : Les consommables importés n'ont pas de type assigné.
- **Solution** : Créer un mapping `typeId` → `ConsumableType` et assigner `consumable_type_id` dans `integrateConsumable()`.

#### ResourceType (`resource_type_id`)
- **Problème** : Les ressources sont identifiées par `typeId` 15, 35, mais le `resource_type_id` n'est **jamais assigné**.
- **Impact** : Les ressources importées n'ont pas de type assigné.
- **Solution** : Créer un mapping `typeId` → `ResourceType` et assigner `resource_type_id` dans `integrateResource()`.

#### SpellType (many-to-many)
- **Problème** : La méthode `integrateSpellLevels()` ne fait que logger les niveaux, mais n'assign **jamais les types de sorts** via la relation `spellTypes()`.
- **Impact** : Les sorts importés n'ont pas de types assignés.
- **Solution** : Implémenter la logique d'assignation des `SpellType` dans `integrateSpellLevels()` ou créer une méthode dédiée.

### 2. ⚠️ Champs optionnels non utilisés

#### Items, Consumables, Resources
- `dofusdb_id` : Parfois manquant dans `integrateConsumable()` et `integrateResource()`
- `image` : Jamais assigné (mais présent dans les données DofusDB)
- `effect`, `bonus`, `recipe` : Jamais assignés (mais présents dans les données DofusDB)

#### Spells
- `dofusdb_id` : Jamais assigné
- `image` : Jamais assigné
- `level`, `element`, `category`, `is_magic`, `powerful`, etc. : Beaucoup de champs optionnels non utilisés

### 3. ✅ Types correctement utilisés

#### MonsterRace (`monster_race_id`)
- ✅ **Utilisé** : Assigné dans `integrateMonster()` depuis `$convertedData['monsters']['monster_race_id']`
- ✅ **Source** : `$rawData['race']` ou `$rawData['monster_race_id']` dans `convertMonster()`

## 📊 Mapping nécessaire

### ItemType mapping (typeId → ItemType name)
```php
1-8, 19-20 => 'Arc', 'Bouclier', 'Bâton', 'Dague', 'Épée', 'Marteau', 'Pelle', 'Hache', 'Outil'
9 => 'Anneau'
10 => 'Amulette'
11 => 'Ceinture'
13 => 'Bottes'
14 => 'Chapeau'
16-17 => 'Cape', 'Familier'
18 => 'Familier'
```

### ConsumableType mapping (typeId → ConsumableType name)
```php
12 => 'Potion'
13 => 'Parchemin d\'expérience' (ou autre selon le contexte)
14 => 'Objet de dons' (ou autre selon le contexte)
```

### ResourceType mapping (typeId → ResourceType name)
```php
15 => 'Minerai' (ou autre selon le contexte)
35 => 'Fleur' (ou autre selon le contexte)
```

### SpellType mapping
- Nécessite une analyse des données DofusDB pour déterminer comment mapper les sorts vers les types de sorts KrosmozJDR.

## 🎯 Actions à effectuer

### Priorité HAUTE ✅ TERMINÉ
1. ✅ Assigner `item_type_id` dans `integrateGenericItem()`
2. ✅ Assigner `consumable_type_id` dans `integrateConsumable()`
3. ✅ Assigner `resource_type_id` dans `integrateResource()`
4. ✅ Assigner `dofusdb_id` dans toutes les méthodes d'intégration (items, consumables, resources, spells)

### Priorité MOYENNE ✅ TERMINÉ
5. ✅ Implémenter l'assignation des `SpellType` dans `integrateSpellLevels()`
   - Méthode `determineSpellTypes()` créée pour analyser les effets et assigner les types
   - Détection automatique : Invocation, Soin, Offensif, Buff, Debuff, Défensif (par défaut)
6. ✅ Assigner les champs optionnels (`image`, `effect`, `bonus`, `recipe`) si présents dans les données DofusDB
   - `image` : Assigné depuis `rawData['img']`
   - `effect` : Converti depuis `rawData['effects']` via `convertEffects()`
   - `bonus` : Converti depuis `rawData['effects']` via `convertBonus()`
   - `recipe` : Préservé depuis `rawData['recipe']` (déjà géré)

### Priorité BASSE ⚠️ PARTIELLEMENT TERMINÉ
7. ⚠️ Assigner les autres champs optionnels des sorts (`level`, `element`, `category`, etc.)
   - `level` : ✅ Assigné
   - `element`, `category`, `is_magic`, `powerful` : ⚠️ Non assignés (nécessitent une analyse plus approfondie des données DofusDB)

## 📝 Notes

- Les seeders pour les types sont déjà créés et fonctionnels.
- Les modèles ont tous les relations nécessaires définies.
- Le problème principal est que les types ne sont **jamais assignés** lors de l'intégration, même si les données sont disponibles.

