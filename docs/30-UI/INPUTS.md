# Inputs — Système Unifié KrosmozJDR

## Vue d'ensemble

Le système d'input a été entièrement refactorisé pour offrir une API unifiée et transparente :

- **Core (Atom)** : Input natif avec accessibilité et styles
- **Field (Molecule)** : Wrapper utilisant `useInputField` et `FieldTemplate`
- **useInputField** : Composable unifié centralisant toute la logique
- **FieldTemplate** : Template standardisé pour tous les composants Field

## Architecture Unifiée

### 🎯 **Nouveau système : useInputField + FieldTemplate**

Tous les composants Field utilisent maintenant le même pattern :

```vue
<script setup>
import { useSlots, useAttrs } from 'vue'
import InputCore from '@/Pages/Atoms/data-input/InputCore.vue'
import FieldTemplate from '@/Pages/Molecules/data-input/FieldTemplate.vue'
import useInputField from '@/Composables/form/useInputField'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'

// Props héritées automatiquement
const props = defineProps(getInputPropsDefinition('input', 'field'))
const emit = defineEmits(['update:modelValue'])
const $attrs = useAttrs()

// Composable unifié
const {
  // V-model et actions
  currentValue, actionsToDisplay, inputRef, focus, isModified, isReadonly,
  reset, back, clear, togglePassword, copy, toggleEdit, showPassword,
  
  // Attributs et événements
  inputAttrs, listeners,
  
  // Labels
  labelConfig,
  
  // Validation
  processedValidation,
  
  // Style
  styleProperties, containerClasses,
  
  // Helpers
  getValidatorState, getValidatorMessage, hasValidationState
} = useInputField({
  modelValue: props.modelValue,
  type: 'input',
  mode: 'field',
  props,
  attrs: $attrs,
  emit
})
</script>

<template>
  <FieldTemplate
    :container-classes="containerClasses"
    :label-config="labelConfig"
    :input-attrs="inputAttrs"
    :listeners="listeners"
    :input-ref="inputRef"
    :actions-to-display="actionsToDisplay"
    :style-properties="styleProperties"
    :processed-validation="processedValidation"
    :has-validation-state="hasValidationState"
    :get-validator-state="getValidatorState"
    :get-validator-message="getValidatorMessage"
    :helper="props.helper"
  >
    <template #core="{ inputAttrs, listeners, inputRef }">
      <InputCore v-bind="inputAttrs" v-on="listeners" ref="inputRef" />
    </template>
    <template #helper><slot name="helper" /></template>
  </FieldTemplate>
</template>
```

## API Unifiée

### ✅ **Interface cohérente pour tous les composants**

Tous les composants Field ont maintenant la même interface :

```vue
<!-- Input -->
<InputField label="Email" v-model="email" />

<!-- Textarea -->
<TextareaField label="Description" v-model="description" />

<!-- Select -->
<SelectField label="Catégorie" v-model="category" :options="categories" />

<!-- Checkbox -->
<CheckboxField label="Conditions" v-model="accepted" />

<!-- Radio -->
<RadioField label="Genre" v-model="gender" :options="genders" />

<!-- Toggle -->
<ToggleField label="Notifications" v-model="notifications" />

<!-- Range -->
<RangeField label="Volume" v-model="volume" :min="0" :max="100" />

<!-- Rating -->
<RatingField label="Note" v-model="rating" :max="5" />

<!-- Filter -->
<FilterField label="Rechercher" v-model="search" />

<!-- File -->
<FileField label="Document" v-model="file" accept=".pdf" />

<!-- Color -->
<ColorField label="Couleur" v-model="color" />

<!-- Date -->
<DateField label="Date" v-model="date" />
```

## Transparence du Système

### ✅ **Validation et Actions Transparentes**

Le système est parfaitement transparent et permet aux vues d'avoir leurs logiques spécifiques :

#### **Validation locale préservée**
```javascript
// Dans Login.vue - Logique spécifique à la vue
function validateIdentifier() {
    const identifier = form.identifier;
    
    if (!identifier) {
        identifierValidation.value = quickValidation.local.error('Email ou pseudo requis');
        return false;
    }
    
    // Logique spécifique : validation email OU pseudo
    if (identifier.includes('@')) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(identifier)) {
            identifierValidation.value = quickValidation.local.error('Format d\'email invalide');
            return false;
        }
    }
    
    return true;
}
```

#### **Événements personnalisés préservés**
```vue
<InputField
    :validation="identifierValidation"
    @blur="validateIdentifier"        <!-- ✅ Préservé -->
    @input="handleIdentifierInput"    <!-- ✅ Préservé -->
    @focus="handleIdentifierFocus"    <!-- ✅ Préservé -->
/>
```

#### **v-model transparent**
```vue
<InputField
    v-model="form.identifier"         <!-- ✅ Fonctionne normalement -->
    :validation="identifierValidation"
/>
```

## Actions Contextuelles

### ✅ **Actions intégrées dans useInputField**

Les actions contextuelles sont maintenant intégrées dans le composable unifié :

```javascript
const {
  // Actions disponibles
  reset, back, clear, togglePassword, copy, toggleEdit, showPassword,
  
  // Actions à afficher
  actionsToDisplay
} = useInputField({
  modelValue: props.modelValue,
  type: 'input',
  mode: 'field',
  props,
  attrs: $attrs,
  emit
})
```

### Actions disponibles
- `reset` : Revenir à la valeur initiale
- `back` : Annuler la dernière modification
- `clear` : Vider le champ
- `copy` : Copier le contenu
- `togglePassword` : Afficher/masquer le mot de passe
- `toggleEdit` : Basculer édition/lecture seule

## Exemples d'utilisation

### Input basique
```vue
<InputField
  v-model="email"
  label="Email"
  type="email"
  color="primary"
/>
```

### Avec actions et validation
```vue
<InputField
  v-model="password"
  label="Mot de passe"
  type="password"
  :actions="['password', 'clear']"
  :validation="{ state: 'error', message: 'Mot de passe trop court' }"
/>
```

### Avec logique spécifique à la vue
```vue
<template>
  <InputField
    v-model="form.identifier"
    label="Email ou pseudo"
    :validation="identifierValidation"
    @blur="validateIdentifier"
    @input="handleIdentifierInput"
  />
</template>

<script setup>
// Logique spécifique à la vue
const identifierValidation = ref(null)

function validateIdentifier() {
  const identifier = form.identifier
  
  if (!identifier) {
    identifierValidation.value = { state: 'error', message: 'Champ requis' }
    return false
  }
  
  // Validation spécifique email OU pseudo
  if (identifier.includes('@')) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(identifier)) {
      identifierValidation.value = { state: 'error', message: 'Email invalide' }
      return false
    }
  }
  
  identifierValidation.value = { state: 'success', message: 'Format valide' }
  return true
}

function handleIdentifierInput() {
  // Logique spécifique lors de la saisie
  console.log('Identifiant modifié:', form.identifier)
}
</script>
```

## Composants Refactorisés

### ✅ **Tous les composants Field unifiés (12/12)**

| Composant | Lignes avant | Lignes après | Réduction |
|-----------|-------------|--------------|-----------|
| `InputField.vue` | 447 | ~80 | -82% |
| `TextareaField.vue` | 432 | ~80 | -81% |
| `SelectField.vue` | 507 | ~80 | -84% |
| `CheckboxField.vue` | 522 | ~80 | -85% |
| `RadioField.vue` | 556 | ~80 | -86% |
| `ToggleField.vue` | 533 | ~80 | -85% |
| `RangeField.vue` | 649 | ~80 | -88% |
| `RatingField.vue` | 695 | ~80 | -88% |
| `FilterField.vue` | 612 | ~80 | -87% |
| `FileField.vue` | 598 | ~80 | -87% |
| `ColorField.vue` | 789 | ~80 | -90% |
| `DateField.vue` | 823 | ~80 | -90% |

**Total : ~7,000 lignes → ~960 lignes (-86%)**

## Bonnes pratiques

### ✅ **À utiliser**
- Utiliser `useInputField()` pour centraliser la logique
- Utiliser `FieldTemplate` pour standardiser la structure
- Utiliser `getInputPropsDefinition()` pour hériter toutes les props
- Implémenter des logiques de validation spécifiques dans les vues
- Utiliser les événements personnalisés (`@blur`, `@input`, `@focus`)

### ❌ **À éviter**
- Ne pas redéclarer les props déjà factorisées
- Ne pas utiliser les anciennes fonctions (`getInputAttrs`, `generateCoreBindings`, etc.)
- Ne pas dupliquer la logique entre les composants Field
- Ne pas interférer avec le système de validation transparent

## Migration depuis l'ancienne architecture

### ❌ **Anciennes fonctions (obsolètes)**
```javascript
// À NE PLUS UTILISER
getInputAttrs()
getFilteredAttrs()
getFilteredEvents()
combineAttrs()
getCommonCoreProps()
getTypeSpecificCoreProps()
getVBindAttrs()
getVOnEvents()
generateCoreBindings()
```

### ✅ **Nouveau système**
```javascript
// À UTILISER
useInputField()           // Composable unifié
FieldTemplate             // Template standardisé
getInputPropsDefinition() // Définition des props
```

## Liens utiles

- [useInputField.js](../../resources/js/Composables/form/useInputField.js)
- [FieldTemplate.vue](../../resources/js/Pages/Molecules/data-input/FieldTemplate.vue)
- [INPUT_ARCHITECTURE.md](./INPUT_ARCHITECTURE.md) — Architecture détaillée
- [INPUT_STYLES.md](./INPUT_STYLES.md) — Styles et variants
- [REFACTORING_INPUTS_PHASE1.md](../100-%20Done/REFACTORING_INPUTS_PHASE1.md) — Historique du refactoring
- [REFACTORING_INPUTS_PHASE2.md](../100-%20Done/REFACTORING_INPUTS_PHASE2.md) — Simplification et tableaux 