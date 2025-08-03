# Architecture des Inputs - Système Unifié

## Vue d'ensemble

Le système d'input fonctionne en trio : **Core** (Atom) + **Field** (Molecule) + **FieldTemplate** (Template).

- **Core** : Contient l'élément HTML natif (input, select, textarea, etc.) et la logique métier
- **Field** : Wrapper qui utilise `useInputField` et `FieldTemplate` pour une API unifiée
- **FieldTemplate** : Template standardisé pour tous les composants Field

## Architecture Unifiée

### 🎯 **Nouveau système : useInputField + FieldTemplate**

Le système a été entièrement refactorisé pour offrir une API unifiée et transparente :

#### 1. **useInputField** - Composable unifié
```javascript
// Centralise toute la logique pour tous les types d'input
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
```

#### 2. **FieldTemplate** - Template standardisé
```vue
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
    input-type="checkbox" <!-- Prop pour gérer les classes CSS dynamiques -->
  >
    <!-- Slot core spécifique -->
    <template #core="{ inputAttrs, listeners, inputRef }">
      <InputCore v-bind="inputAttrs" v-on="listeners" ref="inputRef" />
    </template>
    
    <!-- Slots personnalisés -->
    <template #helper>
      <slot name="helper" />
    </template>
  </FieldTemplate>
</template>
```

#### 3. **Gestion des classes CSS dynamiques**

Le `FieldTemplate` gère automatiquement les classes CSS du bloc principal selon le type d'input :

```javascript
// Classes dynamiques pour le bloc principal selon le type d'input
const mainBlockClasses = computed(() => {
  const baseClasses = 'relative'
  
  // Types d'inputs avec taille fixe (pas de flex-1)
  const fixedSizeTypes = ['checkbox', 'radio', 'toggle', 'rating']
  
  if (fixedSizeTypes.includes(props.inputType)) {
    return baseClasses // "relative"
  }
  
  // Types d'inputs avec taille dynamique (avec flex-1)
  return `${baseClasses} flex-1` // "relative flex-1"
})
```

**Types d'inputs classifiés :**

- **Taille fixe** (pas de `flex-1`) : `checkbox`, `radio`, `toggle`, `rating`
- **Taille dynamique** (avec `flex-1`) : `input`, `textarea`, `select`, `range`, `filter`, `file`, `color`, `date`

Cette classification permet d'éviter les problèmes de mise en page avec les inputs ayant une taille fixe.

#### 4. **Bonnes pratiques pour les classes CSS dynamiques**

**Pour les composants Field existants :**
- ✅ **Types avec taille fixe** : Ajouter `input-type="[type]"` au `FieldTemplate`
- ✅ **Types avec taille dynamique** : Aucune modification nécessaire (utilise le type par défaut `'input'`)

**Pour les nouveaux composants Field :**
1. **Identifier le type d'input** dans `inputHelper.js`
2. **Déterminer la classification** :
   - Taille fixe → Ajouter `input-type="[type]"`
   - Taille dynamique → Pas de modification
3. **Tester le comportement** avec différents layouts

**Exemples d'utilisation :**

```vue
<!-- Checkbox avec taille fixe -->
<FieldTemplate input-type="checkbox">
  <template #core="{ inputAttrs, listeners, inputRef }">
    <CheckboxCore v-bind="inputAttrs" v-on="listeners" ref="inputRef" />
  </template>
</FieldTemplate>

<!-- Input avec taille dynamique (par défaut) -->
<FieldTemplate>
  <template #core="{ inputAttrs, listeners, inputRef }">
    <InputCore v-bind="inputAttrs" v-on="listeners" ref="inputRef" />
  </template>
</FieldTemplate>
```

## Pattern Field Unifié

### ✅ **Nouveau pattern (tous les composants Field)**

```vue
<script setup>
/**
 * InputField Molecule (DaisyUI, Atomic Design)
 * 
 * @description
 * Molecule pour input complet, utilisant le système unifié useInputField.
 * 
 * @example
 * // Label simple
 * <InputField label="Email" v-model="email" />
 * 
 * // Avec validation
 * <InputField 
 *   label="Email" 
 *   v-model="email"
 *   :validation="{ state: 'error', message: 'Email invalide' }"
 * />
 */
import { useSlots, useAttrs } from 'vue'
import InputCore from '@/Pages/Atoms/data-input/InputCore.vue'
import FieldTemplate from '@/Pages/Molecules/data-input/FieldTemplate.vue'
import useInputField from '@/Composables/form/useInputField'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'

// ------------------------------------------
// 🔧 Définition des props et des events
// ------------------------------------------
const props = defineProps(getInputPropsDefinition('input', 'field'))
const emit = defineEmits(['update:modelValue'])
const $attrs = useAttrs()

// ------------------------------------------
// 🎯 Utilisation du composable unifié
// ------------------------------------------
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
    <!-- Slot core spécifique pour InputCore -->
    <template #core="{ inputAttrs, listeners, inputRef }">
      <InputCore
        v-bind="inputAttrs"
        v-on="listeners"
        ref="inputRef"
      />
    </template>
    
    <!-- Slots personnalisés -->
    <template #helper>
      <slot name="helper" />
    </template>
  </FieldTemplate>
</template>
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

## Avantages du Système Unifié

### ✅ **DRY (Don't Repeat Yourself)**
- **1 seul endroit** pour modifier l'API des inputs
- **0 duplication** de code entre les composants
- **Cohérence garantie** sur tous les types d'input

### ✅ **Maintenabilité**
- Ajout d'une prop = modification dans `useInputField` uniquement
- Suppression d'une prop = suppression dans `useInputField` uniquement
- Évolution de l'API = impact maîtrisé

### ✅ **Performance**
- **Bundle size réduit** : Moins de code dupliqué
- **Tree-shaking optimisé** : Imports plus efficaces
- **Mémoire réduite** : Moins d'instances de logique dupliquée

### ✅ **Transparence**
- **Validation locale préservée** : Les vues peuvent avoir leurs logiques spécifiques
- **Événements personnalisés préservés** : `@blur`, `@input`, `@focus`, etc.
- **v-model transparent** : Fonctionne avec les logiques de vue sans interférence
- **Actions contextuelles optionnelles** : Ne s'activent que si explicitement demandées

### ✅ **Extensibilité**
- Nouveau type d'input = ajout dans `useInputField`
- Nouvelle prop commune = ajout dans `useInputField`
- Nouvelle fonctionnalité = ajout dans `useInputField`

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
```

### ✅ **Nouveau système**
```javascript
// À UTILISER
useInputField()           // Composable unifié
FieldTemplate             // Template standardisé
getInputPropsDefinition() // Définition des props
```

## Types d'input supportés

| Type | Core | Field | Description | Taille CSS |
|------|------|-------|-------------|------------|
| `input` | ✅ | ✅ | Input textuel standard | Dynamique |
| `select` | ✅ | ✅ | Liste déroulante | Dynamique |
| `textarea` | ✅ | ✅ | Zone de texte | Dynamique |
| `checkbox` | ✅ | ✅ | Case à cocher | Fixe |
| `radio` | ✅ | ✅ | Bouton radio | Fixe |
| `toggle` | ✅ | ✅ | Interrupteur | Fixe |
| `range` | ✅ | ✅ | Curseur | Dynamique |
| `rating` | ✅ | ✅ | Évaluation | Fixe |
| `file` | ✅ | ✅ | Sélection de fichier | Dynamique |
| `date` | ✅ | ✅ | Sélecteur de date | Dynamique |
| `color` | ✅ | ✅ | Sélecteur de couleur | Dynamique |
| `filter` | ✅ | ✅ | Filtre de recherche | Dynamique |

**Légende :**
- **Dynamique** : Utilise `flex-1` pour occuper l'espace disponible
- **Fixe** : Taille fixe, pas de `flex-1` pour éviter les problèmes de mise en page

## Liens utiles

- [useInputField.js](../../resources/js/Composables/form/useInputField.js)
- [FieldTemplate.vue](../../resources/js/Pages/Molecules/data-input/FieldTemplate.vue)
- [INPUTS.md](./INPUTS.md) — Guide d'utilisation
- [INPUT_STYLES.md](./INPUT_STYLES.md) — Styles et variants
- [REFACTORING_INPUTS_PHASE1.md](../100-%20Done/REFACTORING_INPUTS_PHASE1.md) — Historique du refactoring
- [REFACTORING_INPUTS_PHASE2.md](../100-%20Done/REFACTORING_INPUTS_PHASE2.md) — Simplification et tableaux 