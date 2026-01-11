# Guide d'utilisation — Système d'Actions pour les Entités

**Date** : 2026-01-06  
**Version** : 1.0

---

## 📋 Vue d'ensemble

Le système d'actions pour les entités permet d'afficher et de gérer les actions disponibles pour chaque type d'entité (spells, items, monsters, etc.) de manière unifiée et configurable.

### Fonctionnalités principales

- ✅ **Actions configurables** : 9 actions disponibles (view, quick-view, edit, quick-edit, copy-link, download-pdf, refresh, minimize, delete)
- ✅ **Permissions automatiques** : Filtrage selon les permissions utilisateur via `usePermissions`
- ✅ **Formats flexibles** : Boutons, dropdown, menu contextuel
- ✅ **Modes d'affichage** : Icône seule ou icône + texte
- ✅ **Filtrage avancé** : Whitelist/blacklist d'actions
- ✅ **Groupement** : Actions organisées par groupes avec séparateurs

---

## 🚀 Utilisation rapide

### Format : Liste de boutons (vues entités)

```vue
<template>
  <EntityActions
    entity-type="spells"
    :entity="entity"
    format="buttons"
    display="icon-only"
    @action="handleAction"
  />
</template>

<script setup>
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';

const handleAction = (actionKey, entity) => {
  switch (actionKey) {
    case 'view':
      // Ouvrir dans une page
      break;
    case 'quick-view':
      // Ouvrir dans une modal
      break;
    // ...
  }
};
</script>
```

### Format : Dropdown (colonne Actions dans tableau)

```vue
<template>
  <EntityActions
    entity-type="spells"
    :entity="row.entity"
    format="dropdown"
    display="icon-text"
    @action="handleAction"
  />
</template>
```

### Format : Menu contextuel (clic droit)

```vue
<template>
  <EntityActions
    entity-type="spells"
    :entity="contextEntity"
    format="context"
    display="icon-text"
    :context-position="{ x: 100, y: 200 }"
    :context-visible="contextMenuVisible"
    @action="handleAction"
  />
</template>
```

---

## 📚 API du composant `EntityActions`

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `entityType` | `String` | **requis** | Type d'entité (ex: 'spells', 'items') |
| `entity` | `Object` | `null` | Entité (peut être null pour certaines actions) |
| `format` | `String` | `'dropdown'` | Format : `'buttons'`, `'dropdown'`, `'context'` |
| `display` | `String` | `'icon-text'` | Mode : `'icon-only'` ou `'icon-text'` |
| `whitelist` | `Array` | `null` | Liste d'actions à inclure uniquement |
| `blacklist` | `Array` | `null` | Liste d'actions à exclure |
| `context` | `Object` | `{}` | Contexte (ex: `{ inPanel: true }` pour minimize) |
| `size` | `String` | `'sm'` | Taille des boutons (xs, sm, md, lg) |
| `color` | `String` | `'primary'` | Couleur des boutons |
| `placement` | `String` | `'bottom-end'` | Position du dropdown |
| `contextPosition` | `Object` | `null` | Position fixe pour menu contextuel `{ x, y }` |
| `contextVisible` | `Boolean` | `false` | Visible pour menu contextuel |

### Événements

| Événement | Paramètres | Description |
|-----------|------------|-------------|
| `action` | `(actionKey, entity)` | Émis pour chaque action |
| `view` | `(entity)` | Ouvrir dans une page |
| `quick-view` | `(entity)` | Ouvrir dans une modal |
| `edit` | `(entity)` | Modifier dans une page |
| `quick-edit` | `(entity)` | Modifier dans une modal |
| `copy-link` | `(entity)` | Copier le lien |
| `download-pdf` | `(entity)` | Télécharger PDF |
| `refresh` | `(entity)` | Rafraîchir les données |
| `minimize` | `(entity)` | Minimiser (fonctionnalité future) |
| `delete` | `(entity)` | Supprimer |

---

## 🎯 Actions disponibles

### Actions de navigation

- **`view`** : Ouvrir l'entité dans une page complète
  - Permission : `canView`
  - Icône : `fa-solid fa-eye`
  - Groupe : `navigation`

- **`quick-view`** : Ouvrir l'entité dans une modal rapide
  - Permission : `canView`
  - Icône : `fa-solid fa-window-maximize`
  - Groupe : `navigation`

### Actions d'édition

- **`edit`** : Modifier l'entité dans une page complète
  - Permission : `canUpdate`
  - Icône : `fa-solid fa-pen`
  - Groupe : `edition`

- **`quick-edit`** : Modifier l'entité dans une modal rapide
  - Permission : `canUpdate`
  - Icône : `fa-solid fa-bolt`
  - Groupe : `edition`

### Actions d'outils

- **`copy-link`** : Copier l'URL de l'entité
  - Permission : Aucune (toujours disponible)
  - Icône : `fa-solid fa-link`
  - Groupe : `tools`

- **`download-pdf`** : Télécharger l'entité en PDF
  - Permission : Aucune (toujours disponible)
  - Icône : `fa-solid fa-file-pdf`
  - Groupe : `tools`

- **`refresh`** : Rafraîchir les données (via scrapping)
  - Permission : `canManage` (admin)
  - Icône : `fa-solid fa-arrow-rotate-right`
  - Groupe : `tools`

- **`minimize`** : Minimiser un modal (fonctionnalité future)
  - Permission : Aucune
  - Icône : `fa-solid fa-window-minimize`
  - Groupe : `tools`
  - **Note** : Visible uniquement si `context.inPanel === true`

### Actions destructives

- **`delete`** : Supprimer l'entité
  - Permission : `canDelete`
  - Icône : `fa-solid fa-trash`
  - Variant : `error` (style rouge)
  - Groupe : `destructive`

---

## 🔧 Exemples d'utilisation

### Exemple 1 : Vues entités (Compact/Minimal)

```vue
<template>
  <div class="flex items-center justify-between">
    <h3>{{ entity.name }}</h3>
    <EntityActions
      :entity-type="entityType"
      :entity="entity"
      format="buttons"
      display="icon-only"
      size="sm"
      @action="handleAction"
    />
  </div>
</template>
```

### Exemple 2 : Vue Large (icône + texte)

```vue
<template>
  <div class="flex items-center justify-between">
    <h2>{{ entity.name }}</h2>
    <EntityActions
      :entity-type="entityType"
      :entity="entity"
      format="buttons"
      display="icon-text"
      size="sm"
      @action="handleAction"
    />
  </div>
</template>
```

### Exemple 3 : Tableau (colonne Actions)

```vue
<template>
  <td>
    <EntityActions
      :entity-type="entityType"
      :entity="row.entity"
      format="dropdown"
      display="icon-text"
      size="sm"
      @action="handleTableAction"
    />
  </td>
</template>
```

### Exemple 4 : Menu contextuel (clic droit)

```vue
<template>
  <tr @contextmenu.prevent="showContextMenu($event, row)">
    <!-- ... contenu de la ligne ... -->
  </tr>
  
  <EntityActions
    v-if="contextMenuVisible"
    :entity-type="entityType"
    :entity="contextEntity"
    format="context"
    display="icon-text"
    :context-position="contextMenuPosition"
    :context-visible="contextMenuVisible"
    @action="handleContextAction"
  />
</template>

<script setup>
import { ref } from 'vue';

const contextMenuVisible = ref(false);
const contextMenuPosition = ref({ x: 0, y: 0 });
const contextEntity = ref(null);

const showContextMenu = (event, row) => {
  event.preventDefault();
  contextMenuPosition.value = {
    x: event.clientX,
    y: event.clientY,
  };
  contextEntity.value = row.entity;
  contextMenuVisible.value = true;
};

const handleContextAction = (actionKey, entity) => {
  contextMenuVisible.value = false;
  // Gérer l'action...
};
</script>
```

### Exemple 5 : Filtrage avec whitelist

```vue
<template>
  <EntityActions
    entity-type="spells"
    :entity="entity"
    format="buttons"
    :whitelist="['view', 'edit', 'copy-link']"
    @action="handleAction"
  />
</template>
```

### Exemple 6 : Filtrage avec blacklist

```vue
<template>
  <EntityActions
    entity-type="spells"
    :entity="entity"
    format="dropdown"
    :blacklist="['delete', 'refresh']"
    @action="handleAction"
  />
</template>
```

### Exemple 7 : Minimize (dans un panel)

```vue
<template>
  <EntityActions
    entity-type="spells"
    :entity="entity"
    format="buttons"
    :context="{ inPanel: true }"
    @action="handleAction"
  />
</template>
```

---

## 🔐 Permissions

Le système utilise automatiquement `usePermissions` pour filtrer les actions selon les permissions de l'utilisateur :

- **`canView`** → Actions `view`, `quick-view`
- **`canUpdate`** → Actions `edit`, `quick-edit`
- **`canDelete`** → Action `delete`
- **`canManage`** → Action `refresh`

Les permissions sont vérifiées via :
- `canViewAny(entityType)`
- `canUpdateAny(entityType)`
- `canDeleteAny(entityType)`
- `canManageAny(entityType)` ou `isAdmin`

---

## 🎨 Personnalisation

### Ajouter une nouvelle action

1. **Modifier `entity-actions-config.js`** :

```javascript
export const ENTITY_ACTIONS_COMMON = Object.freeze({
  // ... actions existantes ...
  'my-action': {
    key: 'my-action',
    label: 'Mon action',
    icon: 'fa-solid fa-star',
    permission: 'canUpdate', // ou null si toujours disponible
    requiresEntity: true,
    group: 'tools',
  },
});
```

2. **Gérer l'événement dans votre composant** :

```vue
<script setup>
const handleAction = (actionKey, entity) => {
  if (actionKey === 'my-action') {
    // Gérer l'action
  }
};
</script>
```

### Personnaliser les groupes

Les groupes sont définis dans `ACTION_GROUPS_ORDER` :

```javascript
export const ACTION_GROUPS_ORDER = Object.freeze([
  "navigation",
  "edition",
  "tools",
  "destructive",
]);
```

---

## 🐛 Dépannage

### Les actions ne s'affichent pas

1. Vérifier que `entityType` est correct (ex: 'spells', pas 'spell')
2. Vérifier les permissions avec `usePermissions`
3. Vérifier que `entity` est fourni si `requiresEntity: true`

### Le menu contextuel ne s'affiche pas

1. Vérifier que `contextVisible` est `true`
2. Vérifier que `contextPosition` est fourni avec `{ x, y }`
3. Utiliser `Teleport` pour afficher au-dessus de tout

### Les permissions ne fonctionnent pas

1. Vérifier que `usePermissions` est correctement configuré
2. Vérifier que les policies Laravel sont définies
3. Vérifier que les permissions sont exposées via Inertia props

---

## 📚 Références

- **Configuration** : `resources/js/Entities/entity-actions-config.js`
- **Composable** : `resources/js/Composables/entity/useEntityActions.js`
- **Composant principal** : `resources/js/Pages/Organismes/entity/EntityActions.vue`
- **Documentation technique** : `docs/100- Done/ENTITY_ACTIONS_SYSTEM_PROPOSAL.md`
- **État d'implémentation** : `docs/100- Done/ENTITY_ACTIONS_IMPLEMENTATION_STATUS.md`

---

## 🔄 Migration depuis `EntityActionsMenu`

Si vous utilisez encore `EntityActionsMenu` (legacy), voici comment migrer :

### Avant (legacy)

```vue
<EntityActionsMenu
  :entity="entity"
  entity-type="spells"
  :can-view="canView"
  :can-update="canUpdate"
  :can-delete="canDelete"
  :is-admin="isAdmin"
  @view="handleView"
  @edit="handleEdit"
/>
```

### Après (nouveau système)

```vue
<EntityActions
  entity-type="spells"
  :entity="entity"
  format="dropdown"
  display="icon-text"
  @action="handleAction"
/>

<script setup>
const handleAction = (actionKey, entity) => {
  switch (actionKey) {
    case 'view':
      handleView(entity);
      break;
    case 'edit':
      handleEdit(entity);
      break;
    // ...
  }
};
</script>
```

**Note** : `EntityActionsMenu` est toujours disponible comme wrapper de compatibilité, mais il est déprécié. Utilisez `EntityActions` directement pour les nouveaux composants.

---

## 🎨 Optimisations récentes (2026-01-06)

### Nom de l'entité dans les menus

Le nom de l'entité est maintenant affiché en haut des menus dropdown et contextuels pour améliorer l'UX :
- **Style discret** : Texte petit et grisé (`text-xs text-base-content/60`)
- **Bordure de séparation** : Pour une meilleure lisibilité
- **Truncate** : Le nom long est tronqué avec un tooltip

### Actions contextuelles intelligentes

Les actions s'adaptent automatiquement au contexte :
- **Dans une page** (`inPage: true`) : `view` et `quick-view` sont masqués
- **Dans un modal** (`inModal: true`) : `edit` est masqué, `expand` est visible
- **Labels dynamiques** : Les labels et tooltips changent selon le contexte

Voir `docs/100- Done/OPTIMISATIONS_UI_2026_01.md` pour plus de détails.

---
