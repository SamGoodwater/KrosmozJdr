# Guide de Migration - Système d'Inputs

## Vue d'ensemble

Ce guide explique comment migrer de l'ancien système d'inputs (basé sur des fonctions génériques) vers le nouveau système (basé entièrement sur les tableaux `SPECIFIC_PROPS` et `COMMON_PROPS`).

## Avantages du nouveau système

1. **Simplicité** : Une seule fonction principale `generateCoreBindings`
2. **Flexibilité** : Basée entièrement sur les tableaux
3. **Robustesse** : Évite les conflits Vue 3 entre props et événements
4. **Maintenabilité** : Modifier le tableau = modifier le comportement
5. **Performance** : Moins de fonctions = moins de surcharge

## Migration des Fields

### ❌ Ancien système (complexe)

```vue
<script setup>
// Imports complexes
import { 
    getInputProps, 
    generateCoreBindings,
    hasValidation,
    getCommonCoreProps,
    getInputLabelProps,
    getVBindAttrs,
    getVOnEvents
} from '@/Utils/atomic-design/inputHelper';

// Logique complexe
const fieldProps = computed(() => getInputProps('input', 'field'));
const fieldVBindAttrs = computed(() => getVBindAttrs($attrs, 'input', 'field', fieldProps.value));
const fieldVOnEvents = computed(() => getVOnEvents($attrs, 'input', 'field'));
const coreBindings = computed(() => generateCoreBindings(props, fieldVBindAttrs.value, 'input'));

const coreProps = computed(() => ({
    ...getCommonCoreProps(props, inputProps.value, currentValue.value, processedValidation.value),
    ...getInputLabelProps(labelConfig.value, !!labelConfig.value.floating),
    ...coreBindings.value.props,
    ...coreBindings.value.attrs,
    ref: inputRef,
}));
</script>

<template>
    <InputCore 
        v-bind="coreProps" 
        v-on="coreBindings.events"
    />
</template>
```

### ✅ Nouveau système (simple)

```vue
<script setup>
// Imports simplifiés
import { 
    getInputProps, 
    generateCoreBindings,
    hasValidation,
    getInputLabelProps
} from '@/Utils/atomic-design/inputHelper';

// Une seule fonction
const coreBindings = computed(() => 
    generateCoreBindings(
        'input',              // Type d'input
        props,                // Props du Field
        $attrs,               // Attrs du Field
        inputProps.value,     // Props du composable useInputActions
        currentValue.value,   // Valeur courante
        processedValidation.value, // État de validation
        { ref: inputRef }     // Options supplémentaires
    )
);
</script>

<template>
    <InputCore v-bind="coreBindings" />
</template>
```

## Migration des Core

### ❌ Ancien système (filtrage complexe)

```vue
<script setup>
// Imports complexes
import { 
    getInputProps, 
    getVBindAttrs, 
    getVOnEvents, 
    getCoreAttrs 
} from '@/Utils/atomic-design/inputHelper';

// Logique de filtrage complexe
const coreProps = computed(() => getInputProps('textarea', 'core'));
const vBindAttrs = computed(() => getVBindAttrs($attrs, 'textarea', 'core', coreProps.value));
const vOnEvents = computed(() => getVOnEvents($attrs, 'textarea', 'core'));
const textareaBindings = computed(() => ({
    ...getCoreAttrs(props, { ref: textareaRef }),
    ...vBindAttrs.value,
}));
</script>

<template>
    <textarea
        v-bind="textareaBindings"
        v-on="vOnEvents"
        @input="onInput"
    />
</template>
```

### ✅ Nouveau système (direct)

```vue
<script setup>
// Imports simplifiés
import { getInputProps } from '@/Utils/atomic-design/inputHelper';

// Aucune logique de filtrage nécessaire
// Les bindings arrivent déjà correctement du Field
</script>

<template>
    <textarea
        :class="atomClasses"
        :value="modelValue"
        :placeholder="placeholder"
        :rows="rows"
        :cols="cols"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        v-bind="$attrs"
        @input="onInput"
    />
</template>
```

## Fonctions supprimées

Les fonctions suivantes ont été supprimées car elles sont remplacées par `generateCoreBindings` :

- ❌ `getCommonCoreProps`
- ❌ `getTypeSpecificCoreProps`
- ❌ `getTypeSpecificCoreAttrs`
- ❌ `getVBindAttrs`
- ❌ `getVOnEvents`
- ❌ `getCoreAttrs`
- ❌ `generateAdvancedCoreBindings`
- ❌ `generateCorePropsFromTables`
- ❌ `generateCoreAttrsFromTables`
- ❌ `generateCoreEventsFromTables`
- ❌ `generateUltimateCoreBindings`

## Fonctions conservées

Les fonctions suivantes sont conservées car elles restent utiles :

- ✅ `getInputProps` - Génère les props selon les tableaux
- ✅ `generateCoreBindings` - Fonction principale (nouvelle signature)
- ✅ `isPropAllowed` - Vérifie si une prop est autorisée
- ✅ `getPropDefinition` - Récupère la définition d'une prop
- ✅ `getInputLabelProps` - Props pour les labels inline
- ✅ `hasValidation` - Vérifie si un composant a une validation

## Exemples de migration par type

### TextareaField

```javascript
// ❌ Avant
const coreBindings = computed(() => 
    generateCoreBindings(props, $attrs, 'textarea')
);

// ✅ Après
const coreBindings = computed(() => 
    generateCoreBindings(
        'textarea',           // Type d'input
        props,                // Props du Field
        $attrs,               // Attrs du Field
        inputProps.value,     // Props du composable useInputActions
        currentValue.value,   // Valeur courante
        processedValidation.value, // État de validation
        { ref: inputRef }     // Options supplémentaires
    )
);
```

### InputField

```javascript
// ❌ Avant
const fieldProps = computed(() => getInputProps('input', 'field'));
const fieldVBindAttrs = computed(() => getVBindAttrs($attrs, 'input', 'field', fieldProps.value));
const fieldVOnEvents = computed(() => getVOnEvents($attrs, 'input', 'field'));
const coreBindings = computed(() => generateCoreBindings(props, fieldVBindAttrs.value, 'input'));

// ✅ Après
const coreBindings = computed(() => 
    generateCoreBindings(
        'input',              // Type d'input
        props,                // Props du Field
        $attrs,               // Attrs du Field
        inputProps.value,     // Props du composable useInputActions
        currentValue.value,   // Valeur courante
        processedValidation.value, // État de validation
        { ref: inputRef }     // Options supplémentaires
    )
);
```

## Vérification de la migration

Pour vérifier que la migration est correcte :

1. **Props spécifiques** : Chaque type d'input reçoit seulement les props pertinentes
2. **Attributs HTML** : Les attributs natifs sont correctement transmis
3. **Événements** : Les événements sont correctement liés
4. **Validation** : L'état de validation est correctement transmis
5. **Références** : Les refs sont correctement gérées

## Tests recommandés

Après migration, testez :

1. **Fonctionnalité de base** : v-model, placeholder, validation
2. **Props spécifiques** : rows/cols pour textarea, multiple pour select, etc.
3. **Événements** : @input, @change, @focus, @blur
4. **Accessibilité** : aria-label, aria-invalid, etc.
5. **Styles** : variant, color, size, animation

## Support

En cas de problème lors de la migration :

1. Vérifiez que tous les imports sont corrects
2. Assurez-vous que la signature de `generateCoreBindings` est respectée
3. Vérifiez que les tableaux `SPECIFIC_PROPS` et `COMMON_PROPS` contiennent les bonnes définitions
4. Utilisez `isPropAllowed` et `getPropDefinition` pour déboguer 