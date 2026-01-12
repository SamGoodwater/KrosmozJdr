# Redondance entre Descriptors et TableConfig

**Date de création** : 2026-01-XX  
**Problème identifié** : Redondance entre `resource-descriptors.js` et `ResourceTableConfig.js`

---

## 🔍 Problème identifié

### Redondance actuelle

Les deux fichiers définissent les mêmes informations :

1. **resource-descriptors.js** définit :
   ```javascript
   name: {
     key: "name",
     label: "Nom",
     icon: "fa-solid fa-font",
     display: {
       sizes: {
         xs: { mode: "route", truncate: 20 },
         sm: { mode: "route", truncate: 30 },
         md: { mode: "route", truncate: 44 },
         lg: { mode: "route", truncate: 60 },
         xl: { mode: "route" },
       },
     },
   }
   ```

2. **ResourceTableConfig.js** redéfinit tout ça :
   ```javascript
   .addColumn(
     new TableColumnConfig({
       key: "name",
       label: descriptors.name?.label || "Nom",  // ❌ Redondant
       type: "route",
       icon: descriptors.name?.icon || "fa-solid fa-font",  // ❌ Redondant
     })
       .withFormat({
         xs: { mode: "truncate", maxLength: 20 },  // ❌ Redondant avec display.sizes
         sm: { mode: "truncate", maxLength: 30 },
         md: { mode: "truncate", maxLength: 44 },
         lg: { mode: "full" },
         xl: { mode: "full" },
       })
   )
   ```

### Ce qui est redondant

- ✅ **Labels** : Définis dans descriptors, redéfinis dans TableConfig
- ✅ **Icônes** : Définies dans descriptors, redéfinies dans TableConfig
- ✅ **Formatage responsive** : `display.sizes` dans descriptors, `.withFormat()` dans TableConfig
- ✅ **Types de colonnes** : Déduits de `display.sizes.mode`, redéfinis manuellement dans TableConfig

### Ce qui est spécifique au tableau (non redondant)

- ✅ **Ordre des colonnes** : Spécifique au tableau
- ✅ **Tri, recherche, filtres** : Spécifiques au tableau
- ✅ **Permissions de colonnes** : Peuvent différer des permissions de champs
- ✅ **Visibilité par défaut** : Peut différer de `visibleIf` dans descriptors

---

## 💡 Solution proposée

### Option 1 : Helper pour générer les colonnes depuis les descriptors ✅ **RECOMMANDÉ**

Créer une fonction helper qui génère automatiquement une `TableColumnConfig` à partir d'un descriptor :

```javascript
// Entities/entity/TableColumnConfigHelpers.js

/**
 * Génère une TableColumnConfig à partir d'un descriptor
 * 
 * @param {string} fieldKey - Clé du champ
 * @param {Object} descriptor - Descriptor du champ
 * @param {Object} [options] - Options spécifiques au tableau
 * @param {number} [options.order] - Ordre de la colonne
 * @param {boolean} [options.sort] - Activer le tri
 * @param {boolean} [options.search] - Activer la recherche
 * @param {Object} [options.filter] - Configuration du filtre
 * @param {Object} [options.defaultVisible] - Visibilité par défaut
 * @param {string} [options.permission] - Permission spécifique
 * @returns {TableColumnConfig}
 */
export function createColumnFromDescriptor(fieldKey, descriptor, options = {}) {
  if (!descriptor || !descriptor.display?.sizes) {
    throw new Error(`Descriptor manquant ou sans display.sizes pour ${fieldKey}`);
  }

  // Extraire le type de colonne depuis le mode le plus courant
  const modes = Object.values(descriptor.display.sizes).map(s => s.mode);
  const mostCommonMode = modes.reduce((a, b, i, arr) => 
    arr.filter(v => v === a).length >= arr.filter(v => v === b).length ? a : b
  );
  
  // Mapper les modes vers les types de colonnes
  const modeToType = {
    'badge': 'badge',
    'text': 'text',
    'route': 'route',
    'routeExternal': 'routeExternal',
    'thumb': 'image',
    'boolIcon': 'badge',
    'boolBadge': 'badge',
    'dateShort': 'date',
    'dateTime': 'date',
  };
  
  const type = modeToType[mostCommonMode] || 'text';

  // Convertir display.sizes en format pour TableColumnConfig
  const format = {};
  for (const [size, config] of Object.entries(descriptor.display.sizes)) {
    format[size] = {
      mode: config.mode,
      ...(config.truncate && { maxLength: config.truncate }),
    };
  }

  const column = new TableColumnConfig({
    key: fieldKey,
    label: descriptor.label,
    type: type,
    icon: descriptor.icon,
  });

  // Appliquer le formatage depuis display.sizes
  column.format = format;

  // Appliquer les options spécifiques au tableau
  if (options.order !== undefined) column.withOrder(options.order);
  if (options.sort) column.withSort(true);
  if (options.search) column.withSearch(true);
  if (options.filter) column.withFilter(options.filter);
  if (options.defaultVisible) column.withDefaultVisible(options.defaultVisible);
  if (options.permission) column.withPermission(options.permission);
  if (options.isMain) column.asMain(true);

  return column;
}
```

**Utilisation dans ResourceTableConfig.js :**

```javascript
import { createColumnFromDescriptor } from "../entity/TableColumnConfigHelpers.js";

export function createResourceTableConfig(ctx = {}) {
  const descriptors = getResourceFieldDescriptors(ctx);

  const tableConfig = new TableConfig({ /* ... */ });

  // Colonnes générées automatiquement depuis les descriptors
  tableConfig
    .addColumn(createColumnFromDescriptor('name', descriptors.name, {
      order: 4,
      sort: true,
      search: true,
      isMain: true,
    }))
    .addColumn(createColumnFromDescriptor('level', descriptors.level, {
      order: 5,
      sort: true,
      filter: { id: "level", type: "multi", /* ... */ },
    }))
    .addColumn(createColumnFromDescriptor('rarity', descriptors.rarity, {
      order: 7,
      sort: true,
      filter: { id: "rarity", type: "multi", /* ... */ },
      defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
    }));

  return tableConfig;
}
```

**Avantages :**
- ✅ Élimine la redondance (labels, icônes, formatage)
- ✅ Source de vérité unique : les descriptors
- ✅ Code plus court et maintenable
- ✅ Moins d'erreurs (pas de duplication)

**Inconvénients :**
- ⚠️ Nécessite une fonction helper
- ⚠️ Mapping des modes vers les types de colonnes

---

### Option 2 : Génération automatique complète ❌ **NON RECOMMANDÉ**

Générer automatiquement toutes les colonnes depuis les descriptors :

```javascript
// Générer toutes les colonnes automatiquement
Object.entries(descriptors).forEach(([key, desc]) => {
  if (desc.display?.sizes) {
    tableConfig.addColumn(createColumnFromDescriptor(key, desc));
  }
});
```

**Problèmes :**
- ❌ Perte de contrôle sur l'ordre
- ❌ Impossible de configurer tri/filtres spécifiques
- ❌ Pas de colonnes conditionnelles (permissions)
- ❌ Colonnes système (id, created_at, etc.) ne sont pas dans descriptors

---

### Option 3 : Déplacer la configuration du tableau dans les descriptors ❌ **NON RECOMMANDÉ**

Ajouter une section `table` dans les descriptors :

```javascript
name: {
  // ...
  table: {
    order: 4,
    sort: true,
    search: true,
    isMain: true,
  },
}
```

**Problèmes :**
- ❌ Mélange les responsabilités (descriptors = générique, table = spécifique)
- ❌ Les descriptors deviennent trop complexes
- ❌ Violation du principe de séparation des responsabilités

---

## ✅ Recommandation finale

**Option 1 : Helper pour générer les colonnes depuis les descriptors**

### Plan d'implémentation

1. **Créer `TableColumnConfigHelpers.js`**
   - Fonction `createColumnFromDescriptor()`
   - Mapping des modes vers les types
   - Conversion de `display.sizes` en `format`

2. **Refactoriser `ResourceTableConfig.js`**
   - Utiliser `createColumnFromDescriptor()` pour toutes les colonnes
   - Garder uniquement les configurations spécifiques au tableau (ordre, tri, filtres, permissions)

3. **Appliquer aux autres entités**
   - Refactoriser tous les `*TableConfig.js` de la même manière

### Exemple de code refactorisé

**Avant (347 lignes) :**
```javascript
.addColumn(
  new TableColumnConfig({
    key: "name",
    label: descriptors.name?.label || "Nom",
    type: "route",
    icon: descriptors.name?.icon || "fa-solid fa-font",
  })
    .asMain(true)
    .withOrder(4)
    .withSort(true)
    .withSearch(true)
    .withFormat({
      xs: { mode: "truncate", maxLength: 20 },
      sm: { mode: "truncate", maxLength: 30 },
      md: { mode: "truncate", maxLength: 44 },
      lg: { mode: "full" },
      xl: { mode: "full" },
    })
)
```

**Après (7 lignes) :**
```javascript
.addColumn(createColumnFromDescriptor('name', descriptors.name, {
  order: 4,
  sort: true,
  search: true,
  isMain: true,
}))
```

**Gain :** ~70% de code en moins, source de vérité unique dans les descriptors.

---

## 📋 Checklist de migration

- [ ] Créer `TableColumnConfigHelpers.js` avec `createColumnFromDescriptor()`
- [ ] Tester la fonction helper avec Resource
- [ ] Refactoriser `ResourceTableConfig.js`
- [ ] Vérifier que le tableau fonctionne correctement
- [ ] Appliquer aux autres entités (Item, Monster, etc.)
- [ ] Mettre à jour la documentation

---

## 📚 Références

- [DESCRIPTORS_PATTERN.md](./DESCRIPTORS_PATTERN.md) — Rôle des descriptors
- [ARCHITECTURE_ENTITY_SYSTEM.md](./ARCHITECTURE_ENTITY_SYSTEM.md) — Architecture complète
