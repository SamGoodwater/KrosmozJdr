# Plan de refonte — Service de scrapping

**Date :** 2026-03-03  
**Références :** [ANALYSE_GLOBALE_REFONTE_SCRAPPING.md](./ANALYSE_GLOBALE_REFONTE_SCRAPPING.md), [ETAT_DES_LIEUX_REFONTE_SCRAPPING.md](./ETAT_DES_LIEUX_REFONTE_SCRAPPING.md), [BESOINS_CARACTERISTIQUES_ET_MAPPING.md](./BESOINS_CARACTERISTIQUES_ET_MAPPING.md).

---

## 1. Principes du plan

- **Pas de big-bang** : avancer par phases, en gardant le pipeline fonctionnel après chaque étape.
- **Fallback conservé** : si le mapping BDD est vide (tests, première install), le mapping JSON reste utilisé.
- **Races et types** : ne pas modifier les tables existantes (item_types, resource_types, monster_races) ; s’appuyer sur l’existant.
- **Validation** : reste pilotée par les caractéristiques (CharacteristicLimitService) ; pas de changement de fond.
- **UI** : priorité explicite (mettre le paquet sur l’UI).

---

## 2. Vue d’ensemble des phases

| Phase | Objectif | Dépendances |
|-------|----------|-------------|
| **Phase 1** | Modèle de données (BDD) pour le mapping et les caractéristiques | — |
| **Phase 2** | Pipeline : lire le mapping depuis le service Caractéristiques, dofusdb_characteristic_id, effectId en BDD | Phase 1 |
| **Phase 3** | UI admin (caractéristiques + mapping, catalogues) et page scrapping | Phase 1, idéalement Phase 2 |
| **Phase 4** | Recettes : extension du pivot (résultat équipement/ressource/consommable ↔ ingrédients) | Phase 1 (schéma) |
| **Phase 5** | Consolidation, tests, documentation | Toutes |

---

## 3. Phase 1 — Modèle de données (BDD)

**Objectif :** Avoir en BDD tout ce qui sert au mapping et à la conversion, sans encore changer le comportement du pipeline (qui continue d’utiliser l’existant).

### 3.1 Caractéristiques : dofusdb_characteristic_id (M2)

- **Migration** : ajouter la colonne `dofusdb_characteristic_id` (nullable, integer ou bigInteger) sur :
  - `characteristic_creature`
  - `characteristic_object`
  - `characteristic_spell`
- **Seeder / données** : remplir les lignes existantes à partir de la référence [DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md](../Characteristics-DB/DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md) ou du fichier `dofusdb_characteristic_to_krosmoz.json` (pour object).
- **Service** : exposer dans CharacteristicGetterService (ou équivalent) la résolution « dofusdb_characteristic_id → characteristic key » pour le groupe object (et les autres si pertinent).

**Livrable :** Colonne en place, données de référence remplies, résolution id → caractéristique disponible côté service.

### 3.2 Règles de mapping (M1, M5, M6)

- **Décision** : soit **faire évoluer** `scrapping_entity_mappings` (s’assurer que `characteristic_id` est systématiquement renseigné pour les règles « pilotées par caractéristique », ajouter `spell_level_aggregation` si besoin), soit **créer** une table `characteristic_dofusdb_mapping_rules` et migrer progressivement.
- **Colonnes à avoir** (sur la table retenue) : source, entity (monster, spell, item, panoply, breed), characteristic_id (FK), from_path, formatters (JSON), spell_level_aggregation (nullable : first | max | min | last), sort_order. Cibles (target_model, target_field) : soit déduites de la caractéristique (db_column, groupe), soit stockées (comme aujourd’hui dans scrapping_entity_mapping_targets).
- **Panoply** : ajouter les règles de mapping pour panoply en BDD (alignement avec item, bonus).

**Livrable :** Une seule source de vérité pour les règles « chemin → caractéristique → cible » ; panoply inclus.

### 3.3 Catalogue effectId → sous-effet (M4)

- **Migration** : créer la table `dofusdb_effect_mapping` (ou nom équivalent) : dofusdb_effect_id (int), sub_effect_id (FK vers sub_effects) ou sub_effect_slug (string), characteristic_source (element | none), sort_order.
- **Seeder** : alimenter à partir de l’existant (DofusDbEffectMapping en PHP : effectId 96–100 → frapper + element).
- **Service** : DofusdbEffectMappingService (ou équivalent) lit depuis la BDD au lieu de la constante PHP ; fallback sur « autre » si effectId inconnu.

**Livrable :** Table en place, données de base, service qui lit en BDD avec fallback.

### 3.4 Points de contrôle Phase 1

- Migrations exécutables sans casser l’existant.
- Les seeders / données permettent de retrouver le comportement actuel (mapping, effectId).
- Aucun changement obligatoire du pipeline : ConfigLoader peut toujours utiliser ScrappingMappingService comme aujourd’hui (déjà en BDD).

### 3.5 Implémenté (2026-03-03)

- **3.1** : Migration `2026_03_03_100000_add_dofusdb_characteristic_id_to_characteristic_group_tables.php` (colonnes sur characteristic_creature, characteristic_object, characteristic_spell). Modèles mis à jour (fillable, casts). Seeder `DofusdbCharacteristicIdSeeder` (remplit object depuis `dofusdb_characteristic_to_krosmoz.json`). `CharacteristicGetterService::getCharacteristicKeyByDofusdbCharacteristicId(int $dofusdbCharacteristicId, string $group)` pour la résolution id → characteristic_key.
- **3.2** : Migration `2026_03_03_100001_add_spell_level_aggregation_to_scrapping_entity_mappings.php`. Modèle `ScrappingEntityMapping` : `spell_level_aggregation` en fillable. Règles panoply et migration des règles existantes à faire en Phase 3 (UI) ou manuellement.
- **3.3** : Table `dofusdb_effect_mappings` et `DofusdbEffectMappingSeeder` (effectId 96–100) déjà en place ; `DofusdbEffectMappingService` lit en BDD avec fallback PHP.

---

## 4. Phase 2 — Pipeline (mapping et conversion)

**Objectif :** Le pipeline s’appuie sur le modèle de données (règles liées aux caractéristiques, dofusdb_characteristic_id, effectId en BDD) ; suppression progressive des sources dispersées (JSON dofusdb_characteristic_to_krosmoz, constante PHP effectId).

### 4.1 Chargement du mapping

- ConfigLoader : **priorité au mapping BDD** (déjà le cas via ScrappingMappingService). S’assurer que les règles chargées depuis `scrapping_entity_mappings` (ou la nouvelle table) exposent bien `characteristic_id` / `characteristic_key` pour que FormatterApplicator et DofusConversionService les utilisent.
- **Fallback** : si aucune règle en BDD pour (source, entity), garder le chargement du mapping depuis le JSON d’entité (comportement actuel).

### 4.2 Bonus items et panoply (M2)

- Remplacer l’usage de `dofusdb_characteristic_to_krosmoz.json` par la résolution via **dofusdb_characteristic_id** sur les caractéristiques (groupe object). Le formatter `itemEffectsToKrosmozBonus` (ou le service qui convertit les effects[]) appelle le service Caractéristiques pour résoudre id → caractéristique et récupérer la formule.
- Même logique pour panoply (bonus).

### 4.3 Effets de sorts (M4)

- SpellEffectsConversionService (ou DofusdbEffectMappingService) lit le mapping **effectId → sous-effet** depuis la table `dofusdb_effect_mapping` au lieu de la constante PHP. Supprimer ou déprécier DofusDbEffectMapping (constante).

### 4.4 Formatters et characteristic_id

- S’assurer que les formatters **dofusdb_*** et **clampToCharacteristic** utilisent en priorité le `characteristic_id` / `characteristic_key` de la règle de mapping (déjà partiellement en place via `_resolvedCharacteristicKey`). Étendre si des règles n’étaient pas encore branchées.

### 4.5 Normalisation

- **Conserver** la phase de normalisation (SpellGlobalNormalizer) pour les sorts ; pas de changement si la structure DofusDB reste la même.

### 4.6 Points de contrôle Phase 2

- Les tests existants (scrapping, conversion, intégration) passent.
- Bonus items et panoply restent convertis correctement (résolution par dofusdb_characteristic_id).
- Effets de sorts : mapping effectId depuis la BDD.
- En environnement sans données de mapping en BDD (ou avec seeder minimal), le fallback JSON fonctionne.

### 4.7 Implémenté (2026-03-03)

- **4.1** : ScrappingMappingService exposait déjà `characteristic_id` et `characteristic_key` dans les règles ; ConversionService passe `mappingRule` au context ; FormatterApplicator utilise `_resolvedCharacteristicKey` pour dofusdb_attribute et clampToCharacteristic. Aucun changement nécessaire.
- **4.2** : `FormatterApplicator::itemEffectsToKrosmozBonus` utilise en priorité `CharacteristicGetterService::getCharacteristicKeyByDofusdbCharacteristicId($charId, 'object')` ; fallback sur `dofusdb_characteristic_to_krosmoz.json` si BDD vide ou getter absent. Méthode `loadDofusdbCharacteristicToKrosmozJsonMapping()` pour le fallback. Items et panoply (même formatter, entityType item/panoply) convertis via la résolution BDD.
- **4.3** : Classe `DofusDbEffectMapping` (constante PHP) marquée `@deprecated` ; DofusdbEffectMappingService continue de l’utiliser en fallback lorsque la table est vide.
- **4.4** : Formatters dofusdb_* et clampToCharacteristic utilisent déjà la règle de mapping via `resolveCharacteristicKeyFromContext` et `_resolvedCharacteristicKey`. Aucune extension nécessaire.
- **4.5** : Aucun changement (SpellGlobalNormalizer conservé).

---

## 5. Phase 3 — UI

**Objectif :** Mettre le paquet sur l’UI : admin caractéristiques (3 panneaux dont Mapping), admin des règles de mapping / catalogues, page scrapping (preview, batch) lisible et maintenable.

### 5.1 Admin caractéristiques

- Structurer l’écran en **3 panneaux** (ou équivalent) : Limite/valeur, Conversion (formule, graphe, échantillons), **Mapping** (règles DofusDB qui utilisent cette caractéristique, ou lien vers l’écran mapping).
- Exposer **dofusdb_characteristic_id** en édition (pour les lignes de groupe creature/object/spell).
- S’appuyer sur [ARCHITECTURE_SCRAPPING_MAPPING_CARACTERISTIQUES.md](../ARCHITECTURE_SCRAPPING_MAPPING_CARACTERISTIQUES.md) et [VISION_UI_ADMIN_MAPPING_ET_CARACTERISTIQUES.md](../VISION_UI_ADMIN_MAPPING_ET_CARACTERISTIQUES.md).

### 5.2 Admin règles de mapping et catalogues

- **Règles** : CRUD des règles (source, entity, characteristic, from_path, formatters, spell_level_aggregation, cibles). Liste filtrable par source/entité. Lien vers la fiche caractéristique.
- **EffectId** : écran (ou section) pour gérer le catalogue dofusdb_effect_mapping (effect_id → sub_effect, characteristic_source).
- Pas de duplication des types/races : les tables existantes restent la référence ; ajouter si besoin un lien dofusdb_id sur les types pour l’affichage ou le filtre.

### 5.3 Page scrapping (preview, batch)

- Refactoriser / découper selon [REFONTE_SCRAPPING_ANALYSE_ET_PLAN.md](./REFONTE_SCRAPPING_ANALYSE_ET_PLAN.md) (composables, configs, composants) pour améliorer la maintenabilité.
- Conserver les fonctionnalités décrites dans [SPEC_UI_SCRAPPING.md](./SPEC_UI_SCRAPPING.md).
- Priorité : clarté, lisibilité, possibilité de gérer le mapping et les caractéristiques depuis l’UI sans éditer du JSON.

### 5.4 Points de contrôle Phase 3

- Un admin peut créer/éditer une règle de mapping et la lier à une caractéristique.
- Un admin peut renseigner dofusdb_characteristic_id sur une caractéristique et voir les règles associées.
- La page scrapping permet toujours preview, batch, comparaison, import ; le code est plus maintenable.

---

## 6. Phase 4 — Recettes (pivot étendu)

**Objectif :** Les recettes sont des relations (résultat : équipement / ressource / consommable) ↔ (ingrédients : ressources). Aujourd’hui seul resource → resources est couvert par `resource_recipe`. Étendre si les recettes DofusDB concernent aussi items et consommables.

### 6.1 Analyse

- Vérifier dans l’API DofusDB et les imports actuels : les recettes sont-elles uniquement pour les ressources, ou aussi pour items/consommables ?
- Si uniquement ressources : **rien à faire** (resource_recipe suffit). Si items/consommables ont des recettes : passer à 6.2.

### 6.2 Schéma

- **Option A** : Pivot générique (ex. `recipe_ingredients` : result_type (resource, consumable, item), result_id, ingredient_resource_id, quantity). Une table pour tous les types de résultat.
- **Option B** : Tables dédiées `consumable_recipe`, `item_recipe` sur le même modèle que `resource_recipe` (result_id, ingredient_resource_id, quantity).
- Migration + modèles Eloquent + synchronisation dans IntegrationService (comme syncResourceRecipe aujourd’hui) pour les entités concernées.

### 6.3 Points de contrôle Phase 4

- Les recettes importées pour les ressources restent fonctionnelles.
- Si extension : les recettes pour items/consommables sont persistées en pivot et exploitables (affichage, édition).

---

## 7. Phase 5 — Consolidation, tests, documentation

**Objectif :** Stabiliser, documenter, éviter les régressions.

### 7.1 Tests

- Conserver et faire passer les tests existants (ScrappingRunCommandTest, tests unitaires Conversion, FormatterApplicator, etc.).
- Ajouter ou compléter les tests pour : résolution dofusdb_characteristic_id → caractéristique ; chargement des règles depuis la BDD ; fallback JSON quand BDD vide.
- Tests d’intégration sur un scénario complet (collecte → conversion → validation → intégration) pour au moins une entité par type (monster, spell, item, panoply, breed).

### 7.2 Documentation

- Mettre à jour [ETAT_AVANCEMENT.md](./Architecture/ETAT_AVANCEMENT.md) et les docs d’architecture pour refléter le modèle BDD (dofusdb_characteristic_id, règles, dofusdb_effect_mapping).
- Documenter le fallback (mapping BDD prioritaire, JSON en secours) dans le guide dev ou la doc scrapping.
- Mettre à jour [BESOINS_CARACTERISTIQUES_ET_MAPPING.md](./BESOINS_CARACTERISTIQUES_ET_MAPPING.md) si des décisions sont prises en cours de route (ex. nom final des tables).

### 7.3 Nettoyage

- Supprimer ou déprécier : constante DofusDbEffectMapping (PHP), fichier dofusdb_characteristic_to_krosmoz.json (une fois la résolution par BDD validée).
- Vérifier qu’aucun code mort ne reste (anciens chemins de mapping, formatters obsolètes).

---

## 8. Ordre d’exécution recommandé

1. **Phase 1** (BDD) : 3.1 → 3.2 → 3.3. Chaque sous-étape peut être livrée et testée (migrations, seeders, services de lecture).
2. **Phase 2** (Pipeline) : une fois Phase 1 livrée, enchaîner 4.1 → 4.2 → 4.3 → 4.4 ; garder 4.5 (normalisation) tel quel.
3. **Phase 3** (UI) : peut démarrer en parallèle ou juste après le début de Phase 2 (les écrans peuvent d’abord lire l’existant, puis être branchés sur les nouvelles tables). Priorité comme demandé.
4. **Phase 4** (Recettes) : après analyse 6.1 ; si extension nécessaire, faire 6.2–6.3.
5. **Phase 5** (Consolidation) : en continu (tests à chaque phase) et en fin de refonte (doc, nettoyage).

---

## 9. Risques et parades

| Risque | Parade |
|--------|--------|
| Régression sur le mapping actuel | Garder le fallback JSON ; tests de non-régression sur la chaîne complète ; migration des données (seeders) avant de couper l’ancien chemin. |
| Surcharge de l’UI | Découper Phase 3 en sous-étapes (d’abord admin mapping, puis panneaux caractéristiques, puis page scrapping). |
| Recettes : schéma trop générique ou trop rigide | Valider l’option (pivot générique vs tables dédiées) avec un exemple concret DofusDB (items/consommables avec recettes) avant de coder. |

---

## 10. Suite

- Ce plan sera ajusté au fil des phases (ajout de sous-tâches, reports, décisions sur les noms de tables).
- Les livrables de chaque phase peuvent être détaillés en tickets ou issues selon l’outil de suivi du projet.
