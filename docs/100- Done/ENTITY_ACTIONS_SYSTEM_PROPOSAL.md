# Proposition — Système d'Actions pour les Entités

**Date** : 2026-01-06  
**Statut** : 📋 Proposition

---

## 📋 Résumé de la demande

### Actions disponibles par entité (selon permissions)

1. **Ouvrir dans une modal** (rapide) — `quick-view`
2. **Ouvrir dans une page** — `view`
3. **Modifier dans une modal** (rapide) — `quick-edit` (si permission)
4. **Modifier dans une page** — `edit` (si permission)
5. **Copier l'URL de l'entité** — `copy-link`
6. **Rafraîchir les données** (via scrapping) — `refresh` (si permission)
7. **Télécharger en PDF** — `download-pdf`
8. **Minimiser** — `minimize` (nouveau)
9. **Supprimer** — `delete` (si permission)

### Composant flexible

**Paramètres** :
- **Filtrage** : `whitelist` ou `blacklist` d'actions à afficher
- **Format** : `buttons` (liste horizontale) ou `dropdown` (menu vertical)
- **Affichage** : `icon-only` ou `icon-text` (icône + texte)
- **Retour** : Liste de boutons, menu vertical, ou dropdown selon les bonnes pratiques

### Emplacements d'affichage

1. **En haut de chaque entité** (vues Compact/Minimal/Large)
   - Compact/Minimal : icônes seulement
   - Large : icônes + texte

2. **Dans les tableaux d'entités**
   - **Colonne "Actions"** (dropdown) : icône de menu, placée après le checkbox par défaut, pas de nom de colonne
   - **Menu contextuel** (clic droit) : dropdown au clic droit sur la ligne

---

## 🔍 Analyse de l'existant

### Composant existant : `EntityActionsMenu.vue`

**Localisation** : `resources/js/Pages/Organismes/entity/EntityActionsMenu.vue`

**Fonctionnalités actuelles** :
- ✅ Menu dropdown avec actions
- ✅ Gestion des permissions (`canView`, `canUpdate`, `canDelete`)
- ✅ Actions : view, quick-view, edit, quick-edit, copy-link, download-pdf, refresh, delete
- ✅ Émission d'événements pour chaque action
- ✅ Support des routes via `entityRouteRegistry`

**Limitations** :
- ❌ Format fixe (dropdown uniquement)
- ❌ Pas de filtrage (whitelist/blacklist)
- ❌ Pas de mode icône seule / icône + texte
- ❌ Pas de mode "liste de boutons"
- ❌ Pas d'intégration dans les vues entités (Compact/Minimal/Large)
- ❌ Pas d'intégration dans les tableaux (colonne actions, clic droit)

---

## 🏗️ Architecture proposée

### Structure Atomic Design

```
Atoms/
  └── action/
      └── EntityActionButton.vue        # Bouton d'action unique (icône ou icône+texte)

Molecules/
  └── entity/
      └── EntityActionsList.vue        # Liste horizontale de boutons
      └── EntityActionsMenu.vue        # Menu vertical (dropdown)

Organisms/
  └── entity/
      └── EntityActions.vue            # Composant principal flexible
```

### Composables

```
Composables/
  └── entity/
      └── useEntityActions.js          # Logique métier (permissions, filtrage, format)
```

### Configuration

```
Entities/
  └── entity-actions-config.js        # Configuration des actions par entité
```

---

## 📐 Architecture détaillée

### 1. Configuration des actions (`entity-actions-config.js`)

```javascript
/**
 * Configuration des actions disponibles pour chaque type d'entité.
 * 
 * @description
 * Définit les actions possibles, leurs permissions, icônes, labels, etc.
 */

export const ENTITY_ACTIONS_CONFIG = {
  // Actions communes à toutes les entités
  common: {
    view: {
      key: 'view',
      label: 'Ouvrir (page)',
      icon: 'fa-solid fa-eye',
      permission: 'canView',
      requiresEntity: true,
    },
    'quick-view': {
      key: 'quick-view',
      label: 'Ouvrir rapide',
      icon: 'fa-solid fa-window-maximize',
      permission: 'canView',
      requiresEntity: true,
    },
    edit: {
      key: 'edit',
      label: 'Modifier (page)',
      icon: 'fa-solid fa-pen',
      permission: 'canUpdate',
      requiresEntity: true,
    },
    'quick-edit': {
      key: 'quick-edit',
      label: 'Modifier rapide',
      icon: 'fa-solid fa-bolt',
      permission: 'canUpdate',
      requiresEntity: true,
    },
    'copy-link': {
      key: 'copy-link',
      label: 'Copier le lien',
      icon: 'fa-solid fa-link',
      permission: null, // Toujours disponible
      requiresEntity: true,
    },
    'download-pdf': {
      key: 'download-pdf',
      label: 'Télécharger PDF',
      icon: 'fa-solid fa-file-pdf',
      permission: null, // Toujours disponible
      requiresEntity: true,
    },
    refresh: {
      key: 'refresh',
      label: 'Rafraîchir',
      icon: 'fa-solid fa-arrow-rotate-right',
      permission: 'canManage', // Admin/maintenance
      requiresEntity: true,
    },
    minimize: {
      key: 'minimize',
      label: 'Minimiser',
      icon: 'fa-solid fa-window-minimize',
      permission: null, // Toujours disponible
      requiresEntity: false, // Peut être utilisé sans entité
    },
    delete: {
      key: 'delete',
      label: 'Supprimer',
      icon: 'fa-solid fa-trash',
      permission: 'canDelete',
      requiresEntity: true,
      variant: 'error', // Style spécial pour action destructive
    },
  },
  
  // Actions spécifiques par entité (exemple)
  resource: {
    // Actions spécifiques aux ressources
  },
};
```

### 2. Composable `useEntityActions.js`

```javascript
/**
 * Composable pour gérer les actions d'entité.
 * 
 * @description
 * - Filtre les actions selon les permissions
 * - Gère le filtrage (whitelist/blacklist)
 * - Retourne les actions disponibles formatées
 */

import { computed } from 'vue';
import { ENTITY_ACTIONS_CONFIG } from '@/Entities/entity-actions-config';
import { usePermissions } from '@/Composables/permissions/usePermissions';

export function useEntityActions(entityType, entity = null, options = {}) {
  const { can } = usePermissions();
  
  const {
    whitelist = null,      // Liste d'actions à inclure
    blacklist = null,      // Liste d'actions à exclure
    capabilities = null,   // Permissions spécifiques (override)
  } = options;
  
  // Récupère la config des actions pour ce type d'entité
  const actionsConfig = computed(() => {
    const common = ENTITY_ACTIONS_CONFIG.common || {};
    const specific = ENTITY_ACTIONS_CONFIG[entityType] || {};
    return { ...common, ...specific };
  });
  
  // Filtre les actions selon les permissions et les options
  const availableActions = computed(() => {
    const config = actionsConfig.value;
    const actions = Object.values(config);
    
    return actions.filter(action => {
      // Whitelist : n'inclure que les actions listées
      if (whitelist && !whitelist.includes(action.key)) {
        return false;
      }
      
      // Blacklist : exclure les actions listées
      if (blacklist && blacklist.includes(action.key)) {
        return false;
      }
      
      // Vérifier si l'entité est requise
      if (action.requiresEntity && !entity) {
        return false;
      }
      
      // Vérifier les permissions
      if (action.permission) {
        const canAction = capabilities?.[action.permission] ?? 
                         can(entityType, action.permission);
        if (!canAction) {
          return false;
        }
      }
      
      return true;
    });
  });
  
  return {
    availableActions,
    actionsConfig,
  };
}
```

### 3. Composant principal `EntityActions.vue`

```vue
<script setup>
/**
 * EntityActions Organism
 * 
 * @description
 * Composant flexible pour afficher les actions d'une entité.
 * Supporte différents formats : boutons, dropdown, menu contextuel.
 */

import { computed } from 'vue';
import { useEntityActions } from '@/Composables/entity/useEntityActions';
import EntityActionsList from '@/Pages/Molecules/entity/EntityActionsList.vue';
import EntityActionsMenu from '@/Pages/Molecules/entity/EntityActionsMenu.vue';

const props = defineProps({
  entityType: { type: String, required: true },
  entity: { type: Object, default: null },
  
  // Format d'affichage
  format: { 
    type: String, 
    default: 'dropdown', 
    validator: (v) => ['buttons', 'dropdown', 'menu'].includes(v) 
  },
  
  // Mode d'affichage
  display: { 
    type: String, 
    default: 'icon-text', 
    validator: (v) => ['icon-only', 'icon-text'].includes(v) 
  },
  
  // Filtrage
  whitelist: { type: Array, default: null },
  blacklist: { type: Array, default: null },
  
  // Permissions (override)
  capabilities: { type: Object, default: null },
  
  // Options UI
  size: { type: String, default: 'sm' },
  color: { type: String, default: 'primary' },
  placement: { type: String, default: 'bottom-end' }, // Pour dropdown
});

const emit = defineEmits([
  'action',        // Émis pour chaque action (action, entity)
  'view',
  'quick-view',
  'edit',
  'quick-edit',
  'copy-link',
  'download-pdf',
  'refresh',
  'minimize',
  'delete',
]);

const { availableActions } = useEntityActions(
  props.entityType, 
  props.entity, 
  {
    whitelist: props.whitelist,
    blacklist: props.blacklist,
    capabilities: props.capabilities,
  }
);

const handleAction = (actionKey) => {
  emit('action', actionKey, props.entity);
  emit(actionKey, props.entity);
};
</script>

<template>
  <!-- Format : liste de boutons -->
  <EntityActionsList
    v-if="format === 'buttons'"
    :actions="availableActions"
    :display="display"
    :size="size"
    :color="color"
    @action="handleAction"
  />
  
  <!-- Format : dropdown -->
  <EntityActionsMenu
    v-else-if="format === 'dropdown'"
    :actions="availableActions"
    :display="display"
    :size="size"
    :color="color"
    :placement="placement"
    @action="handleAction"
  />
  
  <!-- Format : menu (pour clic droit) -->
  <EntityActionsMenu
    v-else
    :actions="availableActions"
    :display="display"
    :size="size"
    :color="color"
    variant="context"
    @action="handleAction"
  />
</template>
```

### 4. Molecule `EntityActionsList.vue`

```vue
<script setup>
/**
 * EntityActionsList Molecule
 * 
 * @description
 * Liste horizontale de boutons d'actions.
 */

import EntityActionButton from '@/Pages/Atoms/action/EntityActionButton.vue';

const props = defineProps({
  actions: { type: Array, required: true },
  display: { type: String, default: 'icon-text' },
  size: { type: String, default: 'sm' },
  color: { type: String, default: 'primary' },
});

const emit = defineEmits(['action']);

const handleAction = (actionKey) => {
  emit('action', actionKey);
};
</script>

<template>
  <div class="flex items-center gap-2">
    <EntityActionButton
      v-for="action in actions"
      :key="action.key"
      :action="action"
      :display="display"
      :size="size"
      :color="color"
      @click="handleAction(action.key)"
    />
  </div>
</template>
```

### 5. Atom `EntityActionButton.vue`

```vue
<script setup>
/**
 * EntityActionButton Atom
 * 
 * @description
 * Bouton d'action unique (icône seule ou icône + texte).
 */

import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';

const props = defineProps({
  action: { type: Object, required: true },
  display: { type: String, default: 'icon-text' },
  size: { type: String, default: 'sm' },
  color: { type: String, default: 'primary' },
});

const showIcon = computed(() => props.display === 'icon-only' || props.display === 'icon-text');
const showText = computed(() => props.display === 'icon-text');
</script>

<template>
  <Btn
    :size="size"
    :variant="action.variant || 'ghost'"
    :color="action.variant === 'error' ? 'error' : color"
    :title="showIcon && !showText ? action.label : null"
    class="gap-2"
  >
    <Icon 
      v-if="showIcon"
      :source="action.icon" 
      :alt="action.label" 
      :size="size" 
    />
    <span v-if="showText">{{ action.label }}</span>
  </Btn>
</template>
```

---

## 🔌 Intégrations

### 1. Dans les vues entités (Compact/Minimal/Large)

```vue
<!-- EntityViewCompact.vue / EntityViewMinimal.vue -->
<EntityActions
  entity-type="spells"
  :entity="entity"
  format="buttons"
  display="icon-only"
  :capabilities="capabilities"
  @view="handleView"
  @quick-view="handleQuickView"
  @edit="handleEdit"
  @quick-edit="handleQuickEdit"
  @copy-link="handleCopyLink"
  @download-pdf="handleDownloadPdf"
  @refresh="handleRefresh"
  @delete="handleDelete"
/>
```

```vue
<!-- EntityViewLarge.vue -->
<EntityActions
  entity-type="spells"
  :entity="entity"
  format="buttons"
  display="icon-text"
  :capabilities="capabilities"
  @view="handleView"
  @quick-view="handleQuickView"
  @edit="handleEdit"
  @quick-edit="handleQuickEdit"
  @copy-link="handleCopyLink"
  @download-pdf="handleDownloadPdf"
  @refresh="handleRefresh"
  @delete="handleDelete"
/>
```

### 2. Dans les tableaux (colonne Actions)

```vue
<!-- Dans TanStackTable.vue ou EntityTanStackTable.vue -->
<template #actions="{ row }">
  <EntityActions
    :entity-type="entityType"
    :entity="row.rowParams.entity"
    format="dropdown"
    display="icon-text"
    :capabilities="capabilities"
    @action="handleTableAction"
  />
</template>
```

### 3. Menu contextuel (clic droit)

```vue
<!-- Dans TanStackTableRow.vue -->
<div 
  @contextmenu.prevent="showContextMenu($event, row)"
  class="context-menu-trigger"
>
  <!-- ... contenu de la ligne ... -->
</div>

<!-- Menu contextuel -->
<EntityActions
  v-if="contextMenuVisible"
  :entity-type="entityType"
  :entity="contextMenuEntity"
  format="menu"
  display="icon-text"
    :style="{ position: 'fixed', left: contextMenuX + 'px', top: contextMenuY + 'px' }"
  @action="handleContextAction"
/>
```

---

## 📋 Plan d'implémentation

### Phase 1 : Structure de base (2-3h)
1. ✅ Créer `entity-actions-config.js`
2. ✅ Créer `useEntityActions.js` composable
3. ✅ Créer `EntityActionButton.vue` (Atom)
4. ✅ Créer `EntityActionsList.vue` (Molecule)
5. ✅ Refactoriser `EntityActionsMenu.vue` (Molecule)
6. ✅ Créer `EntityActions.vue` (Organism)

### Phase 2 : Intégration vues entités (1-2h)
1. ✅ Intégrer dans `EntityViewCompact.vue`
2. ✅ Intégrer dans `EntityViewMinimal.vue`
3. ✅ Intégrer dans `EntityViewLarge.vue`

### Phase 3 : Intégration tableaux (2-3h)
1. ✅ Ajouter colonne "Actions" dans `TanStackTable.vue`
2. ✅ Intégrer `EntityActions` dans la colonne
3. ✅ Implémenter menu contextuel (clic droit)

### Phase 4 : Tests et documentation (1-2h)
1. ✅ Tests unitaires pour `useEntityActions`
2. ✅ Tests d'intégration pour les composants
3. ✅ Documentation

**Total estimé** : 6-10h

---

## 🎯 Avantages de cette architecture

1. **Flexibilité** : Un seul composant pour tous les cas d'usage
2. **Réutilisabilité** : Configuration centralisée, logique partagée
3. **Maintenabilité** : Atomic Design, séparation des responsabilités
4. **Extensibilité** : Facile d'ajouter de nouvelles actions
5. **Cohérence** : Même système partout dans l'application

---

## ❓ Questions à valider

1. **Action "minimize"** : Quel comportement exact ? (réduire la vue, fermer un panneau, etc.)
2. **Menu contextuel** : Préférence pour un composant dédié ou réutiliser le dropdown ?
3. **Permissions** : Utiliser `usePermissions` existant ou passer les capabilities en props ?
4. **Routes** : Utiliser `entityRouteRegistry` existant ou créer un nouveau système ?

---

## 📚 Références

- Composant existant : `resources/js/Pages/Organismes/entity/EntityActionsMenu.vue`
- Permissions : `resources/js/Composables/permissions/usePermissions.js`
- Routes : `resources/js/Composables/entity/entityRouteRegistry.js`
- Atomic Design : `docs/30-UI/ATOMIC_DESIGN.md`

