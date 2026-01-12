# Guide des Descriptors

**Version** : 2.0  
**Date** : 2026-01-XX

---

## 🎯 Rôle

Les **descriptors** sont la **source de vérité déclarative** pour la configuration UX de chaque entité. Ils décrivent :
- Comment afficher les champs (labels, icônes, formatage)
- Comment éditer les champs (types, validation, options)
- Quelles permissions sont requises (visibleIf, editableIf)
- Comment configurer le tableau (colonnes, visibilité, formatage)

---

## 📁 Emplacement

```
Entities/{entity}/{entity}-descriptors.js
```

Exemple : `Entities/resource/resource-descriptors.js`

---

## 📋 Structure d'un descriptor

```javascript
export function getResourceFieldDescriptors(ctx = {}) {
  return {
    // Descriptor d'un champ
    fieldKey: {
      // Métadonnées générales
      general: {
        label: "Niveau",
        icon: "fa-solid fa-level-up-alt",
        tooltip: "Niveau de la ressource"
      },
      
      // Permissions
      permissions: {
        visibleIf: (ctx) => boolean,  // Visibilité en mode read
        editableIf: (ctx) => boolean  // Éditabilité
      },
      
      // Configuration tableau
      table: {
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
        visibleIf: (ctx) => boolean,  // Visibilité de la colonne
        cell: {
          sizes: {
            xs: { mode: "badge" },
            sm: { mode: "badge" },
            md: { mode: "badge" },
            lg: { mode: "badge" },
            xl: { mode: "badge" }
          }
        }
      },
      
      // Configuration affichage (vues Large, Compact, etc.)
      display: {
        tooltip: "...",
        style: { compact: "...", large: "..." },
        color: { compact: "...", large: "..." },
        format: "rarity" // Clé du formatter
      },
      
      // Configuration édition
      edition: {
        form: {
          type: "select",
          required: true,
          validation: { min: 0, max: 5 },
          options: [...] // ou fonction(ctx)
        },
        bulk: {
          enabled: true,
          nullable: true
        }
      }
    },
    
    // Configuration globale tableau
    _tableConfig: {
      id: "resources.index",
      entityType: "resource",
      quickEdit: { enabled: true, permission: "updateAny" },
      actions: { enabled: true, permission: "view" },
      features: { search: {...}, filters: {...}, pagination: {...} }
    },
    
    // Configuration globale quickedit
    _quickeditConfig: {
      fields: ["resource_type_id", "rarity", "level", ...]
    }
  };
}
```

---

## 🔑 Sections principales

### `general`
Métadonnées utilisées partout (tableau, vues, formulaires).

- **`label`** : Libellé traduit (ex: "Niveau")
- **`icon`** : Icône FontAwesome (ex: "fa-solid fa-level-up-alt")
- **`tooltip`** : Tooltip par défaut

### `permissions`
Contrôle la visibilité et l'éditabilité selon les permissions.

- **`visibleIf(ctx)`** : Fonction retournant `true` si le champ est visible
- **`editableIf(ctx)`** : Fonction retournant `true` si le champ est éditable

**Exemple :**
```javascript
permissions: {
  visibleIf: (ctx) => {
    const can = ctx?.capabilities?.updateAny || ctx?.meta?.capabilities?.updateAny || false;
    return can;
  }
}
```

### `table`
Configuration spécifique pour les tableaux.

- **`defaultVisible`** : Visibilité par défaut selon la taille d'écran
- **`visibleIf(ctx)`** : Fonction pour vérifier la visibilité de la colonne
- **`cell.sizes`** : Formatage de la cellule selon la taille d'écran

**Exemple :**
```javascript
table: {
  defaultVisible: {
    xs: false,
    sm: false,
    md: false,
    lg: false,
    xl: false  // Caché par défaut (admin seulement)
  },
  visibleIf: (ctx) => Boolean(ctx?.capabilities?.updateAny),
  cell: {
    sizes: {
      xs: { mode: "text" },
      sm: { mode: "text" },
      md: { mode: "text" },
      lg: { mode: "text" },
      xl: { mode: "text" }
    }
  }
}
```

### `display`
Configuration pour les vues d'affichage (Large, Compact, Minimal, Text).

- **`tooltip`** : Tooltip spécifique pour les vues
- **`style`** : Classes CSS selon le variant de vue
- **`color`** : Couleurs selon le variant de vue
- **`format`** : Clé du formatter à utiliser

### `edition`
Configuration pour l'édition (Large, Compact, QuickEdit).

#### `edition.form`
Configuration du formulaire d'édition.

- **`type`** : Type de champ (`text`, `textarea`, `select`, `checkbox`, `number`, `date`, `file`)
- **`required`** : Champ obligatoire
- **`validation`** : Règles de validation (`min`, `max`, `minLength`, `maxLength`, `pattern`, `validator`, `message`)
- **`options`** : Options pour les selects (tableau ou fonction `(ctx) => [...]`)
- **`placeholder`** : Placeholder pour les inputs
- **`help`** : Texte d'aide
- **`searchable`** : Pour les selects, active la recherche (utilise `SelectSearchField`)

#### `edition.bulk`
Configuration pour l'édition en masse (QuickEdit).

- **`enabled`** : Activer l'édition en masse
- **`nullable`** : Permettre null/vide en bulk

---

## 🚫 Règles strictes

### ✅ Autorisé
- Déclarations pures (pas de logique)
- Constantes et options
- Fonctions conditionnelles (`visibleIf`, `editableIf`)
- Options dynamiques via fonctions `(ctx) => [...]`

### ❌ Interdit
- Logique métier
- Calculs
- Appels à des modèles
- État ou effets de bord
- Formatage (délégué aux formatters)

---

## 📖 Utilisation

### Dans les tableaux
```javascript
const descriptors = getResourceFieldDescriptors(ctx);
const tableConfig = TableConfig.fromDescriptors(descriptors, ctx);
// Génère les colonnes avec headers, visibilité, formatage
```

### Dans les vues
```javascript
const descriptors = getResourceFieldDescriptors(ctx);
const label = descriptors.rarity.general.label; // "Rareté"
const icon = descriptors.rarity.general.icon; // "fa-solid fa-gem"
const isVisible = descriptors.rarity.permissions.visibleIf(ctx);
```

### Dans les formulaires
```javascript
const descriptors = getResourceFieldDescriptors(ctx);
const fieldsConfig = createFieldsConfigFromDescriptors(descriptors, ctx);
// Génère la config complète pour chaque champ
```

### Dans QuickEdit
```javascript
const descriptors = getResourceFieldDescriptors(ctx);
const fieldMeta = createBulkFieldMetaFromDescriptors(descriptors, ctx);
// Génère la meta pour useBulkEditPanel
```

---

## 🔗 Liens utiles

- [ARCHITECTURE.md](./ARCHITECTURE.md) — Architecture complète
- [FLUX_COMPLETS.md](./FLUX_COMPLETS.md) — Flux détaillés
- [resource-descriptors.js](../../resources/js/Entities/resource/resource-descriptors.js) — Exemple complet
