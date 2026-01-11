# Architecture des entités — Atomic Design

**Date de création** : 2026-01-06  
**Dernière mise à jour** : 2026-01-XX  
**Contexte** : Structure des fichiers d'entités selon l'Atomic Design et l'architecture en 4 couches

> **Voir [ARCHITECTURE_ENTITY_SYSTEM.md](./ARCHITECTURE_ENTITY_SYSTEM.md) pour la vue d'ensemble de l'architecture.**

---

## 🎯 Principe

Respecter strictement l'Atomic Design pour organiser tous les fichiers liés aux entités :
- **Séparation claire** : Logique métier ≠ UI ≠ Configuration
- **Hiérarchie respectée** : Atoms → Molecules → Organisms → Pages
- **Cohérence** : Même structure pour toutes les entités
- **5 couches** : Mappers → Models → Formatters → Descriptors → Renderers → Vues

---

## 📁 Structure complète

### 1. **Models** — Logique métier (non-UI)

**Emplacement :** `resources/js/Models/Entity/`

**Rôle :** Classes JavaScript représentant les entités avec leur logique métier.

**Contenu :**
```
Models/
├── BaseModel.js                    # Classe de base avec méthodes génériques
└── Entity/
    ├── Resource.js                 # Modèle Resource (formatage, toCell(), etc.)
    ├── Item.js                     # Modèle Item
    ├── Consumable.js               # Modèle Consumable
    ├── Spell.js                    # Modèle Spell
    └── ...                         # Autres entités
```

**Responsabilités :**
- Formatage des données (`toCell()`, `formatRarity()`, etc.)
- Validation des données
- Transformation des données (raw → model)
- Cache des cellules générées
- Logique métier spécifique à l'entité

**❌ Ne contient PAS :**
- Composants Vue
- Configuration UI
- Descriptors

---

### 2. **Utils/Formatters** — Formatage centralisé (non-UI)

**Emplacement :** `resources/js/Utils/Formatters/`

**Rôle :** Classes statiques pour formater les propriétés communes aux entités.

**Contenu :**
```
Utils/Formatters/
├── BaseFormatter.js               # Classe abstraite
├── FormatterRegistry.js            # Registre centralisé
├── RarityFormatter.js              # Formatage rarity
├── LevelFormatter.js               # Formatage level
├── VisibilityFormatter.js         # Formatage visibility
├── DateFormatter.js                # Formatage dates
└── ...                             # Autres formatters
```

**Responsabilités :**
- Formatage des valeurs communes (rarity → badge, level → texte, etc.)
- Conversion valeur → label → couleur
- Formatage selon la taille (xs, sm, md, lg, xl)

**❌ Ne contient PAS :**
- Logique spécifique à une entité
- Composants Vue

---

### 3. **Entities** — Configuration et adapters (non-UI)

**Emplacement :** `resources/js/Entities/{entity}/`

**Rôle :** Configuration, descriptors, adapters pour chaque entité.

**Contenu :**
```
Entities/
├── entity/                         # Classes de base communes
│   ├── EntityDescriptor.js        # Classe de base descriptor
│   ├── EntityDescriptorConstants.js
│   ├── EntityDescriptorHelpers.js
│   ├── TableConfig.js
│   ├── TableColumnConfig.js
│   ├── FormConfig.js
│   ├── FormFieldConfig.js
│   └── BulkConfig.js
│
└── resource/                       # Configuration Resource
    ├── resource-descriptors.js     # Descriptor simplifié (table + form)
    ├── ResourceTableConfig.js      # Configuration tableau
    ├── ResourceFormConfig.js       # Configuration formulaire
    ├── ResourceBulkConfig.js       # Configuration bulk edit
    └── resource-adapter.js         # Adapter (raw → model)
```

**Responsabilités :**
- Configuration des tableaux (colonnes, tri, filtres, etc.)
- Configuration des formulaires (champs, validation, etc.)
- Configuration du bulk edit
- Adaptation des réponses backend → modèles

**❌ Ne contient PAS :**
- Composants Vue
- Vues d'affichage
- Logique de formatage (déléguée aux modèles)

---

### 4. **Atoms** — Composants de base réutilisables

**Emplacement :** `resources/js/Pages/Atoms/data-display/`

**Rôle :** Composants atomiques pour afficher des données.

**Contenu :**
```
Pages/Atoms/data-display/
├── CellRenderer.vue                # Rendu d'une cellule de tableau
├── Badge.vue                        # Badge générique
├── Avatar.vue                       # Avatar
├── Image.vue                        # Image
└── ...                              # Autres atoms de display
```

**Responsabilités :**
- Affichage d'un élément de données unique
- Pas de logique métier
- Réutilisable partout

**❌ Ne contient PAS :**
- Logique spécifique à une entité
- Logique métier complexe

---

### 5. **Molecules** — Vues d'entités spécifiques

**Emplacement :** `resources/js/Pages/Molecules/entity/{entity}/`

**Rôle :** Vues manuelles spécifiques à chaque entité (Large, Compact, Minimal, Text).

**Contenu :**
```
Pages/Molecules/entity/
├── resource/                       # Vues Resource
│   ├── ResourceViewLarge.vue       # Vue Large (page complète)
│   ├── ResourceViewCompact.vue     # Vue Compact (modal)
│   ├── ResourceViewMinimal.vue     # Vue Minimal (carte)
│   └── ResourceViewText.vue        # Vue Text (ligne)
│
├── item/                           # Vues Item
│   ├── ItemViewLarge.vue
│   ├── ItemViewCompact.vue
│   ├── ItemViewMinimal.vue
│   └── ItemViewText.vue
│
└── ...                             # Autres entités
```

**Responsabilités :**
- Affichage d'une entité dans un format spécifique
- Utilise les méthodes du modèle (`resource.toCell()`, `resource.formatRarity()`, etc.)
- Layout personnalisé pour chaque entité
- Actions spécifiques à l'entité

**❌ Ne contient PAS :**
- Logique métier (déléguée au modèle)
- Configuration (déléguée aux configs)
- Formatage (délégué aux formatters)

**⚠️ Supprimer :**
- `Pages/Molecules/entity/EntityViewLarge.vue` (générique → à supprimer)
- `Pages/Molecules/entity/EntityViewCompact.vue` (générique → à supprimer)
- `Pages/Molecules/entity/EntityViewMinimal.vue` (générique → à supprimer)
- `Pages/Molecules/entity/EntityViewText.vue` (générique → à supprimer)

---

### 6. **Organisms** — Composants complexes réutilisables

**Emplacement :** `resources/js/Pages/Organismes/entity/`

**Rôle :** Composants complexes réutilisables pour toutes les entités.

**Contenu :**
```
Pages/Organismes/entity/
├── EntityTable.vue                 # Tableau générique (utilise TanStackTable)
├── EntityModal.vue                 # Modal générique (utilise les vues Molecules)
├── EntityEditForm.vue              # Formulaire d'édition générique
├── EntityQuickEditPanel.vue        # Panneau quickedit latéral
├── EntityQuickEditModal.vue        # Modal quickedit
├── EntityActions.vue                # Actions d'entité
├── EntityActionsMenu.vue            # Menu d'actions
├── EntityRelationsManager.vue       # Gestionnaire de relations
└── CreateEntityModal.vue            # Modal de création
```

**Responsabilités :**
- Composants complexes réutilisables pour toutes les entités
- Orchestration de plusieurs molecules/atoms
- Logique d'interaction complexe
- Utilise les vues Molecules spécifiques (`ResourceViewLarge.vue`, etc.)

**❌ Ne contient PAS :**
- Logique spécifique à une entité (déléguée aux vues Molecules)
- Configuration (déléguée aux configs)

---

### 7. **Pages** — Pages complètes

**Emplacement :** `resources/js/Pages/Pages/entity/{entity}/`

**Rôle :** Pages complètes pour chaque entité.

**Contenu :**
```
Pages/Pages/entity/
├── resource/
│   └── Index.vue                    # Page liste Resource (utilise EntityTable)
│
├── item/
│   └── Index.vue                    # Page liste Item
│
└── ...                              # Autres entités
```

**Responsabilités :**
- Page complète avec layout
- Utilise les Organisms (`EntityTable`, `EntityModal`, etc.)
- Gestion du state global de la page
- Navigation et routing

**❌ Ne contient PAS :**
- Logique métier (déléguée aux modèles)
- Formatage (délégué aux formatters)
- Configuration (déléguée aux configs)

---

## 🔄 Flux de données

### Backend → Frontend

```
Backend (raw data)
    ↓
Adapter (Entities/{entity}/{entity}-adapter.js)
    ↓
Model (Models/Entity/{Entity}.js)
    ↓
Formatter (Utils/Formatters/{Property}Formatter.js)
    ↓
Vue Component (Pages/Molecules/entity/{entity}/{Entity}View*.vue)
    ↓
Atom (Pages/Atoms/data-display/*.vue)
    ↓
Rendu final
```

### Exemple concret : Resource

```
Backend API Response
    ↓
resource-adapter.js → adaptResourceEntitiesTableResponse()
    ↓
Resource.fromArray(rawData) → Array<Resource>
    ↓
resource.toCell('rarity', { size: 'md' })
    ↓
RarityFormatter.toCell(value, size)
    ↓
ResourceViewLarge.vue → <Badge :label="..." :color="..." />
    ↓
Badge.vue (Atom)
    ↓
Rendu final
```

---

## 📊 Matrice de responsabilités

| Élément | Logique métier | Formatage | Configuration | UI | Réutilisable |
|---------|----------------|-----------|---------------|-----|--------------|
| **Models** | ✅ | ✅ | ❌ | ❌ | ✅ |
| **Formatters** | ❌ | ✅ | ❌ | ❌ | ✅ |
| **Entities (configs)** | ❌ | ❌ | ✅ | ❌ | ✅ |
| **Atoms** | ❌ | ❌ | ❌ | ✅ | ✅ |
| **Molecules** | ❌ | ❌ | ❌ | ✅ | ❌ (spécifique entité) |
| **Organisms** | ❌ | ❌ | ❌ | ✅ | ✅ |
| **Pages** | ❌ | ❌ | ❌ | ✅ | ❌ (spécifique entité) |

---

## 🎨 Exemples de structure complète pour Resource

### Structure des fichiers

```
resources/js/
├── Models/Entity/
│   └── Resource.js                 # ✅ Logique métier + formatage
│
├── Utils/Formatters/
│   ├── RarityFormatter.js          # ✅ Formatage rarity
│   ├── LevelFormatter.js           # ✅ Formatage level
│   └── ...
│
├── Entities/resource/
│   ├── resource-descriptors.js     # ✅ Configuration simplifiée
│   ├── ResourceTableConfig.js      # ✅ Config tableau
│   ├── ResourceFormConfig.js       # ✅ Config formulaire
│   ├── ResourceBulkConfig.js       # ✅ Config bulk
│   └── resource-adapter.js         # ✅ Adapter raw → model
│
├── Pages/Atoms/data-display/
│   ├── CellRenderer.vue            # ✅ Rendu cellule
│   ├── Badge.vue                   # ✅ Badge générique
│   └── ...
│
├── Pages/Molecules/entity/resource/
│   ├── ResourceViewLarge.vue       # ✅ Vue Large manuelle
│   ├── ResourceViewCompact.vue     # ✅ Vue Compact manuelle
│   ├── ResourceViewMinimal.vue     # ✅ Vue Minimal manuelle
│   └── ResourceViewText.vue        # ✅ Vue Text manuelle
│
├── Pages/Organismes/entity/
│   ├── EntityTable.vue             # ✅ Tableau générique
│   ├── EntityModal.vue             # ✅ Modal générique
│   └── ...
│
└── Pages/Pages/entity/resource/
    └── Index.vue                    # ✅ Page complète
```

### Exemple d'utilisation dans ResourceViewLarge.vue

```vue
<template>
  <div class="resource-view-large">
    <!-- Utilise les méthodes du modèle -->
    <h1>{{ resource.name }}</h1>
    
    <!-- Utilise les formatters via le modèle -->
    <Badge 
      v-if="resource.hasRarity()"
      :label="resource.formatRarity()"
      :color="resource.toRarityCell().params.color"
    />
    
    <!-- Utilise toCell() pour les cellules -->
    <div v-for="field in visibleFields" :key="field">
      <CellRenderer :cell="resource.toCell(field, { size: 'lg' })" />
    </div>
  </div>
</template>

<script setup>
import { Resource } from '@/Models/Entity/Resource'
import Badge from '@/Pages/Atoms/data-display/Badge.vue'
import CellRenderer from '@/Pages/Atoms/data-display/CellRenderer.vue'

const props = defineProps({
  resource: {
    type: Resource,
    required: true
  }
})
</script>
```

---

## 🔄 Migration depuis l'ancienne structure

### Fichiers à déplacer

| Ancien emplacement | Nouveau emplacement | Action |
|-------------------|---------------------|--------|
| `Entities/resource/ResourceViewLarge.js` | `Pages/Molecules/entity/resource/ResourceViewLarge.vue` | Convertir JS → Vue, déplacer |
| `Entities/resource/ResourceViewCompact.js` | `Pages/Molecules/entity/resource/ResourceViewCompact.vue` | Convertir JS → Vue, déplacer |
| `Entities/resource/ResourceViewMinimal.js` | `Pages/Molecules/entity/resource/ResourceViewMinimal.vue` | Convertir JS → Vue, déplacer |
| `Pages/Molecules/entity/EntityView*.vue` | ❌ | **Supprimer** (génériques obsolètes) |

### Fichiers à garder (adaptés)

| Fichier | Action |
|---------|--------|
| `Models/Entity/Resource.js` | ✅ Enrichir avec `toCell()`, etc. |
| `Entities/resource/resource-descriptors.js` | ✅ Simplifier (table + form uniquement) |
| `Entities/resource/resource-adapter.js` | ✅ Simplifier (créer modèles uniquement) |
| `Pages/Organismes/entity/EntityModal.vue` | ✅ Adapter pour utiliser vues Molecules spécifiques |
| `Pages/Pages/entity/resource/Index.vue` | ✅ Adapter pour utiliser nouvelles configs |

---

## ✅ Checklist de validation

### Structure
- [ ] Tous les modèles dans `Models/Entity/`
- [ ] Tous les formatters dans `Utils/Formatters/`
- [ ] Toutes les configs dans `Entities/{entity}/`
- [ ] Toutes les vues spécifiques dans `Pages/Molecules/entity/{entity}/`
- [ ] Tous les composants génériques dans `Pages/Organismes/entity/`
- [ ] Toutes les pages dans `Pages/Pages/entity/{entity}/`

### Séparation des responsabilités
- [ ] Aucune logique métier dans les composants Vue
- [ ] Aucun formatage dans les adapters
- [ ] Aucune configuration dans les modèles
- [ ] Aucune vue générique (toutes spécifiques par entité)

### Cohérence
- [ ] Même structure pour toutes les entités
- [ ] Même pattern de nommage
- [ ] Même organisation des fichiers

---

## 📝 Notes importantes

1. **Vues spécifiques par entité** : Chaque entité a ses propres vues (ResourceViewLarge.vue, ItemViewLarge.vue, etc.). Pas de vues génériques.

2. **Composants génériques** : Les Organisms (EntityTable, EntityModal, etc.) restent génériques et utilisent les vues Molecules spécifiques.

3. **Formatage délégué** : Toute la logique de formatage est dans les modèles et formatters, jamais dans les composants Vue.

4. **Configuration centralisée** : Toute la configuration est dans `Entities/{entity}/`, jamais dans les composants Vue.

5. **Atomic Design strict** : Respecter la hiérarchie Atoms → Molecules → Organisms → Pages.

---

## 🔗 Références

- **Plan de refactoring** : `docs/110- To Do/PLAN_REFACTORING_ENTITIES.md`
- **Spécifications complètes** : `docs/110- To Do/New Système d'Entity.md`
- **Atomic Design** : `docs/30-UI/ATOMIC_DESIGN.md`
- **Structure du projet** : `docs/10-BestPractices/PROJECT_STRUCTURE.md`
