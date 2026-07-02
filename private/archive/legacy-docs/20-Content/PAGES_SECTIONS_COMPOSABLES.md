# Composables pour les Sections

## Vue d'ensemble

Les composables pour les sections fournissent une interface unifiée et réutilisable pour gérer les sections dans l'application. Ils suivent le pattern de composition de Vue 3 et centralisent la logique métier.

## Liste des composables

### 1. `useSectionAPI`
**Localisation** : `resources/js/Pages/Organismes/section/composables/useSectionAPI.js`

**Description** : Centralise tous les appels backend pour les sections.

**Méthodes disponibles** :
- `createSection(sectionData, options)` - Créer une section
- `updateSection(sectionId, updates, options)` - Mettre à jour une section
- `deleteSection(sectionId, options)` - Supprimer une section
- `reorderSections(sections, options)` - Réorganiser l'ordre
- `getSection(sectionId, options)` - Récupérer une section
- `restoreSection(sectionId, options)` - Restaurer une section
- `forceDeleteSection(sectionId, options)` - Supprimer définitivement
- `attachFile(sectionId, file, metadata, options)` - Ajouter un fichier
- `detachFile(sectionId, fileId, options)` - Supprimer un fichier

**Exemple** :
```javascript
import { useSectionAPI } from './composables/useSectionAPI';

const { createSection, updateSection } = useSectionAPI();

await createSection({
  page_id: 1,
  template: 'text',
  title: 'Ma section'
});
```

### 2. `useSectionSave`
**Localisation** : `resources/js/Pages/Organismes/section/composables/useSectionSave.js`

**Description** : Gère l'auto-save des sections avec debounce.

**Méthodes disponibles** :
- `saveSection(sectionId, updates, delay)` - Sauvegarder avec debounce (défaut: 500ms)
- `saveSectionImmediate(sectionId, updates)` - Sauvegarder immédiatement

**Exemple** :
```javascript
import { useSectionSave } from './composables/useSectionSave';

const { saveSection } = useSectionSave();

// Auto-save avec debounce de 500ms
saveSection(sectionId, { data: { content: 'Nouveau contenu' } });

// Auto-save avec debounce personnalisé
saveSection(sectionId, { data: { content: 'Nouveau contenu' } }, 1000);
```

### 3. `useSectionMode`
**Localisation** : `resources/js/Pages/Organismes/section/composables/useSectionMode.js`

**Description** : Gère les modes lecture/écriture des sections.

**Méthodes disponibles** :
- `isEditing` (computed) - État du mode édition
- `toggleEditMode()` - Basculer le mode
- `setEditMode(value)` - Définir explicitement le mode

**Exemple** :
```javascript
import { useSectionMode } from './composables/useSectionMode';

const sectionId = computed(() => props.section.id);
const { isEditing, toggleEditMode, setEditMode } = useSectionMode(sectionId);

// Activer le mode édition
setEditMode(true);

// Basculer le mode
toggleEditMode();
```

### 4. `useSectionDefaults`
**Localisation** : `resources/js/Pages/Organismes/section/composables/useSectionDefaults.js`

**Description** : Fournit les valeurs par défaut pour les sections selon leur template.

**Méthodes disponibles** :
- `getDefaultSettings(template)` - Settings par défaut
- `getDefaultData(template)` - Data par défaut
- `getDefaults(template)` - Settings + Data

**Exemple** :
```javascript
import { useSectionDefaults } from './composables/useSectionDefaults';

const { getDefaults } = useSectionDefaults();

const defaults = getDefaults('text');
// { settings: { align: 'left', size: 'md' }, data: { content: '' } }
```

### 5. `useSectionTemplates`
**Localisation** : `resources/js/Pages/Organismes/section/composables/useSectionTemplates.js`

**Description** : Gère le chargement dynamique des templates de sections.

**Méthodes disponibles** :
- `getTemplateComponent(template, mode)` - Charger un template (read/edit)

**Exemple** :
```javascript
import { useSectionTemplates } from './composables/useSectionTemplates';

const { getTemplateComponent } = useSectionTemplates();

const component = await getTemplateComponent('text', 'read');
```

### 6. `useSectionUI`
**Localisation** : `resources/js/Pages/Organismes/section/composables/useSectionUI.js`

**Description** : Interface unifiée combinant mapper + adapter pour l'UI.

**Propriétés disponibles** :
- `sectionModel` (computed) - Section normalisée
- `uiData` (computed) - Données UI adaptées
- `status` (computed) - Statut combiné
- `canEdit` (computed) - Permission d'édition
- `canDelete` (computed) - Permission de suppression
- `templateInfo` (computed) - Infos du template { value, icon, label }
- `stateInfo` (computed) - Infos d'état { value, badge, color, label }
- `visibilityInfo` (computed) - Infos de visibilité
- `editRoleInfo` (computed) - Infos de rôle d'édition

**Exemple** :
```javascript
import { useSectionUI } from './composables/useSectionUI';

const { 
  sectionModel, 
  canEdit, 
  templateInfo,
  stateInfo,
  uiData 
} = useSectionUI(() => props.section);

// Utilisation dans le template
<Badge :color="stateInfo.badge.color">{{ stateInfo.badge.text }}</Badge>
<Icon :source="templateInfo.icon" />
```

### 7. `useSectionStyles`
**Localisation** : `resources/js/Pages/Organismes/section/composables/useSectionStyles.js`

**Description** : Génère les classes CSS dynamiques selon les settings d'une section.

**Propriétés disponibles** :
- `alignClasses` (computed) - Classes d'alignement
- `sizeClasses` (computed) - Classes de taille de texte
- `imageSizeClasses` (computed) - Classes de taille d'image
- `galleryColumnsClasses` (computed) - Classes de colonnes de galerie
- `galleryGapClasses` (computed) - Classes d'espacement de galerie
- `customClasses` (computed) - Classes CSS personnalisées
- `containerClasses` (computed) - Classes combinées pour conteneur
- `galleryClasses` (computed) - Classes combinées pour galerie
- `imageClasses` (computed) - Classes combinées pour image

**Exemple** :
```javascript
import { useSectionStyles } from './composables/useSectionStyles';

const { containerClasses, galleryClasses } = useSectionStyles(() => props.settings);

// Utilisation dans le template
<div :class="containerClasses">...</div>
<div :class="galleryClasses">...</div>
```

## Utilisation combinée

### Exemple complet dans un template

```vue
<script setup>
import { computed } from 'vue';
import { useSectionUI } from '../../composables/useSectionUI';
import { useSectionStyles } from '../../composables/useSectionStyles';
import { useSectionSave } from '../../composables/useSectionSave';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

// UI unifiée
const { sectionModel, canEdit, templateInfo, stateInfo } = useSectionUI(() => props.section);

// Styles dynamiques
const { containerClasses } = useSectionStyles(() => props.settings);

// Auto-save
const { saveSection } = useSectionSave();
</script>

<template>
  <div :class="containerClasses">
    <Badge :color="stateInfo.badge.color">
      {{ stateInfo.badge.text }}
    </Badge>
    <Icon :source="templateInfo.icon" />
    <!-- Contenu -->
  </div>
</template>
```

## Bonnes pratiques

1. **Toujours utiliser `useSectionUI`** pour obtenir les données normalisées
2. **Utiliser `useSectionStyles`** pour les classes CSS dynamiques
3. **Utiliser `useSectionSave`** pour l'auto-save dans les templates Edit
4. **Utiliser `useSectionAPI`** pour toutes les opérations backend
5. **Utiliser `useSectionDefaults`** pour les valeurs par défaut lors de la création

## Migration depuis l'ancienne approche

### Avant
```javascript
const align = props.settings?.align || 'left';
const alignClasses = {
  'left': 'text-left',
  'center': 'text-center',
  'right': 'text-right'
}[align] || 'text-left';
```

### Après
```javascript
const { alignClasses } = useSectionStyles(() => props.settings);
```

## Support

Pour plus d'informations :
- `docs/20-Content/PAGES_SECTIONS_ARCHITECTURE.md` - Architecture complète

