# Système de Style des Inputs

## Vue d'ensemble

Le système de style des inputs est un système unifié qui permet de gérer les styles de tous les types d'input de manière cohérente et extensible.

## Architecture

### Composants impliqués

```
InputField (Molecule)
├── InputCore (Atom) - Styles d'input
├── InputLabel (Atom) - Styles de label
├── Helper (Atom) - Styles de helper
└── Validator (Atom) - Styles de validation
```

### Système de configuration par tableau

Le système utilise un tableau de configuration `STYLE_CONFIG` qui définit les classes CSS et animations pour chaque type d'input et variant :

```javascript
export const STYLE_CONFIG = {
    text: {
        glass: {
            classes: ['input', 'bg-transparent', 'border', 'border-gray-300', 'focus:border-primary', 'focus:ring-2', 'focus:ring-primary/20'],
            animations: ['hover:scale-105', 'focus:scale-102', 'transition-all', 'duration-200']
        },
        dash: {
            classes: ['input', 'border-dashed', 'border-2', 'bg-gray-50', 'hover:bg-gray-100', 'focus:bg-white'],
            animations: ['hover:scale-105', 'focus:scale-102', 'transition-all', 'duration-200']
        },
        // ... autres variants
    },
    // ... autres types d'input
};
```

**Avantages :**
- **Classes en toutes lettres** : Pas de génération dynamique pour la compatibilité Tailwind
- **Configuration centralisée** : Toutes les classes dans un seul endroit
- **Animations spécifiques** : Animations différentes selon le type d'input
- **Extensibilité** : Ajout facile de nouveaux types/variants

## Fonction de Fusion Intelligente

### Vue d'ensemble

La fonction `mergeStyleConfig()` permet de fusionner intelligemment des configurations de style en reconnaissant automatiquement les types de propriétés et en complétant avec les valeurs par défaut.

### Fonctionnalités

- **Reconnaissance intelligente** : Reconnaît automatiquement les types de propriétés (couleur, taille, variant, animation)
- **Fusion intelligente** : Fusionne les configurations avec les valeurs par défaut
- **API flexible** : Supporte les strings simples et les objets complexes

### Utilisation

#### 1. Fusion avec string (couleur)

```javascript
import { mergeStyleConfig } from '@/Composables/form/useInputStyle';

const config = mergeStyleConfig(
  { variant: 'glass', color: 'primary' },
  'success'
);
// Résultat: { variant: 'glass', color: 'success', size: 'md', animation: true }
```

#### 2. Fusion avec objet

```javascript
const config = mergeStyleConfig(
  { variant: 'glass', color: 'primary' },
  { color: 'success', size: 'lg' }
);
// Résultat: { variant: 'glass', color: 'success', size: 'lg', animation: true }
```

#### 3. Fusion avec configuration complète

```javascript
const config = mergeStyleConfig(
  { variant: 'glass', color: 'primary' },
  { 
    variant: 'outline', 
    color: 'success', 
    size: 'lg', 
    animation: false 
  }
);
// Résultat: { variant: 'outline', color: 'success', size: 'lg', animation: false }
```

### Reconnaissance automatique des propriétés

La fonction reconnaît automatiquement :

- **Strings** : Interprétées comme des couleurs
- **Variant** : `variant` ou valeurs dans `VARIANTS`
- **Taille** : `size` ou valeurs dans `SIZES`
- **Couleur** : `color`, valeurs dans `COLORS`, ou classes commençant par `color-`/`bg-`
- **Animation** : `animation`, booléens, ou strings

### Valeurs par défaut

Si aucune valeur n'est fournie, la fonction utilise :

```javascript
const defaults = { 
  variant: 'glass', 
  size: 'md', 
  color: 'primary', 
  animation: true 
};
```

## Système d'Actions Contextuelles

### Vue d'ensemble

Le système d'actions contextuelles permet d'ajouter des boutons d'action directement dans les champs de saisie (reset, clear, copy, toggle password, etc.).

### Configuration des actions

Chaque action est configurée dans le tableau `ACTIONS_CONFIGURATION` :

```javascript
const ACTIONS_CONFIGURATION = {
  reset: {
    compatibility: ['input', 'textarea', 'select', 'file', 'range', 'rating', 'checkbox', 'radio', 'toggle', 'filter'],
    options: {
      delay: 1000, // délai avant de pouvoir refaire l'action
      autofocus: false, // autofocus sur le champ
      destroy: false, // détruire l'action après l'utilisation
      notify: false, // notifier l'utilisateur après l'utilisation
      confirm: false, // demander confirmation avant l'action
      confirmMessage: 'Êtes-vous sûr de vouloir réinitialiser ce champ ?',
    },
    icon: 'fa-solid fa-arrow-rotate-left',
    size: 'auto', // dépend de l'input
    color: "neutral",
    variant: "ghost",
    ariaLabel: 'Réinitialiser',
    tooltip: 'Revenir à la valeur initiale',
    actionKey: 'reset',
  },
  // ... autres actions
};
```

### Actions disponibles

| Action | Compatibilité | Description | Options par défaut |
|--------|---------------|-------------|-------------------|
| `reset` | Tous sauf password | Réinitialiser à la valeur initiale | delay: 1000ms, confirm: false |
| `back` | Tous sauf password | Annuler la dernière modification | delay: 500ms, confirm: false |
| `clear` | Input, textarea, select, file, range, rating | Vider le champ | delay: 1000ms, confirm: false |
| `copy` | Input, textarea, select, file, range, rating | Copier le contenu | delay: 1000ms, notify: true |
| `password` | Password uniquement | Afficher/masquer le mot de passe | delay: 100ms, autofocus: false |
| `edit` | Tous | Basculer édition/lecture seule | delay: 100ms, autofocus: true |
| `lock` | Tous | Activer/désactiver le champ | delay: 100ms, autofocus: true |

### Utilisation

#### Actions simples
```vue
<InputField
  v-model="text"
  label="Avec actions"
  :actions="['copy', 'clear']"
/>
```

#### Actions avec options personnalisées
```vue
<InputField
  v-model="text"
  label="Avec options personnalisées"
  :actions="[
    { key: 'reset', color: 'warning', confirm: true },
    { key: 'copy', notify: { message: 'Copié !' } }
  ]"
/>
```

#### Actions avec options globales
```vue
<InputField
  v-model="text"
  label="Avec options globales"
  :actions="['reset', 'copy']"
  :actionOptions="{
    reset: { confirm: true, confirmMessage: 'Vraiment réinitialiser ?' },
    copy: { notify: { type: 'info' } }
  }"
/>
```

### Options disponibles

#### Options de base
- `delay` : Délai avant de pouvoir refaire l'action (ms)
- `autofocus` : Autofocus sur le champ après l'action
- `destroy` : Détruire l'action après l'utilisation
- `notify` : Configuration de notification
- `confirm` : Demander confirmation avant l'action
- `confirmMessage` : Message de confirmation

#### Configuration de notification
```javascript
notify: {
  message: 'Message à afficher',
  type: 'success', // success, error, info, warning
  icon: 'fa-solid fa-check',
  duration: 2000, // ms
}
```

#### Personnalisation des boutons
```javascript
{
  key: 'reset',
  color: 'warning', // Couleur du bouton
  variant: 'outline', // Variant du bouton
  size: 'sm', // Taille du bouton
  confirm: true, // Demander confirmation
}
```

### API JavaScript

```javascript
import useInputActions from '@/Composables/form/useInputActions';

const {
  actionsToDisplay, // Actions à afficher
  inputProps, // Props pour l'input
  isActionCompatible, // Vérifier la compatibilité
  mergeActionOptions, // Fusionner les options
  getDynamicActionProps, // Obtenir les props dynamiques
} = useInputActions({
  modelValue: 'valeur',
  type: 'text',
  actions: ['copy', 'clear'],
  actionOptions: {
    copy: { notify: { message: 'Copié !' } }
  }
});
```

### Intégration avec InputField

Les actions sont automatiquement affichées dans `InputField` via les slots `overEnd` :

```vue
<template>
  <!-- Actions contextuelles -->
  <div v-if="actionsToDisplay.length" class="absolute right-2 top-1/2 transform -translate-y-1/2 z-10 flex gap-1">
    <Btn
      v-for="action in actionsToDisplay"
      :key="action.key"
      :variant="action.variant"
      :color="action.color"
      :size="action.size"
      circle
      :aria-label="action.ariaLabel"
      :title="action.tooltip"
      @click.stop="action.onClick"
    >
      <i :class="action.icon" class="text-sm"></i>
    </Btn>
  </div>
</template>
```

## Utilisation

### Exemples de base

```vue
<!-- Input simple -->
<InputField label="Nom" v-model="name" />

<!-- Input avec style -->
<InputField 
  label="Email" 
  v-model="email" 
  variant="glass" 
  color="primary" 
  size="lg" 
/>

<!-- Input avec validation -->
<InputField 
  label="Mot de passe" 
  v-model="password" 
  type="password"
  :validation="{ state: 'error', message: 'Mot de passe trop court' }"
/>

<!-- Input avec actions -->
<InputField 
  label="Recherche" 
  v-model="search" 
  :actions="['clear', 'copy']"
/>
```

### Exemples avec fusion de styles

```vue
<!-- Utilisation de mergeStyleConfig dans un composant -->
<script setup>
import { computed } from 'vue';
import { mergeStyleConfig, getInputStyle } from '@/Composables/form/useInputStyle';

const props = defineProps({
  modelValue: Boolean,
  styleState: Object,
  variant: { type: String, default: 'glass' },
  color: { type: String, default: 'primary' },
  size: { type: String, default: 'md' }
});

// Configuration de base du composant
const baseConfig = {
  variant: props.variant,
  color: props.color,
  size: props.size
};

// Fusion avec styleState si fourni
const finalConfig = computed(() => {
  if (props.styleState) {
    // Déterminer l'état selon la valeur
    const state = props.modelValue ? 'on' : 'off';
    const stateConfig = props.styleState[state];
    
    if (stateConfig) {
      return mergeStyleConfig(baseConfig, stateConfig);
    }
  }
  
  return baseConfig;
});

// Générer les classes CSS
const atomClasses = computed(() => 
  getInputStyle('checkbox', finalConfig.value)
);
</script>
```

## Migration

### Depuis l'ancien système

#### Props obsolètes
- `bgOn` / `bgOff` → `styleState` (géré dans les composants)
- `validator` (string) → `validation` (objet)
- `style` (string) → `variant` (string)

#### Nouvelle API
```vue
<!-- Avant -->
<InputField 
  label="Email" 
  v-model="email"
  style="glass"
  validator="Email invalide"
  bgOn="bg-green-500"
/>

<!-- Après -->
<InputField 
  label="Email" 
  v-model="email"
  variant="glass"
  :validation="{ state: 'error', message: 'Email invalide' }"
/>
```

## API Référence

### Props communes

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `variant` | String | 'glass' | Variant du style |
| `color` | String | 'primary' | Couleur du composant |
| `size` | String | 'md' | Taille du composant |
| `animation` | Boolean/String | true | Animation du composant |
| `style` | Object/String | - | Configuration de style complète |

### Méthodes

| Méthode | Description |
|---------|-------------|
| `getInputStyle(type, config)` | Génère les classes CSS pour un type d'input |
| `getInputStyleProperties(type, config)` | Extrait les propriétés de style |
| `mergeStyleConfig(base, override, defaults)` | Fusionne intelligemment les configurations |
| `normalizeInputStyle(type, config)` | Normalise une configuration de style |
| `validateInputStyle(type, config)` | Valide une configuration de style |

### Composables

| Composable | Description |
|------------|-------------|
| `useInputStyle()` | Composable principal pour les styles |
| `useInputActions()` | Composable pour les actions contextuelles | 