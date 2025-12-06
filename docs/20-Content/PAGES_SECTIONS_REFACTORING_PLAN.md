# 🔄 Plan de Refactorisation - Pages et Sections

**Date** : 2025-01-27  
**Objectif** : Simplifier et réorganiser le système de pages et sections selon les nouvelles spécifications.

---

## 📋 Spécifications finales

### Pages
- ✅ Interface OK, à optimiser
- Création depuis liste
- Paramétrage via modal (titre, slug, permissions, menu, parent, état)
- Tableau avec hiérarchie et drag & drop

### Sections
- **Header** : Toujours visible, titre (input/text selon mode), icônes au hover
- **Modes** : Lecture/Écriture indépendants par section
- **Auto-save** : Privilégié, templates envoient data via composable
- **Templates** : Structure en dossier avec fichiers séparés + config
- **Suppression** : Via modal paramètres avec confirmation

---

## 🗂️ Structure de fichiers proposée

```
resources/js/Pages/Organismes/section/
├── PageRenderer.vue                    # Affiche la page et ses sections
├── SectionRenderer.vue                # Gère le header + bascule lecture/écriture
├── SectionHeader.vue                  # Header réutilisable (Molecule)
│
├── modals/
│   ├── EditPageModal.vue              # Paramétrage page (général + sections)
│   ├── CreatePageModal.vue            # Création page
│   ├── CreateSectionModal.vue         # Création section (choix template)
│   └── SectionParamsModal.vue         # Paramètres section (settings + suppression)
│
├── templates/
│   ├── index.js                       # Auto-discovery des templates
│   ├── text/
│   │   ├── config.js                  # Nom, description, icône
│   │   ├── SectionTextRead.vue         # Mode lecture
│   │   └── SectionTextEdit.vue        # Mode écriture
│   ├── image/
│   │   ├── config.js
│   │   ├── SectionImageRead.vue
│   │   └── SectionImageEdit.vue
│   ├── gallery/
│   │   ├── config.js
│   │   ├── SectionGalleryRead.vue
│   │   └── SectionGalleryEdit.vue
│   └── ...
│
└── composables/
    ├── useSectionMode.js              # Gestion des modes lecture/écriture
    ├── useSectionSave.js              # Auto-save des sections
    └── useSectionTemplates.js         # Découverte et chargement des templates
```

---

## 🏗️ Architecture proposée

### 1. SectionRenderer.vue

**Rôle** : Composant principal qui orchestre une section

**Structure** :
```vue
<template>
  <div class="section" @mouseenter="isHovered = true" @mouseleave="isHovered = false">
    <!-- Header toujours visible -->
    <SectionHeader
      :title="section.title"
      :is-editing="isEditing"
      :can-edit="canEdit"
      :is-hovered="isHovered"
      @update:title="handleTitleUpdate"
      @toggle-edit="toggleEditMode"
      @open-params="openParamsModal"
      @copy-link="copySectionLink"
    />
    
    <!-- Contenu selon le mode -->
    <component
      :is="currentTemplateComponent"
      :section="section"
      :mode="isEditing ? 'edit' : 'read'"
      @data-updated="handleDataUpdate"
    />
  </div>
</template>
```

**Logique** :
- Gère l'état `isEditing` (local, frontend uniquement)
- Charge le bon template (read/edit) selon le mode
- Utilise `useSectionMode` pour la logique de basculement
- Utilise `useSectionSave` pour l'auto-save

### 2. SectionHeader.vue (Molecule)

**Rôle** : Header réutilisable pour toutes les sections

**Props** :
- `title` : Titre de la section
- `isEditing` : Mode édition actif ?
- `canEdit` : Droits d'écriture ?
- `isHovered` : Hover actif ?

**Événements** :
- `update:title` : Mise à jour du titre
- `toggle-edit` : Basculer mode lecture/écriture
- `open-params` : Ouvrir modal paramètres
- `copy-link` : Copier le lien

**Structure** :
```vue
<template>
  <div class="section-header">
    <!-- Titre à gauche -->
    <div class="section-header__title">
      <input
        v-if="isEditing"
        v-model="localTitle"
        @blur="handleTitleBlur"
        class="input input-sm"
      />
      <h3 v-else>{{ title || 'Sans titre' }}</h3>
    </div>
    
    <!-- Icônes à droite (hover) -->
    <div v-if="isHovered" class="section-header__actions">
      <!-- Copier lien (toujours) -->
      <button @click="$emit('copy-link')">
        <Icon source="fa-solid fa-link" />
      </button>
      
      <!-- Basculer mode (si droits) -->
      <button v-if="canEdit" @click="$emit('toggle-edit')">
        <Icon :source="isEditing ? 'fa-solid fa-eye' : 'fa-solid fa-edit'" />
      </button>
      
      <!-- Paramètres (si droits) -->
      <button v-if="canEdit" @click="$emit('open-params')">
        <Icon source="fa-solid fa-gear" />
      </button>
    </div>
  </div>
</template>
```

### 3. Templates - Structure en dossier

**Chaque template = un dossier avec** :

#### `config.js`
```javascript
export default {
  name: 'Texte',
  description: 'Section de texte riche avec éditeur WYSIWYG',
  icon: 'fa-solid fa-font',
  value: 'text', // Identifiant unique
  supportsAutoSave: true, // Compatible auto-save ?
}
```

#### `SectionTextRead.vue`
```vue
<script setup>
const props = defineProps({
  section: Object,
  data: Object, // Données extraites de section.data
  settings: Object, // Données extraites de section.settings
});

// Affiche le contenu en mode lecture
</script>
```

#### `SectionTextEdit.vue`
```vue
<script setup>
import { useSectionSave } from '@/Pages/Organismes/section/composables/useSectionSave';

const props = defineProps({
  section: Object,
  data: Object,
  settings: Object,
});

const emit = defineEmits(['data-updated']);

const { saveSection } = useSectionSave();

// Éditeur WYSIWYG
const content = ref(props.data?.content || '');

// Auto-save avec debounce
watch(content, debounce((newContent) => {
  const newData = { ...props.data, content: newContent };
  saveSection(props.section.id, { data: newData });
  emit('data-updated', newData);
}, 1000));
</script>
```

### 4. Auto-discovery des templates

#### `templates/index.js`
```javascript
import { ref, computed } from 'vue';

// Import dynamique de tous les templates
const templateModules = import.meta.glob('./*/config.js', { eager: true });

export const availableTemplates = computed(() => {
  return Object.entries(templateModules).map(([path, module]) => {
    const config = module.default;
    return {
      ...config,
      // Charger les composants read/edit
      readComponent: () => import(`${path.replace('/config.js', '/Section' + config.name + 'Read.vue')}`),
      editComponent: () => import(`${path.replace('/config.js', '/Section' + config.name + 'Edit.vue')}`),
    };
  });
});

export function getTemplateByValue(value) {
  return availableTemplates.value.find(t => t.value === value);
}
```

### 5. Composables

#### `useSectionMode.js`
```javascript
import { ref } from 'vue';

// État global des sections en mode édition (par section ID)
const editingSections = ref(new Set());

export function useSectionMode(sectionId) {
  const isEditing = computed(() => editingSections.value.has(sectionId));
  
  const toggleEditMode = () => {
    if (isEditing.value) {
      editingSections.value.delete(sectionId);
    } else {
      editingSections.value.add(sectionId);
    }
  };
  
  const setEditMode = (value) => {
    if (value) {
      editingSections.value.add(sectionId);
    } else {
      editingSections.value.delete(sectionId);
    }
  };
  
  return {
    isEditing,
    toggleEditMode,
    setEditMode,
  };
}
```

#### `useSectionSave.js`
```javascript
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash-es';

const pendingSaves = new Map();

export function useSectionSave() {
  const saveSection = debounce((sectionId, updates) => {
    router.patch(route('sections.update', sectionId), {
      ...updates,
    }, {
      preserveScroll: true,
      only: ['page'], // Recharger uniquement la page
    });
  }, 500);
  
  const saveSectionImmediate = (sectionId, updates) => {
    router.patch(route('sections.update', sectionId), {
      ...updates,
    }, {
      preserveScroll: true,
      only: ['page'],
    });
  };
  
  return {
    saveSection,
    saveSectionImmediate,
  };
}
```

#### `useSectionTemplates.js`
```javascript
import { availableTemplates, getTemplateByValue } from '../templates';

export function useSectionTemplates() {
  const getTemplateConfig = (templateValue) => {
    return getTemplateByValue(templateValue);
  };
  
  const getTemplateComponent = async (templateValue, mode = 'read') => {
    const config = getTemplateByValue(templateValue);
    if (!config) return null;
    
    if (mode === 'read') {
      return await config.readComponent();
    } else {
      return await config.editComponent();
    }
  };
  
  return {
    availableTemplates,
    getTemplateConfig,
    getTemplateComponent,
  };
}
```

---

## 🔄 Flux de fonctionnement

### 1. Affichage d'une section

```
PageRenderer
  └── SectionRenderer (pour chaque section)
      ├── SectionHeader (toujours visible)
      └── Template Component (read ou edit selon mode)
```

### 2. Basculement mode édition

```
User hover → Icônes apparaissent
User clique "Éditer" → toggleEditMode()
  └── isEditing = true
  └── Template bascule de Read à Edit
  └── Titre bascule de texte à input
```

### 3. Auto-save

```
Template Edit détecte changement
  └── watch() déclenché
  └── debounce(500ms)
  └── useSectionSave.saveSection()
  └── PATCH /sections/{id}
  └── Rechargement page (only: ['page'])
```

### 4. Création section

```
User clique "Ajouter section"
  └── CreateSectionModal s'ouvre
  └── Liste des templates (auto-découverte)
  └── User choisit template
  └── Section créée
  └── Si template compatible auto-save → Mode édition direct
  └── Sinon → Redirection page édition
```

---

## ✅ Avantages de cette architecture

1. **Séparation claire** : Header / Contenu / Templates
2. **Réutilisabilité** : Header commun, templates isolés
3. **Extensibilité** : Ajouter un template = créer un dossier
4. **Auto-discovery** : Pas besoin de déclarer manuellement
5. **Auto-save** : Géré de manière uniforme
6. **Modes indépendants** : Chaque section gère son propre état
7. **Composables** : Logique réutilisable et testable

---

## 🚀 Plan d'implémentation

### Phase 1 : Structure de base
1. Créer la nouvelle structure de dossiers
2. Créer `SectionHeader.vue`
3. Créer les composables de base
4. Créer le système d'auto-discovery

### Phase 2 : Refactorisation SectionRenderer
1. Refactoriser `SectionRenderer.vue` avec le nouveau header
2. Implémenter le système de modes
3. Intégrer l'auto-save

### Phase 3 : Migration des templates
1. Migrer les templates existants vers la nouvelle structure
2. Séparer read/edit pour chaque template
3. Ajouter les config.js

### Phase 4 : Modals et optimisations
1. Optimiser les modals existants
2. Ajouter la suppression dans SectionParamsModal
3. Tests et ajustements

---

*Document créé le 2025-01-27*

