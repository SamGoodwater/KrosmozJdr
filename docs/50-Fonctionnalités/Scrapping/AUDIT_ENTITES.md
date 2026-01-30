# Audit des entités pour le système de scrapping

## 📋 Objectif

Vérifier que toutes les entités nécessaires sont supportées par le système de scrapping. Le but est de récupérer les données depuis DofusDB pour les transformer en entités KrosmozJDR.

## ✅ Entités actuellement supportées

### 1. Classes (`Classe`)
- **Status** : ✅ Supporté
- **Méthode** : `importClass()`
- **Endpoint DofusDB** : `/breeds/{id}`
- **Relations** : 
  - Sorts (`class_spell`)
- **Champs importés** : name, description, life, life_dice, specificity, dofusdb_id

### 2. Monstres (`Monster` / `Creature`)
- **Status** : ✅ Supporté
- **Méthode** : `importMonster()`
- **Endpoint DofusDB** : `/monsters/{id}`
- **Relations** : 
  - Sorts (`creature_spell`)
  - Ressources/Drops (`creature_resource`)
- **Champs importés** : name, level, life, stats (strength, intelligence, agility, etc.), size

### 3. Items (`Item`, `Consumable`, `Resource`)
- **Status** : ✅ Supporté
- **Méthode** : `importItem()`
- **Endpoint DofusDB** : `/items/{id}`
- **Mapping** : 
  - Type 15, 35 → `Resource`
  - Type 12, 13, 14 → `Consumable`
  - Autres → `Item`
- **Relations** : 
  - Ressources de recette (`item_resource`)
- **Champs importés** : name, description, level, rarity, price, type, category

### 4. Sorts (`Spell`)
- **Status** : ✅ Supporté
- **Méthode** : `importSpell()`
- **Endpoint DofusDB** : `/spells` (pagination)
- **Relations** : 
  - Monstres invoqués (`spell_invocation`)
- **Champs importés** : name, description, cost (pa), range (po), area

## ❌ Entités non supportées

### 1. Attributes (`Attribute`)
- **Status** : ❌ Non nécessaire (créé manuellement)
- **Raison** : Les attributs sont des données créées manuellement, pas disponibles sur DofusDB
- **Action** : Aucune action nécessaire pour le scrapping

### 2. Campaigns (`Campaign`)
- **Status** : ❌ Non nécessaire (créé manuellement)
- **Raison** : Les campagnes sont créées manuellement par les Game Masters, pas disponibles sur DofusDB
- **Action** : Aucune action nécessaire pour le scrapping

### 3. Capabilities (`Capability`)
- **Status** : ❌ Non nécessaire (créé manuellement)
- **Raison** : Les capacités sont des données créées manuellement, pas disponibles sur DofusDB
- **Action** : Aucune action nécessaire pour le scrapping

### 4. NPCs (`Npc`)
- **Status** : ❌ Non nécessaire (créé manuellement)
- **Raison** : Les NPCs sont créés manuellement, pas disponibles sur DofusDB
- **Action** : Aucune action nécessaire pour le scrapping

### 5. Panoplies (`Panoply`) ✅ **IMPLÉMENTÉE**
- **Status** : ✅ Supporté
- **Disponible sur DofusDB** : ✅ Oui
- **Endpoint DofusDB** : `/item-sets/{id}`
- **Méthode** : `importPanoply()`
- **Relations** : 
  - Items (via `item_panoply`) - ✅ Import en cascade et synchronisation
  - Campaigns, Scenarios, Shops, NPCs (via pivots) - Créées manuellement
- **Champs importés** : name, description, bonus, state, read_level, write_level, dofusdb_id

### 6. Scenarios (`Scenario`)
- **Status** : ❌ Non nécessaire (créé manuellement)
- **Raison** : Les scénarios sont créés manuellement par les Game Masters, pas disponibles sur DofusDB
- **Action** : Aucune action nécessaire pour le scrapping

### 7. Shops (`Shop`)
- **Status** : ❌ Non nécessaire (créé manuellement)
- **Raison** : Les boutiques sont créées manuellement, pas disponibles sur DofusDB
- **Action** : Aucune action nécessaire pour le scrapping

### 8. Specializations (`Specialization`)
- **Status** : ❌ Non nécessaire (créé manuellement)
- **Raison** : Les spécialisations sont créées manuellement, pas disponibles sur DofusDB
- **Action** : Aucune action nécessaire pour le scrapping

## 🔍 Vérifications à effectuer

### 1. Documentation DofusDB
- [ ] Vérifier tous les endpoints disponibles dans l'API DofusDB
- [ ] Identifier quelles entités peuvent être récupérées depuis DofusDB
- [ ] Documenter les endpoints manquants ou non utilisés

### 2. Relations entre entités
- [ ] Vérifier toutes les relations Eloquent dans les modèles
- [ ] Identifier les relations qui nécessitent des imports en cascade
- [ ] S'assurer que toutes les relations sont gérées lors de l'import

### 3. Champs manquants
- [ ] Pour chaque entité supportée, vérifier que tous les champs importants sont importés
- [ ] Identifier les champs qui pourraient être utiles mais ne sont pas encore importés
- [ ] Vérifier la cohérence des données importées

### 4. Tests
- [ ] Créer des tests pour chaque entité supportée
- [ ] Vérifier que les relations sont bien créées
- [ ] Tester les cas limites (entités inexistantes, données manquantes, etc.)

## 📊 Tableau récapitulatif

| Entité | Supporté | Priorité | Endpoint DofusDB | Relations | Notes |
|--------|----------|----------|------------------|-----------|-------|
| Classe | ✅ | Haute | `/breeds/{id}` | Sorts | Implémenté |
| Monster | ✅ | Haute | `/monsters/{id}` | Sorts, Ressources | Implémenté |
| Item | ✅ | Haute | `/items/{id}` | Ressources (recette) | Implémenté |
| Spell | ✅ | Haute | `/spells` | Monstres (invocation) | Implémenté |
| Panoply | ✅ | Haute | `/item-sets/{id}` | Items | Implémenté |
| Attribute | ❌ | N/A | N/A | Creatures | Créé manuellement |
| Campaign | ❌ | N/A | N/A | Scenarios, Users | Créé manuellement |
| Capability | ❌ | N/A | N/A | Creatures | Créé manuellement |
| Npc | ❌ | N/A | N/A | - | Créé manuellement |
| Scenario | ❌ | N/A | N/A | Campaigns, Users | Créé manuellement |
| Shop | ❌ | N/A | N/A | Items, Consumables | Créé manuellement |
| Specialization | ❌ | N/A | N/A | Classes | Créé manuellement |

## 🎯 Prochaines étapes

1. ✅ **Audit DofusDB** : Terminé - Seules les Panoplies sont disponibles en plus des entités déjà supportées
2. ✅ **Implémentation Panoplies** : **TERMINÉE**
   - ✅ `collectPanoply()` créée dans `DataCollectService`
   - ✅ `convertPanoply()` créée dans `DataConversionService`
   - ✅ `integratePanoply()` créée dans `DataIntegrationService`
   - ✅ `importPanoply()` créée dans `ScrappingOrchestrator`
   - ✅ Tests créés et passent
3. ✅ **Tests** : Tests créés pour les panoplies (6 tests dans `PanoplyModelTest`, 1 test dans `ItemModelTest`)
4. ✅ **Documentation** : Documentation mise à jour avec les panoplies

## 📝 Résumé

- **Entités supportées** : 5 (Classes, Monstres, Items, Sorts, **Panoplies**) ✅
- **Entités à implémenter** : 0 ✅
- **Entités créées manuellement** : 7 (Attributes, Campaigns, Capabilities, NPCs, Scenarios, Shops, Specializations)

