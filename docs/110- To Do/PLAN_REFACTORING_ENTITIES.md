# Plan de refactoring — Système d'entités

**Date de création** : 2026-01-06  
**Statut** : 📋 Plan d'action  
**Contexte** : Projet non déployé → Refonte propre possible (pas de compatibilité à maintenir)

---

## 🎯 Objectif

Refondre complètement le système d'entités frontend pour :
- ✅ Éliminer les duplications de code (DRY)
- ✅ Centraliser la logique de formatage dans les modèles
- ✅ Simplifier les descriptors (tableau + formulaires uniquement)
- ✅ Créer des vues manuelles pour chaque entité
- ✅ Implémenter un système de formatters centralisés
- ✅ Optimiser les performances avec un système de cache
- ✅ **Respecter l'Atomic Design** : Architecture claire et cohérente

**Principe** : Refonte propre, pas de transition progressive. Supprimer l'ancien système et implémenter le nouveau.

**📐 Architecture** : Voir `ARCHITECTURE_ENTITIES_ATOMIC_DESIGN.md` pour la structure complète des fichiers selon l'Atomic Design.

---

## 📋 Plan d'action — Ordre d'implémentation

### Phase 1 : Infrastructure de base (Fondations)

**Objectif** : Créer les briques de base réutilisables

#### 1.1 Créer le système de Formatters centralisés

**Fichiers à créer :**
```
resources/js/Utils/Formatters/
├── BaseFormatter.js          # Classe abstraite
├── FormatterRegistry.js       # Registre centralisé
├── RarityFormatter.js         # Priorité 1
├── LevelFormatter.js          # Priorité 1
├── VisibilityFormatter.js    # Priorité 1
├── UsableFormatter.js        # Priorité 1
├── PriceFormatter.js          # Priorité 1
├── DofusVersionFormatter.js   # Priorité 1
├── AutoUpdateFormatter.js     # Priorité 1
├── DofusdbIdFormatter.js      # Priorité 1
├── WeightFormatter.js         # Priorité 2
├── ImageFormatter.js          # Priorité 2
├── OfficialIdFormatter.js     # Priorité 2
├── DateFormatter.js           # Priorité 2
├── BooleanFormatter.js        # Priorité 2 (générique)
├── HostilityFormatter.js      # Priorité 3
├── ElementFormatter.js         # Priorité 3
└── CategoryFormatter.js       # Priorité 3
```

**Actions :**
1. Créer `BaseFormatter.js` avec la logique commune
2. Créer `FormatterRegistry.js` pour l'enregistrement automatique
3. Créer les formatters prioritaires (Priorité 1) en héritant de `BaseFormatter`
4. Enregistrer les formatters dans `FormatterRegistry`
5. Créer des tests unitaires pour chaque formatter

**Dépendances :** Aucune

**Durée estimée :** 2-3 jours

---

#### 1.2 Enrichir BaseModel avec les méthodes génériques

**Fichier à modifier :**
```
resources/js/Models/BaseModel.js
```

**Actions :**
1. Ajouter `_cellCache` (Map) pour le cache des cellules
2. Implémenter `has(fieldKey)` - méthode générique
3. Implémenter `format(fieldKey)` - méthode générique (utilise FormatterRegistry)
4. Implémenter `toCell(fieldKey, options)` - méthode générique principale
5. Implémenter `_resolveFormat(fieldKey, descriptor, context, size)` - logique commune
6. Implémenter `_normalizeSize(size)` - normalisation xs-xl
7. Implémenter `_toDefaultCell(fieldKey, format, size, options)` - fallback
8. Implémenter `_getCacheKey(fieldKey, options)` - clé de cache
9. Implémenter `invalidateCache()` - invalidation du cache
10. Ajouter les méthodes de convenance pour les propriétés très communes :
    - `hasRarity()`, `formatRarity()`, `toRarityCell()`
    - `hasLevel()`, `formatLevel()`, `toLevelCell()`
    - `hasVisibility()`, `formatVisibility()`, `toVisibilityCell()`

**Dépendances :** Phase 1.1 (Formatters)

**Durée estimée :** 1 jour

---

#### 1.3 Adapter EntityDescriptorConstants pour xs-xl

**Fichier à modifier :**
```
resources/js/Entities/entity/EntityDescriptorConstants.js
```

**Actions :**
1. Vérifier que `BREAKPOINTS` et `SCREEN_SIZES` utilisent déjà xs-xl (✅ déjà fait)
2. S'assurer que toutes les constantes sont cohérentes
3. Documenter les constantes

**Dépendances :** Aucune

**Durée estimée :** 0.5 jour

---

### Phase 2 : POC Resource (Première entité complète)

**Objectif** : Implémenter complètement le nouveau système pour Resource comme référence

#### 2.1 Enrichir le modèle Resource

**Fichier à modifier :**
```
resources/js/Models/Entity/Resource.js
```

**Actions :**
1. Surcharger `toCell(fieldKey, options)` pour gérer les champs spécifiques :
   - `name` → `_toNameCell()`
   - `resource_type` → `_toResourceTypeCell()`
   - `image` → `_toImageCell()`
   - `description` → `_toDescriptionCell()`
   - `created_by` → `_toCreatedByCell()`
   - `created_at` / `updated_at` → utilise `DateFormatter`
2. Implémenter les méthodes privées `_to*Cell()` pour les champs spécifiques
3. Utiliser les formatters centralisés pour les champs communs (rarity, level, etc.)
4. Tester avec un tableau de test

**Dépendances :** Phase 1.1, Phase 1.2

**Durée estimée :** 1 jour

---

#### 2.2 Créer les vues manuelles Resource

**Fichiers à créer :**
```
resources/js/Pages/Molecules/entity/resource/
├── ResourceViewLarge.vue
├── ResourceViewCompact.vue
├── ResourceViewMinimal.vue
└── ResourceViewText.vue
```

**⚠️ Architecture Atomic Design :** Les vues d'entités sont des **Molecules** spécifiques à chaque entité, pas des fichiers dans `Entities/`.

**Actions :**
1. Créer `ResourceViewLarge.vue` :
   - Vue complète avec toutes les informations
   - Utilise les méthodes du modèle (`resource.formatRarity()`, `resource.toCell()`, etc.)
   - Actions en haut à droite
   - Layout personnalisé

2. Créer `ResourceViewCompact.vue` :
   - Vue réduite avec informations essentielles
   - Utilise les méthodes du modèle
   - Actions en haut à côté du bouton fermer
   - Layout optimisé pour modal

3. Créer `ResourceViewMinimal.vue` :
   - Petite carte qui s'étend au survol
   - Utilise les méthodes du modèle
   - Actions lors de l'extension

4. Créer `ResourceViewText.vue` :
   - Nom + icône/image
   - Au survol, affiche ResourceViewMinimal
   - Pas d'actions

**Dépendances :** Phase 2.1

**Durée estimée :** 2 jours

---

#### 2.3 Simplifier ResourceDescriptor

**Fichier à modifier :**
```
resources/js/Entities/resource/resource-descriptors.js
```

**Actions :**
1. Supprimer `display.views` (remplacé par vues manuelles)
2. Adapter `display.sizes` pour utiliser xs, sm, md, lg, xl (au lieu de small/normal/large)
3. Garder uniquement :
   - Configuration tableau (`display.sizes` pour les cellules)
   - Configuration formulaires (`edit.form`)
   - Configuration bulk (`edit.form.bulk`)
4. Supprimer `RESOURCE_VIEW_FIELDS` (compact, extended) - garder uniquement `quickEdit`
5. Simplifier la structure globale

**Dépendances :** Phase 2.1, Phase 2.2

**Durée estimée :** 1 jour

---

#### 2.4 Créer ResourceTableConfig

**Fichier à créer :**
```
resources/js/Entities/resource/ResourceTableConfig.js
```

**Actions :**
1. Créer la configuration du tableau en utilisant `TableConfig` et `TableColumnConfig`
2. Configurer toutes les colonnes avec leurs propriétés :
   - Visibilité responsive (xs-xl)
   - Permissions
   - Tri, recherche, filtres
   - Format selon la taille
3. Configurer les features (search, filters, pagination, selection, etc.)
4. Configurer quickEdit et actions

**Dépendances :** Phase 2.3

**Durée estimée :** 0.5 jour

---

#### 2.5 Créer ResourceFormConfig et ResourceBulkConfig

**Fichiers à créer :**
```
resources/js/Entities/resource/ResourceFormConfig.js
resources/js/Entities/resource/ResourceBulkConfig.js
```

**Actions :**
1. Créer `ResourceFormConfig.js` :
   - Utilise `FormConfig` et `FormFieldConfig`
   - Configure tous les champs éditables
   - Configure les groupes de champs

2. Créer `ResourceBulkConfig.js` :
   - Utilise `BulkConfig`
   - Configure les champs bulk-editables
   - Configure la liste des champs quickEdit

**Dépendances :** Phase 2.3

**Durée estimée :** 0.5 jour

---

#### 2.6 Refactoriser resource-adapter.js

**Fichier à modifier :**
```
resources/js/Entities/resource/resource-adapter.js
```

**Actions :**
1. Simplifier `adaptResourceEntitiesTableResponse()` :
   - Créer les modèles Resource depuis les données brutes
   - Ne pas pré-générer les cellules
   - Passer les modèles dans `rowParams.entity`
2. Supprimer `buildResourceCell()` (remplacé par `resource.toCell()`)
3. Nettoyer les imports inutiles

**Dépendances :** Phase 2.1

**Durée estimée :** 0.5 jour

---

#### 2.7 Adapter le composant tableau pour Resource

**Fichiers à modifier :**
```
resources/js/Pages/Pages/entity/resource/Index.vue
resources/js/Pages/Organismes/table/EntityTanStackTable.vue (si nécessaire)
resources/js/Pages/Organismes/table/TanStackTable.vue (si nécessaire)
```

**Actions :**
1. Dans `Index.vue` :
   - Utiliser `ResourceTableConfig` au lieu de la config actuelle
   - Adapter pour utiliser `resource.toCell()` au lieu de `buildResourceCell()`
   - Calculer la taille du tableau (xs-xl)
   - Générer les cellules à la volée dans le composant tableau

2. Dans `EntityTanStackTable.vue` ou `TanStackTable.vue` :
   - Adapter pour générer les cellules via `entity.toCell()` si nécessaire
   - Calculer la taille du tableau selon la largeur disponible

**Dépendances :** Phase 2.1, Phase 2.4, Phase 2.6

**Durée estimée :** 1 jour

---

#### 2.8 Tester et valider le POC Resource

**Actions :**
1. Tester le tableau Resource :
   - Tri, filtres, recherche
   - Visibilité des colonnes
   - Pagination
   - Sélection multiple
   - Génération des cellules selon la taille (xs-xl)

2. Tester le quickedit :
   - Panneau latéral
   - Modal
   - Agrégation des valeurs
   - Construction du payload

3. Tester les vues :
   - ResourceViewLarge
   - ResourceViewCompact
   - ResourceViewMinimal
   - ResourceViewText

4. Tester les actions :
   - Toutes les actions disponibles
   - Permissions

5. Comparer les performances avec l'ancien système

**Dépendances :** Phase 2.1 à 2.7

**Durée estimée :** 1 jour

---

### Phase 3 : Migration des autres entités prioritaires

**Objectif** : Migrer Item et Consumable (similaires à Resource)

#### 3.1 Migrer Item

**Fichiers à créer/modifier :**
- `Models/Entity/Item.js` → Enrichir avec `toCell()`
- `Pages/Molecules/entity/item/` → Créer les 4 vues manuelles (ItemViewLarge.vue, etc.)
- `Entities/item/item-descriptors.js` → Simplifier
- `Entities/item/ItemTableConfig.js` → Créer
- `Entities/item/ItemFormConfig.js` → Créer
- `Entities/item/ItemBulkConfig.js` → Créer
- `Entities/item/item-adapter.js` → Refactoriser
- `Pages/Pages/entity/item/Index.vue` → Adapter

**Actions :** Même pattern que Resource (Phase 2)

**Dépendances :** Phase 2 (POC Resource validé)

**Durée estimée :** 2-3 jours

---

#### 3.2 Migrer Consumable

**Fichiers à créer/modifier :**
- `Models/Entity/Consumable.js` → Enrichir avec `toCell()`
- `Pages/Molecules/entity/consumable/` → Créer les 4 vues manuelles (ConsumableViewLarge.vue, etc.)
- `Entities/consumable/consumable-descriptors.js` → Simplifier
- `Entities/consumable/ConsumableTableConfig.js` → Créer
- `Entities/consumable/ConsumableFormConfig.js` → Créer
- `Entities/consumable/ConsumableBulkConfig.js` → Créer
- `Entities/consumable/consumable-adapter.js` → Refactoriser
- `Pages/Pages/entity/consumable/Index.vue` → Adapter

**Actions :** Même pattern que Resource (Phase 2)

**Dépendances :** Phase 2 (POC Resource validé)

**Durée estimée :** 2-3 jours

---

### Phase 4 : Migration des autres entités

**Objectif** : Migrer toutes les autres entités une par une

#### 4.1 Entités avec formatters spécialisés

**Entités :**
- `spell` (ElementFormatter, CategoryFormatter)
- `creature` / `monster` / `npc` (HostilityFormatter)

**Actions :** Même pattern que Resource, en créant les formatters spécialisés si nécessaire

**Durée estimée :** 1-2 jours par entité

---

#### 4.2 Autres entités

**Entités :**
- `attribute`
- `campaign`
- `capability`
- `classe`
- `panoply`
- `scenario`
- `shop`
- `specialization`
- `resource-type`
- `item-type`
- `consumable-type`
- `spell-type`

**Actions :** Même pattern que Resource

**Durée estimée :** 0.5-1 jour par entité

---

### Phase 5 : Nettoyage et finalisation

**Objectif** : Supprimer l'ancien système et finaliser

#### 5.1 Supprimer les anciens adapters

**Fichiers à supprimer :**
- Tous les `*-adapter.js` (remplacés par adapters simplifiés)
- `Utils/entity/adapter-helpers.js` (logique déplacée dans formatters)

**Actions :**
1. Vérifier qu'aucun fichier n'utilise les anciens adapters
2. Supprimer les fichiers obsolètes
3. Nettoyer les imports

**Dépendances :** Phase 4 (toutes les entités migrées)

**Durée estimée :** 0.5 jour

---

#### 5.2 Adapter EntityDescriptorHelpers

**Fichier à modifier :**
```
resources/js/Entities/entity/EntityDescriptorHelpers.js
```

**Actions :**
1. Remplacer les fonctions locales par des wrappers vers les formatters :
   - `formatRarity()` → wrapper vers `RarityFormatter.format()`
   - `formatVisibility()` → wrapper vers `VisibilityFormatter.format()`
   - `formatHostility()` → wrapper vers `HostilityFormatter.format()`
   - `formatDate()` → wrapper vers `DateFormatter.format()`
2. Marquer les fonctions comme dépréciées avec warnings
3. Documenter la migration vers les formatters

**Dépendances :** Phase 1.1 (Formatters créés)

**Durée estimée :** 0.5 jour

---

#### 5.3 Adapter EntityDescriptor (classe de base)

**Fichier à modifier :**
```
resources/js/Entities/entity/EntityDescriptor.js
```

**Actions :**
1. Adapter les méthodes pour utiliser les formatters au lieu des fonctions locales
2. Utiliser `FormatterRegistry` pour accéder aux formatters
3. Supprimer les méthodes obsolètes

**Dépendances :** Phase 1.1, Phase 5.2

**Durée estimée :** 0.5 jour

---

#### 5.4 Mettre à jour entity-registry.js

**Fichier à modifier :**
```
resources/js/Entities/entity-registry.js
```

**Actions :**
1. Ajouter `Model` dans la config de chaque entité
2. Supprimer `buildCell` (remplacé par `model.toCell()`)
3. Supprimer `viewFields` (remplacé par vues manuelles)
4. Garder `getDescriptors`, `responseAdapter`, `defaults`

**Dépendances :** Phase 4 (toutes les entités migrées)

**Durée estimée :** 0.5 jour

---

#### 5.5 Supprimer les composants génériques obsolètes

**Fichiers à supprimer :**
- `Pages/Molecules/entity/EntityViewLarge.vue` (remplacé par vues manuelles)
- `Pages/Molecules/entity/EntityViewCompact.vue` (remplacé par vues manuelles)
- `Pages/Molecules/entity/EntityViewMinimal.vue` (remplacé par vues manuelles)
- `Pages/Molecules/entity/EntityViewText.vue` (remplacé par vues manuelles)

**Actions :**
1. Vérifier qu'aucun fichier n'utilise ces composants
2. Supprimer les fichiers
3. Nettoyer les imports

**Dépendances :** Phase 4 (toutes les entités migrées)

**Durée estimée :** 0.5 jour

---

#### 5.6 Adapter EntityModal pour utiliser les vues manuelles

**Fichier à modifier :**
```
resources/js/Pages/Organismes/entity/EntityModal.vue
```

**Actions :**
1. Adapter pour utiliser les vues manuelles spécifiques à chaque entité
2. Utiliser `entity-registry` pour récupérer le composant de vue approprié
3. Supprimer la logique de génération automatique

**Dépendances :** Phase 4 (toutes les entités migrées)

**Durée estimée :** 0.5 jour

---

#### 5.7 Documentation et tests

**Actions :**
1. Mettre à jour la documentation :
   - Guide d'utilisation des formatters
   - Guide de création d'une nouvelle entité
   - Guide de création d'une vue manuelle
2. Créer des tests unitaires :
   - Tests pour chaque formatter
   - Tests pour BaseModel
   - Tests pour les modèles d'entités
3. Créer des tests d'intégration :
   - Tests du tableau complet
   - Tests du quickedit
   - Tests des vues

**Dépendances :** Phase 5.1 à 5.6

**Durée estimée :** 2 jours

---

## 📊 Récapitulatif des phases

| Phase | Description | Durée estimée | Dépendances |
|-------|-------------|---------------|-------------|
| **Phase 1** | Infrastructure de base | 3.5 jours | Aucune |
| **Phase 2** | POC Resource (référence) | 7 jours | Phase 1 |
| **Phase 3** | Migration Item + Consumable | 4-6 jours | Phase 2 |
| **Phase 4** | Migration autres entités | 10-15 jours | Phase 2 |
| **Phase 5** | Nettoyage et finalisation | 5 jours | Phase 4 |
| **TOTAL** | | **29.5-36.5 jours** | |

---

## 📁 Structure finale des fichiers

### Formatters
```
resources/js/Utils/Formatters/
├── BaseFormatter.js
├── FormatterRegistry.js
├── RarityFormatter.js
├── LevelFormatter.js
├── VisibilityFormatter.js
├── UsableFormatter.js
├── PriceFormatter.js
├── DofusVersionFormatter.js
├── AutoUpdateFormatter.js
├── DofusdbIdFormatter.js
├── WeightFormatter.js
├── ImageFormatter.js
├── OfficialIdFormatter.js
├── DateFormatter.js
├── BooleanFormatter.js
├── HostilityFormatter.js
├── ElementFormatter.js
└── CategoryFormatter.js
```

### Modèles
```
resources/js/Models/
├── BaseModel.js (enrichi)
└── Entity/
    ├── Resource.js (enrichi)
    ├── Item.js (enrichi)
    ├── Consumable.js (enrichi)
    └── ... (autres entités enrichies)
```

### Descriptors et Configs
```
resources/js/Entities/
├── entity/
│   ├── EntityDescriptor.js (adapté)
│   ├── EntityDescriptorConstants.js (adapté pour xs-xl)
│   ├── EntityDescriptorHelpers.js (wrappers vers formatters)
│   ├── TableConfig.js (existe déjà)
│   ├── TableColumnConfig.js (existe déjà)
│   ├── FormConfig.js (existe déjà)
│   ├── FormFieldConfig.js (existe déjà)
│   └── BulkConfig.js (existe déjà)
└── resource/
    ├── resource-descriptors.js (simplifié)
    ├── ResourceTableConfig.js (nouveau)
    ├── ResourceFormConfig.js (nouveau)
    ├── ResourceBulkConfig.js (nouveau)
    └── resource-adapter.js (simplifié)
```

### Vues Molecules (Atomic Design)
```
resources/js/Pages/Molecules/entity/
└── resource/
    ├── ResourceViewLarge.vue (nouveau)
    ├── ResourceViewCompact.vue (nouveau)
    ├── ResourceViewMinimal.vue (nouveau)
    └── ResourceViewText.vue (nouveau)
```

**⚠️ Architecture Atomic Design :** Les vues d'entités sont des **Molecules** spécifiques à chaque entité, placées dans `Pages/Molecules/entity/{entity}/`, pas dans `Entities/`.

### Fichiers à supprimer

**Adapters obsolètes :**
- `Entities/*/build*Cell()` (fonctions supprimées)
- `Utils/entity/adapter-helpers.js` (logique déplacée dans formatters)

**Composants génériques obsolètes :**
- `Pages/Molecules/entity/EntityViewLarge.vue`
- `Pages/Molecules/entity/EntityViewCompact.vue`
- `Pages/Molecules/entity/EntityViewMinimal.vue`
- `Pages/Molecules/entity/EntityViewText.vue`

**Configurations obsolètes :**
- `RESOURCE_VIEW_FIELDS.compact` et `RESOURCE_VIEW_FIELDS.extended` (supprimés)
- `display.views` dans les descriptors (supprimé)

---

## ✅ Checklist de validation

### Phase 1 : Infrastructure
- [ ] BaseFormatter créé et testé
- [ ] FormatterRegistry créé et testé
- [ ] Tous les formatters prioritaires créés et testés
- [ ] BaseModel enrichi avec méthodes génériques
- [ ] EntityDescriptorConstants adapté pour xs-xl

### Phase 2 : POC Resource
- [ ] Resource.toCell() implémenté et testé
- [ ] 4 vues manuelles Resource créées et testées
- [ ] ResourceDescriptor simplifié
- [ ] ResourceTableConfig créé
- [ ] ResourceFormConfig créé
- [ ] ResourceBulkConfig créé
- [ ] resource-adapter.js refactorisé
- [ ] Tableau Resource fonctionne avec le nouveau système
- [ ] Quickedit Resource fonctionne
- [ ] Toutes les vues Resource fonctionnent
- [ ] Performances validées

### Phase 3 : Migration Item + Consumable
- [ ] Item migré complètement
- [ ] Consumable migré complètement
- [ ] Tests validés

### Phase 4 : Migration autres entités
- [ ] Toutes les entités migrées
- [ ] Formatters spécialisés créés si nécessaire
- [ ] Tests validés

### Phase 5 : Nettoyage
- [ ] Anciens adapters supprimés
- [ ] EntityDescriptorHelpers adapté
- [ ] EntityDescriptor adapté
- [ ] entity-registry.js mis à jour
- [ ] Composants génériques obsolètes supprimés
- [ ] EntityModal adapté
- [ ] Documentation mise à jour
- [ ] Tests créés

---

## 🚀 Ordre d'exécution recommandé

1. **Phase 1** : Infrastructure de base (fondations solides)
2. **Phase 2** : POC Resource (validation du système)
3. **Phase 3** : Migration Item + Consumable (réutilisation du pattern)
4. **Phase 4** : Migration autres entités (réplication)
5. **Phase 5** : Nettoyage (finalisation)

**Principe** : Une phase à la fois, valider avant de passer à la suivante.

---

## 📝 Notes importantes

- **Pas de compatibilité** : Supprimer directement l'ancien système, pas de wrappers
- **Tests au fur et à mesure** : Créer les tests en même temps que l'implémentation
- **Documentation** : Documenter chaque nouvelle brique créée
- **Validation continue** : Tester après chaque étape importante

---

## 🔗 Références

- **Architecture Atomic Design** : `docs/110- To Do/ARCHITECTURE_ENTITIES_ATOMIC_DESIGN.md`
- **Spécifications complètes** : `docs/110- To Do/New Système d'Entity.md`
- **Architecture optimisée** : Voir section "Analyse d'optimisation, DRY et structure" dans le document principal
