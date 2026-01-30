# Guide de migration vers la nouvelle architecture

## Vue d'ensemble

Ce guide explique comment migrer les composants existants vers la nouvelle architecture avec les mappers, adapters et composables UI.

## Composants migrés

### ✅ SectionRenderer
- Utilise maintenant `useSectionUI` pour obtenir les données normalisées et UI
- Accès simplifié aux permissions, template info, state info
- Classes CSS automatiques selon l'état et le template

### ✅ PageSectionEditor
- Utilise `mapToSectionModels` pour normaliser les sections
- Affiche les badges d'état avec les couleurs appropriées
- Affiche les icônes de template

## Migration étape par étape

### Étape 1 : Importer les nouveaux composables

```javascript
// Avant
import { Section } from '@/Models';

// Après
import { useSectionUI } from './composables/useSectionUI';
import { mapToSectionModels } from './mappers/sectionMapper';
```

### Étape 2 : Remplacer l'accès direct aux données

```javascript
// Avant
const sectionModel = computed(() => {
    if (!props.section) return null;
    return new Section(props.section);
});

const canEdit = computed(() => {
    if (!sectionModel.value) return false;
    return sectionModel.value.canUpdate;
});

const template = computed(() => {
    return props.section.template || props.section.type || 'text';
});

// Après
const { 
    sectionModel, 
    canEdit, 
    templateInfo,
    stateInfo,
    uiData 
} = useSectionUI(() => props.section);

const template = computed(() => templateInfo.value.value);
```

### Étape 3 : Utiliser les données UI adaptées

```vue
<!-- Avant -->
<div class="section-container">
    <span>{{ section.state }}</span>
    <i :class="getIcon(section.template)"></i>
</div>

<!-- Après -->
<div :class="uiData.containerClass">
    <Badge :color="stateInfo.badge.color">
        {{ stateInfo.badge.text }}
    </Badge>
    <Icon :source="templateInfo.icon" />
</div>
```

### Étape 4 : Utiliser les informations structurées

```javascript
// Avant
const getStateColor = (state) => {
    const colors = {
        'draft': 'warning',
        'playable': 'success',
        // ...
    };
    return colors[state] || 'neutral';
};

// Après
const { stateInfo } = useSectionUI(props.section);
// stateInfo.value.color est déjà calculé
// stateInfo.value.badge contient { text, color, variant }
```

## Exemples de migration

### Exemple 1 : Liste de sections

```javascript
// Avant
const sections = computed(() => props.sections);

// Après
import { mapToSectionModels } from './mappers/sectionMapper';
const sectionModels = computed(() => mapToSectionModels(props.sections));
```

### Exemple 2 : Affichage avec permissions

```vue
<!-- Avant -->
<template>
    <div v-if="section.can?.update">
        <Btn @click="edit">Éditer</Btn>
    </div>
</template>

<script setup>
const canEdit = computed(() => props.section.can?.update || false);
</script>

<!-- Après -->
<template>
    <div v-if="canEdit">
        <Btn @click="edit">Éditer</Btn>
    </div>
</template>

<script setup>
const { canEdit } = useSectionUI(() => props.section);
</script>
```

### Exemple 3 : Badge d'état

```vue
<!-- Avant -->
<template>
    <span :class="`badge badge-${getStateColor(section.state)}`">
        {{ getStateLabel(section.state) }}
    </span>
</template>

<script setup>
function getStateColor(state) {
    // Logique complexe...
}

function getStateLabel(state) {
    // Logique complexe...
}
</script>

<!-- Après -->
<template>
    <span 
        :class="[
            'badge',
            `badge-${stateInfo.badge.color}`,
            `badge-${stateInfo.badge.variant}`
        ]"
    >
        {{ stateInfo.badge.text }}
    </span>
</template>

<script setup>
const { stateInfo } = useSectionUI(() => props.section);
</script>
```

## Avantages de la migration

1. **Code plus propre** : Moins de logique dupliquée
2. **Cohérence** : Tous les composants utilisent les mêmes transformations
3. **Maintenabilité** : Modifications centralisées dans les adapters
4. **Type safety** : Modèles normalisés avec propriétés garanties
5. **Performance** : Computed properties réactives optimisées

## Checklist de migration

Pour chaque composant utilisant des sections :

- [ ] Importer `useSectionUI` ou `mapToSectionModels`
- [ ] Remplacer l'accès direct à `props.section.*`
- [ ] Utiliser `sectionModel` au lieu de `props.section`
- [ ] Utiliser `canEdit`, `canDelete` du composable
- [ ] Utiliser `templateInfo`, `stateInfo`, `visibilityInfo`
- [ ] Utiliser `uiData.containerClass` pour les classes CSS
- [ ] Remplacer les fonctions de transformation par les adapters
- [ ] Tester le composant après migration

## Composants restants à migrer

- [ ] Templates de sections (Read/Edit)
- [ ] Modals de sections
- [ ] Autres composants utilisant des sections

## Support

Pour toute question sur la migration, consulter :
- `docs/20-Content/PAGES_SECTIONS_ARCHITECTURE.md` - Architecture complète
- `resources/js/Pages/Organismes/section/composables/useSectionUI.js` - Composable principal
- `resources/js/Pages/Organismes/section/adapters/sectionUIAdapter.js` - Adapter UI

