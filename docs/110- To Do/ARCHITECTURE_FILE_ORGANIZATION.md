# Architecture — Organisation des fichiers d'entités

**Date de création** : 2026-01-XX  
**Contexte** : Analyse et recommandation sur l'organisation des fichiers liés aux entités

---

## 🎯 Question centrale

**Faut-il regrouper tous les fichiers d'une entité au même endroit, ou les répartir par type (Models, Mappers, Configs, Vues) ?**

---

## 📊 Analyse de la structure actuelle

### Structure actuelle (séparation par type)

```
resources/js/
├── Models/Entity/                    # ✅ Tous les modèles ensemble
│   ├── Resource.js
│   ├── Item.js
│   └── ...
│
├── Mappers/Entity/                   # ✅ Tous les mappers ensemble
│   └── ResourceMapper.js            # (seulement Resource pour l'instant)
│
├── Utils/Formatters/                 # ✅ Tous les formatters ensemble
│   ├── RarityFormatter.js           # (partagé entre entités)
│   ├── LevelFormatter.js            # (partagé entre entités)
│   └── ...
│
├── Entities/{entity}/                # ✅ Configs par entité
│   ├── resource/
│   │   ├── resource-descriptors.js
│   │   ├── ResourceTableConfig.js
│   │   ├── ResourceFormConfig.js
│   │   ├── ResourceBulkConfig.js
│   │   └── resource-adapter.js
│   └── ...
│
├── Pages/Molecules/entity/{entity}/   # ✅ Vues par entité
│   ├── resource/
│   │   ├── ResourceViewLarge.vue
│   │   ├── ResourceViewCompact.vue
│   │   └── ...
│   └── ...
│
└── Composables/entity/                # ✅ Composables génériques
    ├── useBulkEditPanel.js
    └── ...
```

---

## ⚖️ Comparaison des deux approches

### Approche 1 : Séparation par type (ACTUELLE) ✅

**Principe :** Regrouper les fichiers par leur **rôle/type** (Models, Mappers, Configs, Vues).

**Avantages :**
- ✅ **Cohérence avec l'Atomic Design** : Respecte la hiérarchie Atoms → Molecules → Organisms
- ✅ **Séparation des responsabilités** : Chaque type de fichier a un rôle clair
- ✅ **Réutilisabilité** : Facile de trouver tous les modèles ensemble, tous les formatters ensemble
- ✅ **Partage de code** : Les formatters sont partagés entre entités (RarityFormatter utilisé par Resource, Item, etc.)
- ✅ **Cohérence avec la documentation** : Correspond à `ARCHITECTURE_ENTITY_SYSTEM.md` et `ARCHITECTURE_ENTITIES_ATOMIC_DESIGN.md`
- ✅ **Facilite les imports** : `import { Resource } from '@/Models/Entity/Resource'` est clair
- ✅ **Évite la duplication** : Un seul RarityFormatter pour toutes les entités

**Inconvénients :**
- ⚠️ Les fichiers d'une même entité sont dispersés dans plusieurs dossiers
- ⚠️ Nécessite de naviguer entre plusieurs dossiers pour voir l'ensemble d'une entité

**Exemple de navigation :**
```
Pour voir l'ensemble de Resource :
- Models/Entity/Resource.js
- Mappers/Entity/ResourceMapper.js
- Entities/resource/resource-descriptors.js
- Entities/resource/ResourceTableConfig.js
- Pages/Molecules/entity/resource/ResourceViewLarge.vue
```

---

### Approche 2 : Regroupement par entité ❌

**Principe :** Regrouper tous les fichiers d'une entité dans un seul dossier.

**Structure hypothétique :**
```
resources/js/Entities/
├── resource/
│   ├── Resource.js                  # Model
│   ├── ResourceMapper.js            # Mapper
│   ├── resource-descriptors.js     # Descriptors
│   ├── ResourceTableConfig.js      # Table config
│   ├── ResourceFormConfig.js       # Form config
│   ├── ResourceBulkConfig.js        # Bulk config
│   ├── resource-adapter.js         # Adapter
│   └── views/                       # Vues
│       ├── ResourceViewLarge.vue
│       └── ...
```

**Avantages :**
- ✅ Tous les fichiers d'une entité au même endroit
- ✅ Facile de voir l'ensemble d'une entité d'un coup d'œil

**Inconvénients :**
- ❌ **Violation de l'Atomic Design** : Mélange Models (logique métier) et Vues (UI)
- ❌ **Duplication inévitable** : Où mettre RarityFormatter ? Dans chaque entité ? Ou dans un dossier séparé ?
- ❌ **Imports moins clairs** : `import { Resource } from '@/Entities/resource/Resource'` vs `@/Models/Entity/Resource`
- ❌ **Réutilisabilité réduite** : Plus difficile de trouver tous les modèles ensemble
- ❌ **Incohérence avec la documentation** : Nécessiterait de réécrire toute la documentation
- ❌ **Mélange des responsabilités** : Logique métier (Model) et UI (Vue) dans le même dossier

---

## 🎯 Recommandation : **Conserver l'approche actuelle (séparation par type)**

### Justification

1. **Respect de l'architecture en couches**
   - L'architecture documentée dans `ARCHITECTURE_ENTITY_SYSTEM.md` repose sur une séparation claire des responsabilités
   - Chaque couche (Models, Mappers, Formatters, Descriptors, Renderers, Vues) a un rôle précis
   - Regrouper par entité violerait cette séparation

2. **Réutilisabilité et partage**
   - Les formatters (RarityFormatter, LevelFormatter, etc.) sont partagés entre plusieurs entités
   - Les composables génériques (useBulkEditPanel, etc.) fonctionnent pour toutes les entités
   - Regrouper par entité créerait de la duplication ou des incohérences

3. **Cohérence avec les conventions du projet**
   - La documentation existante (`ARCHITECTURE_ENTITIES_ATOMIC_DESIGN.md`) définit clairement la structure
   - Le projet suit l'Atomic Design, qui sépare par type, pas par entité
   - Les imports suivent un pattern clair : `@/Models/Entity/`, `@/Mappers/Entity/`, etc.

4. **Facilité de maintenance**
   - Facile de trouver tous les modèles ensemble pour les modifier
   - Facile de trouver tous les mappers ensemble pour les standardiser
   - Facile de trouver tous les formatters ensemble pour les optimiser

5. **Scalabilité**
   - Avec 15+ entités, regrouper par entité créerait 15+ dossiers avec des structures similaires
   - La séparation par type permet une meilleure organisation à grande échelle

---

## ✅ Structure recommandée (ACTUELLE)

```
resources/js/
├── Models/Entity/                    # Logique métier
│   ├── Resource.js
│   ├── Item.js
│   └── ...
│
├── Mappers/Entity/                   # Transformations backend → frontend
│   ├── ResourceMapper.js
│   ├── ItemMapper.js
│   └── ...
│
├── Utils/Formatters/                 # Formatage centralisé (partagé)
│   ├── RarityFormatter.js
│   ├── LevelFormatter.js
│   └── ...
│
├── Entities/{entity}/                # Configuration par entité
│   ├── resource/
│   │   ├── resource-descriptors.js
│   │   ├── ResourceTableConfig.js
│   │   ├── ResourceFormConfig.js
│   │   ├── ResourceBulkConfig.js
│   │   └── resource-adapter.js
│   └── ...
│
├── Pages/Molecules/entity/{entity}/  # Vues spécifiques par entité
│   ├── resource/
│   │   ├── ResourceViewLarge.vue
│   │   ├── ResourceViewCompact.vue
│   │   └── ...
│   └── ...
│
└── Composables/entity/               # Composables génériques
    ├── useBulkEditPanel.js
    └── ...
```

---

## 🔍 Vérification de la cohérence actuelle

### ✅ Ce qui est bien placé

1. **Models** : `Models/Entity/` ✅
   - Tous les modèles ensemble
   - Logique métier isolée

2. **Formatters** : `Utils/Formatters/` ✅
   - Formatage centralisé
   - Partagé entre entités

3. **Configs** : `Entities/{entity}/` ✅
   - Configuration par entité
   - Descriptors, TableConfig, FormConfig, BulkConfig, adapter

4. **Vues** : `Pages/Molecules/entity/{entity}/` ✅
   - Vues spécifiques par entité
   - Respecte l'Atomic Design

5. **Composables** : `Composables/entity/` ✅
   - Composables génériques réutilisables

### ⚠️ Point d'attention : Mappers

**Situation actuelle :**
- `Mappers/Entity/ResourceMapper.js` ✅ (bien placé pour les entités)
- `Utils/Services/Mappers/SectionMapper.js` ✅ (pour Pages/Sections, différent système)
- `Utils/Services/Mappers/PageMapper.js` ✅ (pour Pages/Sections, différent système)

**Recommandation :**
- ✅ **Conserver** `Mappers/Entity/` pour tous les mappers d'entités (Resource, Item, etc.)
- ✅ Créer les autres mappers d'entités au même endroit : `Mappers/Entity/ItemMapper.js`, etc.
- ✅ **Conserver** `Utils/Services/Mappers/` pour les mappers de Pages/Sections (système différent)

**Justification :**
- Les mappers d'entités sont des transformations pures, sans logique métier
- Ils sont similaires entre entités (même pattern : `fromApi`, `fromForm`, `toApi`)
- Regrouper par type facilite la standardisation et la maintenance
- Pages/Sections utilisent un système différent (héritent de `BaseMapper`), donc emplacement séparé justifié

---

## 📝 Guide de navigation pour une entité

Pour voir l'ensemble d'une entité (ex: Resource), suivre ce chemin :

1. **Model** : `Models/Entity/Resource.js`
2. **Mapper** : `Mappers/Entity/ResourceMapper.js`
3. **Configs** : `Entities/resource/`
   - `resource-descriptors.js`
   - `ResourceTableConfig.js`
   - `ResourceFormConfig.js`
   - `ResourceBulkConfig.js`
   - `resource-adapter.js`
4. **Vues** : `Pages/Molecules/entity/resource/`
   - `ResourceViewLarge.vue`
   - `ResourceViewCompact.vue`
   - `ResourceViewMinimal.vue`
   - `ResourceViewText.vue`
5. **Formatters utilisés** : `Utils/Formatters/`
   - `RarityFormatter.js`
   - `LevelFormatter.js`
   - etc.

---

## 🎯 Règles d'or

1. **Séparation par type, pas par entité**
   - Models ensemble, Mappers ensemble, Formatters ensemble
   - Configs et Vues peuvent être par entité (car spécifiques)

2. **Partage maximal**
   - Formatters partagés entre entités
   - Composables génériques pour toutes les entités
   - Models et Mappers suivent le même pattern

3. **Cohérence avec la documentation**
   - Respecter `ARCHITECTURE_ENTITY_SYSTEM.md`
   - Respecter `ARCHITECTURE_ENTITIES_ATOMIC_DESIGN.md`

4. **Imports clairs**
   - `@/Models/Entity/Resource`
   - `@/Mappers/Entity/ResourceMapper`
   - `@/Entities/resource/resource-descriptors`

---

## ✅ Conclusion

**L'architecture actuelle est correcte et optimale.**

La séparation par type (Models, Mappers, Formatters, Configs, Vues) respecte :
- ✅ L'Atomic Design
- ✅ La séparation des responsabilités
- ✅ La réutilisabilité
- ✅ La documentation existante
- ✅ Les bonnes pratiques de développement

**Aucun changement nécessaire.** ✅

---

## 📚 Références

- [ARCHITECTURE_ENTITY_SYSTEM.md](./ARCHITECTURE_ENTITY_SYSTEM.md) — Vue d'ensemble de l'architecture
- [ARCHITECTURE_ENTITIES_ATOMIC_DESIGN.md](./ARCHITECTURE_ENTITIES_ATOMIC_DESIGN.md) — Structure des fichiers selon Atomic Design
- [MAPPERS_PATTERN.md](./MAPPERS_PATTERN.md) — Pattern des mappers
- [PROJECT_STRUCTURE.md](../10-BestPractices/PROJECT_STRUCTURE.md) — Structure générale du projet
