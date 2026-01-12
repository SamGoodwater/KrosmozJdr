# Spécification — Descriptor Centralisé et Génération Automatique

**Date de création** : 2026-01-XX  
**Objectif** : Centraliser toute la configuration dans un seul descriptor et générer automatiquement TableConfig et BulkConfig

---

## 🎯 Principe

**Un seul fichier de description** (`resource-descriptors.js`) contient TOUTE la configuration nécessaire pour :
- ✅ Générer automatiquement les colonnes du tableau
- ✅ Générer automatiquement les cellules
- ✅ Générer automatiquement le quickedit

**Des helpers génériques** génèrent automatiquement :
- ✅ `TableConfig` depuis les descriptors
- ✅ `BulkConfig` depuis les descriptors

**Résultat :** Code DRY, maintenable, source de vérité unique.

---

## 📋 Structure complète du Descriptor

### 1. Informations de base pour chaque propriété

```javascript
{
  key: "name",                    // ✅ Clé unique (obligatoire)
  label: "Nom",                   // ✅ Libellé (obligatoire)
  helper: "Nom de la ressource",  // ✅ Texte d'aide (optionnel)
  icon: "fa-solid fa-font",       // ✅ Icône FontAwesome (optionnel)
}
```

### 2. Configuration tableau (par propriété)

```javascript
{
  table: {
    // Permissions
    permission: "view",           // ✅ Permission requise pour voir la colonne (optionnel)
    
    // Tri, recherche, filtres
    sortable: true,               // ✅ Peut-on trier cette colonne ?
    searchable: true,             // ✅ Peut-on rechercher dans cette colonne ?
    filterable: {                 // ✅ Configuration du filtre (optionnel)
      type: "multi",              // Type : "multi", "select", "boolean", "date", "number", "function"
      id: "name",                 // ID du filtre (pour le backend)
      options: [...],             // Options si type = "select" ou "multi"
      function: (value) => {...}, // Fonction si type = "function"
      ui: {                       // Configuration UI du filtre
        optionBadge: {
          enabled: true,
          color: "auto",
          autoScheme: "rarity",
          glassy: true,
          variant: "soft",
        },
      },
    },
    
    // Visibilité
    defaultVisible: {             // ✅ Affichage par défaut selon taille
      xs: false,
      sm: true,
      md: true,
      lg: true,
      xl: true,
    },
    
    // Format des valeurs selon taille
    format: {                     // ✅ Format des cellules selon taille
      xs: { 
        mode: "icon",             // Mode : "badge", "text", "icon", "route", "image", "date", etc.
        truncate: 10,             // Tronquer à X caractères (optionnel)
        maxLength: 10,            // Alias de truncate
      },
      sm: { mode: "badge" },
      md: { mode: "badge" },
      lg: { mode: "badge" },
      xl: { mode: "badge" },
    },
    
    // Fonction de formatage personnalisée (optionnel)
    formatFunction: (value, options) => {
      // Formatage personnalisé si nécessaire
      return formattedValue;
    },
    
    // Ordre dans le tableau
    order: 4,                     // ✅ Ordre d'affichage (optionnel, défaut: ordre alphabétique)
    
    // Colonne principale (non masquable)
    isMain: true,                 // ✅ Colonne principale (optionnel, défaut: false)
    
    // Type de colonne (déduit automatiquement depuis format, mais peut être forcé)
    type: "route",                // ✅ Type : "text", "badge", "route", "image", "date", etc. (optionnel, déduit depuis format)
  },
}
```

### 3. Configuration quickedit (par propriété)

```javascript
{
  quickedit: {
    enabled: true,                // ✅ Modifiable en quickedit (obligatoire si quickedit existe)
    group: "Métier",              // ✅ Groupe de formulaire (obligatoire si enabled)
    type: "select",               // ✅ Type de champ : "text", "textarea", "select", "checkbox", "number", "date", "file" (obligatoire si enabled)
    
    // Propriétés liées au type
    options: [...],                // ✅ Options si type = "select" (obligatoire si type = "select")
    placeholder: "Ex: 50",        // ✅ Placeholder (optionnel)
    helper: "Texte d'aide",       // ✅ Texte d'aide spécifique (optionnel, utilise helper de base si non fourni)
    label: "Niveau",              // ✅ Label spécifique (optionnel, utilise label de base si non fourni)
    defaultValue: false,          // ✅ Valeur par défaut (optionnel)
    
    // Validation
    nullable: true,                // ✅ Peut être null/vide (obligatoire si enabled)
    required: false,               // ✅ Champ obligatoire (optionnel, défaut: false)
    validate: (value) => {         // ✅ Fonction de validation personnalisée (optionnel)
      if (value < 0) return "Le niveau doit être positif";
      return null; // null = valide
    },
    
    // Propriétés spécifiques au type
    min: 0,                       // ✅ Min si type = "number" (optionnel)
    max: 200,                     // ✅ Max si type = "number" (optionnel)
    step: 1,                      // ✅ Step si type = "number" (optionnel)
    accept: "image/*",            // ✅ Accept si type = "file" (optionnel)
    multiple: false,              // ✅ Multiple si type = "select" ou "file" (optionnel)
  },
}
```

### 4. Configuration tableau globale

```javascript
{
  tableConfig: {
    // ID et type d'entité
    id: "resources.index",         // ✅ ID unique du tableau (obligatoire)
    entityType: "resource",       // ✅ Type d'entité (obligatoire)
    
    // Permissions globales
    permission: "view",            // ✅ Permission requise pour voir le tableau (optionnel)
    
    // Features
    features: {
      search: {                   // ✅ Recherche globale
        enabled: true,
        placeholder: "Rechercher une ressource…",
        debounceMs: 200,
        fields: ["name", "description"], // ✅ Champs dans lesquels chercher (optionnel, tous les searchable par défaut)
      },
      sort: {                     // ✅ Tri global
        enabled: true,
        defaultSort: {             // ✅ Tri par défaut (optionnel)
          field: "name",
          direction: "asc",
        },
      },
      filters: {                  // ✅ Filtres globaux
        enabled: true,
        position: "top",          // ✅ Position : "top", "sidebar" (optionnel, défaut: "top")
      },
      pagination: {               // ✅ Pagination
        enabled: true,
        perPage: {                 // ✅ Options de pagination
          default: 25,
          options: [10, 25, 50, 100],
        },
      },
      selection: {                 // ✅ Sélection
        enabled: true,
        checkboxMode: "auto",      // ✅ Mode : "auto", "always", "never"
        clickToSelect: true,       // ✅ Clic sur ligne = sélection (optionnel)
        multiple: true,            // ✅ Sélection multiple (optionnel, défaut: true)
      },
      columnVisibility: {         // ✅ Masquage/affichage colonnes
        enabled: true,
        persist: true,             // ✅ Persister les préférences (optionnel)
      },
      export: {                   // ✅ Export
        enabled: true,
        csv: true,                 // ✅ Export CSV (optionnel)
        excel: false,              // ✅ Export Excel (optionnel)
        pdf: false,                // ✅ Export PDF (optionnel)
        filename: "resources.csv", // ✅ Nom du fichier (optionnel)
      },
    },
    
    // QuickEdit
    quickEdit: {
      enabled: true,               // ✅ Activer le quickedit
      permission: "updateAny",     // ✅ Permission requise (optionnel)
    },
    
    // Actions
    actions: {
      enabled: true,               // ✅ Activer les actions
      permission: "view",          // ✅ Permission requise (optionnel)
      available: [                // ✅ Actions disponibles
        "view",                    // Ouvrir en page
        "quick-view",              // Ouvrir en modal
        "edit",                    // Éditer en page
        "quick-edit",              // Éditer en modal
        "delete",                  // Supprimer
        "copy-link",               // Copier l'URL
        "download-pdf",            // Télécharger PDF
        "refresh",                 // Rafraîchir
        "double-click-edit",       // Double-clic pour éditer en modal
      ],
      access: {                    // ✅ Accès aux actions
        button: {                  // ✅ Bouton au début de la ligne
          enabled: true,
          position: "start",       // ✅ Position : "start", "end" (optionnel, défaut: "start")
          defaultVisible: {        // ✅ Visibilité par défaut selon taille
            xs: false,
            sm: true,
            md: true,
            lg: true,
            xl: true,
          },
        },
        contextMenu: {             // ✅ Menu contextuel (clic droit)
          enabled: true,
        },
      },
    },
    
    // UI
    ui: {
      skeletonRows: 10,            // ✅ Nombre de lignes skeleton (optionnel)
    },
  },
}
```

### 5. Configuration quickedit globale

```javascript
{
  quickeditConfig: {
    enabled: true,                 // ✅ Activer le quickedit (obligatoire)
    permission: "updateAny",       // ✅ Permission requise (optionnel)
    fields: [                      // ✅ Liste des champs (optionnel, tous les enabled par défaut)
      "resource_type_id",
      "rarity",
      "level",
      // ...
    ],
  },
}
```

---

## 📝 Exemple complet : Resource

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
    
    level: {
      key: "level",
      label: "Niveau",
      helper: "Niveau de la ressource",
      icon: "fa-solid fa-level-up-alt",
      
      table: {
        sortable: true,
        filterable: {
          type: "multi",
          id: "level",
          ui: {
            optionBadge: {
              enabled: true,
              color: "auto",
              autoScheme: "level",
              glassy: true,
              variant: "soft",
            },
          },
        },
        defaultVisible: { xs: true, sm: true, md: true, lg: true, xl: true },
        format: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
        order: 5,
        type: "badge",
      },
      
      quickedit: {
        enabled: true,
        group: "Métier",
        type: "text",
        placeholder: "Ex: 50",
        nullable: true,
        validate: (value) => {
          if (value && (isNaN(value) || value < 0)) {
            return "Le niveau doit être un nombre positif";
          }
          return null;
        },
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

## 🔧 Helpers génériques

### 1. `generateTableConfigFromDescriptors(descriptors, ctx)`

Génère automatiquement un `TableConfig` depuis les descriptors.

```javascript
// Entities/entity/TableConfigHelpers.js

export function generateTableConfigFromDescriptors(descriptors, ctx = {}) {
  const tableConfig = descriptors._tableConfig || {};
  
  const config = new TableConfig({
    id: tableConfig.id,
    entityType: tableConfig.entityType,
  });
  
  // Configurer les features
  if (tableConfig.features) {
    config.withFeatures(tableConfig.features);
  }
  
  // Configurer quickEdit
  if (tableConfig.quickEdit) {
    config.withQuickEdit(tableConfig.quickEdit);
  }
  
  // Configurer actions
  if (tableConfig.actions) {
    config.withActions(tableConfig.actions);
  }
  
  // Générer les colonnes depuis les descriptors
  const fieldKeys = Object.keys(descriptors).filter(key => !key.startsWith('_'));
  
  for (const key of fieldKeys) {
    const descriptor = descriptors[key];
    if (descriptor.table) {
      const column = createColumnFromDescriptor(key, descriptor, ctx);
      config.addColumn(column);
    }
  }
  
  return config;
}
```

### 2. `createColumnFromDescriptor(fieldKey, descriptor, ctx)`

Génère automatiquement une `TableColumnConfig` depuis un descriptor.

```javascript
// Entities/entity/TableColumnConfigHelpers.js

export function createColumnFromDescriptor(fieldKey, descriptor, ctx = {}) {
  const table = descriptor.table || {};
  
  // Déduire le type depuis format si non fourni
  const type = table.type || inferTypeFromFormat(table.format);
  
  const column = new TableColumnConfig({
    key: fieldKey,
    label: descriptor.label,
    type: type,
    icon: descriptor.icon,
    tooltip: descriptor.helper,
  });
  
  // Appliquer les configurations
  if (table.permission) column.withPermission(table.permission);
  if (table.order !== undefined) column.withOrder(table.order);
  if (table.isMain) column.asMain(true);
  if (table.sortable) column.withSort(true);
  if (table.searchable) column.withSearch(true);
  if (table.filterable) column.withFilter(table.filterable);
  if (table.defaultVisible) column.withDefaultVisible(table.defaultVisible);
  if (table.format) column.withFormat(table.format);
  
  return column;
}
```

### 3. `generateBulkConfigFromDescriptors(descriptors, ctx)`

Génère automatiquement un `BulkConfig` depuis les descriptors.

```javascript
// Entities/entity/BulkConfigHelpers.js

export function generateBulkConfigFromDescriptors(descriptors, ctx = {}) {
  const quickeditConfig = descriptors._quickeditConfig || {};
  
  const bulkConfig = new BulkConfig({
    entityType: descriptors._tableConfig?.entityType || 'resource',
  });
  
  // Générer les champs depuis les descriptors
  const fieldKeys = Object.keys(descriptors).filter(key => !key.startsWith('_'));
  
  const quickeditFields = [];
  
  for (const key of fieldKeys) {
    const descriptor = descriptors[key];
    if (descriptor.quickedit?.enabled) {
      const field = createBulkFieldFromDescriptor(key, descriptor, ctx);
      bulkConfig.addField(key, field);
      quickeditFields.push(key);
    }
  }
  
  bulkConfig.withQuickEditFields(quickeditFields);
  
  return bulkConfig.build();
}
```

### 4. `createBulkFieldFromDescriptor(fieldKey, descriptor, ctx)`

Génère automatiquement une configuration de champ bulk depuis un descriptor.

```javascript
// Entities/entity/BulkConfigHelpers.js

export function createBulkFieldFromDescriptor(fieldKey, descriptor, ctx = {}) {
  const quickedit = descriptor.quickedit || {};
  
  return {
    enabled: quickedit.enabled,
    nullable: quickedit.nullable,
    label: quickedit.label || descriptor.label,
    type: quickedit.type,
    options: quickedit.options,
    placeholder: quickedit.placeholder,
    helper: quickedit.helper || descriptor.helper,
    defaultValue: quickedit.defaultValue,
    required: quickedit.required || false,
    validate: quickedit.validate,
    // Propriétés spécifiques au type
    min: quickedit.min,
    max: quickedit.max,
    step: quickedit.step,
    accept: quickedit.accept,
    multiple: quickedit.multiple,
  };
}
```

---

## 📦 Utilisation

### ResourceTableConfig.js (simplifié)

```javascript
import { generateTableConfigFromDescriptors } from "../entity/TableConfigHelpers.js";
import { getResourceFieldDescriptors } from "./resource-descriptors.js";

export function createResourceTableConfig(ctx = {}) {
  const descriptors = getResourceFieldDescriptors(ctx);
  return generateTableConfigFromDescriptors(descriptors, ctx);
}
```

### ResourceBulkConfig.js (simplifié)

```javascript
import { generateBulkConfigFromDescriptors } from "../entity/BulkConfigHelpers.js";
import { getResourceFieldDescriptors } from "./resource-descriptors.js";

export function createResourceBulkConfig(ctx = {}) {
  const descriptors = getResourceFieldDescriptors(ctx);
  return generateBulkConfigFromDescriptors(descriptors, ctx);
}
```

---

## ✅ Avantages

1. **Source de vérité unique** : Tout dans un seul fichier
2. **Code DRY** : Pas de duplication entre TableConfig et BulkConfig
3. **Maintenabilité** : Modifier une propriété = modifier un seul endroit
4. **Génération automatique** : Helpers génériques pour toutes les entités
5. **Cohérence** : Même structure pour toutes les entités
6. **Réduction de code** : ~70% de code en moins dans TableConfig et BulkConfig

---

## 📋 Checklist d'implémentation

- [ ] Créer la structure complète du descriptor dans `resource-descriptors.js`
- [ ] Créer `TableConfigHelpers.js` avec `generateTableConfigFromDescriptors()` et `createColumnFromDescriptor()`
- [ ] Créer `BulkConfigHelpers.js` avec `generateBulkConfigFromDescriptors()` et `createBulkFieldFromDescriptor()`
- [ ] Refactoriser `ResourceTableConfig.js` pour utiliser les helpers
- [ ] Refactoriser `ResourceBulkConfig.js` pour utiliser les helpers
- [ ] Tester que le tableau fonctionne correctement
- [ ] Tester que le quickedit fonctionne correctement
- [ ] Appliquer aux autres entités
- [ ] Mettre à jour la documentation

---

## 📚 Références

- [REDONDANCE_DESCRIPTORS_TABLECONFIG.md](./REDONDANCE_DESCRIPTORS_TABLECONFIG.md) — Analyse de la redondance
- [DESCRIPTORS_PATTERN.md](./DESCRIPTORS_PATTERN.md) — Rôle des descriptors
- [ARCHITECTURE_ENTITY_SYSTEM.md](./ARCHITECTURE_ENTITY_SYSTEM.md) — Architecture complète
