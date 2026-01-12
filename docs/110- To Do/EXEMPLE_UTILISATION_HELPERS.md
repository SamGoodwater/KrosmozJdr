# Exemple d'utilisation des Helpers génériques

**Date de création** : 2026-01-XX  
**Objectif** : Montrer comment utiliser les helpers pour générer automatiquement TableConfig et BulkConfig

---

## 📦 Imports

```javascript
import { generateTableConfigFromDescriptors, createColumnFromDescriptor } from '@/Entities/entity/TableConfigHelpers';
import { generateBulkConfigFromDescriptors, createBulkFieldFromDescriptor } from '@/Entities/entity/BulkConfigHelpers';
import { getResourceFieldDescriptors } from '@/Entities/resource/resource-descriptors';
```

---

## 🎯 Utilisation simple

### ResourceTableConfig.js (simplifié)

```javascript
/**
 * ResourceTableConfig — Configuration du tableau pour l'entité Resource
 *
 * @description
 * Génère automatiquement la configuration du tableau depuis les descriptors.
 * Plus besoin de définir manuellement chaque colonne !
 */

import { generateTableConfigFromDescriptors } from '../entity/TableConfigHelpers.js';
import { getResourceFieldDescriptors } from './resource-descriptors.js';

/**
 * Crée la configuration du tableau pour Resource
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {TableConfig} Configuration du tableau
 */
export function createResourceTableConfig(ctx = {}) {
  const descriptors = getResourceFieldDescriptors(ctx);
  return generateTableConfigFromDescriptors(descriptors, ctx);
}
```

### ResourceBulkConfig.js (simplifié)

```javascript
/**
 * ResourceBulkConfig — Configuration de l'édition en masse (bulk) pour Resource
 *
 * @description
 * Génère automatiquement la configuration bulk depuis les descriptors.
 * Plus besoin de définir manuellement chaque champ !
 */

import { generateBulkConfigFromDescriptors } from '../entity/BulkConfigHelpers.js';
import { getResourceFieldDescriptors } from './resource-descriptors.js';

/**
 * Crée la configuration bulk pour Resource
 *
 * @param {Object} [ctx] - Contexte (permissions, etc.)
 * @returns {BulkConfig} Configuration bulk
 */
export function createResourceBulkConfig(ctx = {}) {
  const descriptors = getResourceFieldDescriptors(ctx);
  return generateBulkConfigFromDescriptors(descriptors, ctx);
}
```

---

## 🔄 Migration progressive

Les helpers supportent **les deux structures** pour une migration progressive :

### Structure actuelle (supportée)

```javascript
{
  name: {
    key: "name",
    label: "Nom",
    icon: "fa-solid fa-font",
    display: {
      sizes: {
        xs: { mode: "route", truncate: 20 },
        sm: { mode: "route", truncate: 30 },
      },
    },
    edit: {
      form: {
        bulk: {
          enabled: true,
          nullable: true,
        },
      },
    },
  },
}
```

### Structure nouvelle (recommandée)

```javascript
{
  name: {
    key: "name",
    label: "Nom",
    icon: "fa-solid fa-font",
    helper: "Nom de la ressource",
    table: {
      sortable: true,
      searchable: true,
      defaultVisible: { xs: true, sm: true, md: true, lg: true, xl: true },
      format: {
        xs: { mode: "route", truncate: 20 },
        sm: { mode: "route", truncate: 30 },
      },
      order: 4,
      isMain: true,
      type: "route",
    },
    quickedit: {
      enabled: false, // Le nom ne peut pas être modifié en bulk
    },
  },
}
```

---

## 🎨 Exemple complet avec la nouvelle structure

### resource-descriptors.js (extrait)

```javascript
export function getResourceFieldDescriptors(ctx = {}) {
  return {
    name: {
      key: "name",
      label: "Nom",
      helper: "Nom de la ressource",
      icon: "fa-solid fa-font",
      
      table: {
        sortable: true,
        searchable: true,
        defaultVisible: { xs: true, sm: true, md: true, lg: true, xl: true },
        format: {
          xs: { mode: "route", truncate: 20 },
          sm: { mode: "route", truncate: 30 },
          md: { mode: "route", truncate: 44 },
          lg: { mode: "route", truncate: 60 },
          xl: { mode: "route" },
        },
        order: 4,
        isMain: true,
        type: "route",
      },
      
      quickedit: {
        enabled: false, // Le nom ne peut pas être modifié en bulk
      },
    },
    
    rarity: {
      key: "rarity",
      label: "Rareté",
      helper: "La rareté est un entier (0..5)",
      icon: "fa-solid fa-star",
      
      table: {
        sortable: true,
        filterable: {
          type: "multi",
          id: "rarity",
          ui: {
            optionBadge: {
              enabled: true,
              color: "auto",
              autoScheme: "rarity",
              glassy: true,
              variant: "soft",
            },
          },
        },
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
        format: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
        order: 7,
        type: "badge",
      },
      
      quickedit: {
        enabled: true,
        group: "Métier",
        type: "select",
        options: RarityFormatter.options.map(({ value, label }) => ({ value, label })),
        helper: "La rareté est un entier (0..5). En bulk, laisser vide n'applique aucun changement.",
        nullable: true,
      },
    },
    
    // ... autres champs
    
    // Configuration globale du tableau
    _tableConfig: {
      id: "resources.index",
      entityType: "resource",
      permission: "view",
      features: {
        search: {
          enabled: true,
          placeholder: "Rechercher une ressource…",
          debounceMs: 200,
        },
        sort: { enabled: true },
        filters: { enabled: true },
        pagination: {
          enabled: true,
          perPage: { default: 25, options: [10, 25, 50, 100] },
        },
        selection: {
          enabled: true,
          checkboxMode: "auto",
          clickToSelect: true,
          multiple: true,
        },
        columnVisibility: {
          enabled: true,
          persist: true,
        },
        export: {
          enabled: true,
          csv: true,
          filename: "resources.csv",
        },
      },
      quickEdit: {
        enabled: true,
        permission: "updateAny",
      },
      actions: {
        enabled: true,
        permission: "view",
        available: ["view", "edit", "quick-edit", "delete", "copy-link", "download-pdf", "refresh"],
        access: {
          button: {
            enabled: true,
            position: "start",
            defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
          },
          contextMenu: {
            enabled: true,
          },
        },
      },
      ui: {
        skeletonRows: 10,
      },
    },
    
    // Configuration globale du quickedit
    _quickeditConfig: {
      enabled: true,
      permission: "updateAny",
    },
  };
}
```

---

## ✅ Avantages

1. **Code réduit** : `ResourceTableConfig.js` passe de 347 lignes à ~10 lignes
2. **Source de vérité unique** : Tout dans `resource-descriptors.js`
3. **DRY** : Pas de duplication entre TableConfig et BulkConfig
4. **Maintenabilité** : Modifier une propriété = modifier un seul endroit
5. **Cohérence** : Même structure pour toutes les entités
6. **Migration progressive** : Support des deux structures

---

## 📋 Prochaines étapes

1. ✅ Helpers créés
2. ⏳ Refactoriser `resource-descriptors.js` avec la nouvelle structure
3. ⏳ Simplifier `ResourceTableConfig.js` et `ResourceBulkConfig.js`
4. ⏳ Tester que tout fonctionne
5. ⏳ Appliquer aux autres entités

---

## 📚 Références

- [SPECIFICATION_DESCRIPTOR_CENTRALISE.md](./SPECIFICATION_DESCRIPTOR_CENTRALISE.md) — Spécification complète
- [REDONDANCE_DESCRIPTORS_TABLECONFIG.md](./REDONDANCE_DESCRIPTORS_TABLECONFIG.md) — Analyse de la redondance
