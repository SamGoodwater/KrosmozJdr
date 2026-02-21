# Audit optimisation : Scrapping, Mapping, Caractéristiques

Ce document recense les **doublons supprimés**, les **extractions déjà faites** et les **recommandations** pour garder un code simple, sans duplication, avec des responsabilités claires.

**Contexte** : chasse au code double et au code trop complexe ; objectif de fonctions identifiées, une seule responsabilité, et découpage en sous-services si nécessaire.

---

## 1. Ce qui a été fait

### 1.1 Centralisation « mapping → array »

**Problème** : La conversion d’une règle de mapping (et de ses cibles) en tableau était refaite à plusieurs endroits avec des formes proches (ConversionService, panneau caractéristique, réponse API mappings).

**Modifications** :

- **ScrappingEntityMappingTarget**
  - `toConversionPair(): array` → `['model' => target_model, 'field' => target_field]`. Une seule représentation pour le pipeline et les vues liste / panneau 3.
  - `toResponseArray(): array` → forme pour le formulaire d’édition (id, target_model, target_field, sort_order).

- **ScrappingEntityMapping**
  - `getTargetsForConversion(): array` → délègue aux targets `toConversionPair()`.
  - `toSummaryArray(): array` → résumé pour affichage (id, source, entity, mapping_key, from_path, targets en conversion).

- **ScrappingMappingService**  
  Utilise `$row->getTargetsForConversion()` au lieu de reconstruire les tableaux à la main.

- **CharacteristicController**  
  Utilise `$m->toSummaryArray()` pour `scrappingMappingsUsingThis` au lieu de dupliquer la structure.

- **ScrappingMappingController**  
  Utilise `$t->toResponseArray()` dans `formatMappingForResponse()`.

**Résultat** : Une seule définition des formes « conversion » et « summary » ; les contrôleurs et le service s’appuient sur les modèles.

### 1.2 FormatterApplicator : résolution de la clé caractéristique

**Problème** : La logique d’extraction de `characteristic_key` depuis `context.mappingRule` était en ligne dans `apply()`, ce qui alourdissait la méthode et mélangeait dispatch et résolution.

**Modification** : Méthode dédiée `resolveCharacteristicKeyFromContext(array $context): ?string`. Une seule responsabilité (résoudre la clé pour les formatters dofusdb_*), `apply()` reste un simple dispatch.

---

## 2. État actuel et pistes d’amélioration

### 2.1 Fichiers très longs

| Fichier | Lignes (ordre de grandeur) | Piste |
|--------|-----------------------------|--------|
| **CharacteristicController** | ~1080 | Découper en sous-contrôleurs ou **Actions** : `CharacteristicIndexAction`, `CharacteristicShowAction`, `CharacteristicFormulaPreviewAction`, etc. Ou extraire les parties « formulaire » / « payload » dans des **Form Request** ou des **DTO builders** (ex. `CharacteristicShowPayloadBuilder`). |
| **Index.vue (characteristics)** | ~1730 | Découper en composants : **CharacteristicListSidebar**, **CharacteristicCreateForm**, **CharacteristicEditForm**, **LimitValuePanel**, **ConversionPanel**, **MappingPanel**. Le fichier principal ne garde que layout + orchestration. |
| **FormatterApplicator** | ~450 | À terme : **registry de formatters** (nom → callable ou petite classe). Chaque formatter = une classe ou une fonction dédiée (ex. `DofusdbLevelFormatter`, `PickLangFormatter`). L’applicator ne fait que `registry->get($name)->format(...)`. Réduit la taille du fichier et rend les formatters testables isolément. |

### 2.2 Base de données

- **Pas de changement nécessaire** pour l’instant : `scrapping_entity_mappings` + `scrapping_entity_mapping_targets` avec `characteristic_id` nullable est cohérent. Une règle = une ligne par (source, entity, mapping_key) ; les cibles en table dédiée évitent du JSON et permettent des contraintes.
- **Éventuel** : si la liste des formatters disponibles (noms + args) doit être éditée côté admin, on pourrait introduire une table `scrapping_formatters` (nom, args_schema). Pour l’instant les formatters sont en dur dans le code, ce qui reste simple.

### 2.3 Interface

- **Panneau 3 (Mapping)** : aujourd’hui en lecture seule + lien vers l’écran Mappings. Pour un workflow « tout depuis la caractéristique », on peut ajouter un mini CRUD dans le panneau (créer/éditer les règles dont `characteristic_id` = cette caractéristique), en réutilisant les mêmes payloads que l’écran Mappings (via `toSummaryArray` / `toResponseArray`).
- **Éviter la duplication de formulaires** : si un formulaire d’édition de règle existe dans l’écran Mappings, le réutiliser (composant ou page partagée) depuis le panneau 3 plutôt que de recopier les champs.

### 2.4 Sous-services éventuels

- **CharacteristicController** : extraire la construction du payload « show » (selected, entities, conversionFormulas, scrappingMappingsUsingThis, etc.) dans un **CharacteristicShowPayloadBuilder** ou un **CharacteristicAdminService**. Le contrôleur ne fait qu’appeler le builder et passer le résultat à Inertia. Même idée pour « create » (payload prérempli).
- **Scrapping** : le **ScrappingMappingService** a déjà une responsabilité claire (fournir le mapping pour une entité + helpers). Pas besoin de le découper davantage pour l’instant. Si la liste des mappings par caractéristique est utilisée ailleurs, on peut ajouter `listMappingsForCharacteristic(int $characteristicId)` dans ce service et l’utiliser depuis CharacteristicController (au lieu de faire le query + map dans le contrôleur).

---

## 3. Principes à garder

1. **Une forme de donnée = un endroit** : ex. « cibles en format conversion » = `ScrappingEntityMappingTarget::toConversionPair()` + `ScrappingEntityMapping::getTargetsForConversion()`.
2. **Une responsabilité par méthode** : ex. `resolveCharacteristicKeyFromContext()` ne fait que résoudre la clé.
3. **Modèles riches en comportement de présentation** : `toSummaryArray()`, `toResponseArray()`, `getTargetsForConversion()` évitent de disperser la connaissance des formats dans les contrôleurs.
4. **Découper les gros fichiers** : contrôleurs et vues par « écran » ou « panneau », formatters par type, pour faciliter lecture et tests.

---

## 4. Suite

- Implémenter les découpages (controller, Vue, formatters) par étapes, avec tests existants verts.
- Avant d’ajouter une nouvelle représentation des mappings (ex. pour l’API publique), vérifier si elle peut s’appuyer sur `toSummaryArray()` ou une variante explicite (ex. `toPublicArray()`).
