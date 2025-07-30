# Architecture des Inputs

## Vue d'ensemble

Le système d'input fonctionne en duo : **Core** (Atom) + **Field** (Molecule).

- **Core** : Contient l'élément HTML natif (input, select, textarea, etc.) et la logique métier
- **Field** : Wrapper qui ajoute les labels externes, validation, helper, actions, etc.

## Transmission des Props/Attrs/Événements

### Problématique Vue 3

Vue 3 peut confondre certains attributs avec des événements :
- `type` (attribut HTML) peut être interprété comme `onType` (événement)
- `value` peut être interprété comme `onValue`

### Solution : inputHelper.js (Version simplifiée)

Le fichier `inputHelper.js` centralise la gestion de la transmission avec une approche basée entièrement sur les tableaux :

#### 1. **Props** (définies explicitement)
```javascript
// Props déclarées dans defineProps()
const props = defineProps({
    variant: String,
    size: String,
    validation: Object,
    // ...
});
```

#### 2. **Attributs** (attrs non déclarés)
```javascript
// Attributs HTML natifs, data-*, aria-*, etc.
const $attrs = useAttrs();
// Exemple : class, id, data-testid, aria-label, etc.
```

#### 3. **Événements** (listeners)
```javascript
// Événements préfixés par 'on' ou transmis via v-on
// Exemple : @input, @change, @focus, etc.
```

### Fonction principale : generateCoreBindings

#### `generateCoreBindings(inputType, fieldProps, fieldAttrs, inputProps, modelValue, validationState, options)`
Fonction unique qui génère tous les bindings pour transmettre du Field au Core :

```javascript
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

// Retourne :
{
    props: { /* props spécifiques au Core selon les tableaux */ },
    attrs: { /* attributs HTML */ },
    events: { /* événements */ }
}
```

### Fonctions utilitaires

#### `getInputProps(inputType, variant, exclude)`
Génère les props selon les tableaux `SPECIFIC_PROPS` et `COMMON_PROPS` :

```javascript
// Pour un Core
const coreProps = getInputProps('textarea', 'core');

// Pour un Field
const fieldProps = getInputProps('textarea', 'field');
```

#### `isPropAllowed(inputType, propName, variant)`
Vérifie si une prop est autorisée pour un type d'input :

```javascript
isPropAllowed('textarea', 'rows', 'core') // true
isPropAllowed('select', 'rows', 'core')   // false
```

#### `getPropDefinition(inputType, propName, variant)`
Récupère la définition d'une prop :

```javascript
getPropDefinition('textarea', 'rows', 'core')
// { key: 'rows', type: Number, default: 3 }
```

### Utilisation dans les Fields

#### Méthode recommandée (simple et efficace)
```vue
<script setup>
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
</script>

<template>
    <TextareaCore v-bind="coreBindings" />
</template>
```

### Avantages du système

1. **Simplicité** : Une seule fonction principale
2. **Flexibilité** : Basée entièrement sur les tableaux `SPECIFIC_PROPS` et `COMMON_PROPS`
3. **Robustesse** : Évite les conflits Vue 3 entre props et événements
4. **Maintenabilité** : Modifier le tableau = modifier le comportement
5. **Cohérence** : Une seule source de vérité
6. **Performance** : Moins de fonctions = moins de surcharge

### Types d'input supportés

Le système fonctionne avec tous les types d'input définis dans `SPECIFIC_PROPS` :
- `text`, `email`, `password`, `url`, `tel`, `search`, `number`
- `textarea`, `select`, `file`, `filter`
- `date`, `color`
- `range`, `rating`
- `checkbox`, `radio`, `toggle`

### Exemples de spécificité par type

```javascript
// Input textuel
generateCoreBindings('text', props, $attrs, inputProps, modelValue, validation)
// Inclut : type, placeholder, autocomplete, ariaLabel

// Select
generateCoreBindings('select', props, $attrs, inputProps, modelValue, validation)
// Inclut : multiple, ariaLabel (pas de type, placeholder, etc.)

// File
generateCoreBindings('file', props, $attrs, inputProps, modelValue, validation)
// Inclut : accept, multiple, capture (pas de type, placeholder, etc.)

// Textarea
generateCoreBindings('textarea', props, $attrs, inputProps, modelValue, validation)
// Inclut : placeholder, rows, cols (pas de type, autocomplete, etc.)

// Range
generateCoreBindings('range', props, $attrs, inputProps, modelValue, validation)
// Inclut : min, max, step (pas de placeholder, autocomplete, etc.)
```

### Comparaison : Ancien vs Nouveau système

#### ❌ Ancien système (générique)
```javascript
// getCommonCoreProps était trop générique
const coreProps = getCommonCoreProps(props, inputProps, modelValue, validation);
// Résultat pour un select :
{
    type: 'text',        // ❌ Inutile pour un select
    placeholder: '...',  // ❌ Inutile pour un select
    autocomplete: '...', // ❌ Inutile pour un select
    // ... autres props inutiles
}
```

#### ✅ Nouveau système (basé sur les tableaux)
```javascript
// generateCoreBindings est basé sur les tableaux
const coreBindings = generateCoreBindings('select', props, $attrs, inputProps, modelValue, validation);
// Résultat pour un select :
{
    multiple: true,      // ✅ Pertinent pour un select
    ariaLabel: '...',    // ✅ Pertinent pour un select
    // ❌ Pas de type, placeholder, autocomplete inutiles
}
```

#### Avantages du nouveau système

1. **Props pertinentes uniquement** : Chaque type d'input reçoit seulement les props qui ont du sens
2. **Moins d'erreurs** : Pas de props inutiles qui pourraient causer des conflits
3. **Performance** : Moins de props à traiter
4. **Maintenabilité** : Le tableau `SPECIFIC_PROPS` fait foi sur ce qui est autorisé
5. **Flexibilité** : Facile d'ajouter de nouveaux types d'input

## Pattern Core (Atom)

```vue
<script setup>
import { getInputProps, getVBindAttrs, getVOnEvents, getCoreAttrs } from '@/Utils/atomic-design/inputHelper';
import { useAttrs } from 'vue';

// Props héritées automatiquement
const props = defineProps({ ...getInputProps('input', 'core') });
const $attrs = useAttrs();

// Bindings optimisés
const vBindAttrs = computed(() => getVBindAttrs($attrs, 'input', 'core', coreProps.value));
const vOnEvents = computed(() => getVOnEvents($attrs, 'input', 'core'));
const inputBindings = computed(() => ({
    ...getCoreAttrs(props, { ref: inputRef }),
    ...vBindAttrs.value,
}));
</script>

<template>
  <input v-bind="inputBindings" v-on="vOnEvents" />
</template>
```

## Pattern Field (Molecule)

```vue
<script setup>
import { generateCoreBindings } from '@/Utils/atomic-design/inputHelper';
import { useAttrs } from 'vue';

// Props héritées automatiquement
const props = defineProps({ ...getInputProps('input', 'field') });

// Transmission optimisée au Core
const coreBindings = computed(() => 
    generateCoreBindings(props, useAttrs(), 'input')
);
</script>

<template>
  <InputCore v-bind="coreBindings.props" v-on="coreBindings.events" />
</template>
```

## Types d'input supportés

| Type | Core | Field | Description |
|------|------|-------|-------------|
| `input` | ✅ | ✅ | Input textuel standard |
| `select` | ✅ | ✅ | Liste déroulante |
| `textarea` | ✅ | ✅ | Zone de texte |
| `checkbox` | ✅ | ✅ | Case à cocher |
| `radio` | ✅ | ✅ | Bouton radio |
| `toggle` | ✅ | ✅ | Interrupteur |
| `range` | ✅ | ✅ | Curseur |
| `rating` | ✅ | ✅ | Évaluation |
| `file` | ✅ | ✅ | Sélection de fichier |
| `date` | ✅ | ✅ | Sélecteur de date |
| `color` | ✅ | ✅ | Sélecteur de couleur |
| `filter` | ✅ | ✅ | Filtre de recherche |

## Avantages de la factorisation

### ✅ DRY (Don't Repeat Yourself)
- **1 seul endroit** pour modifier l'API des inputs
- **0 duplication** de code entre les composants
- **Cohérence garantie** sur tous les types d'input

### ✅ Maintenabilité
- Ajout d'une prop = modification dans `inputHelper.js` uniquement
- Suppression d'une prop = suppression dans `inputHelper.js` uniquement
- Évolution de l'API = impact maîtrisé

### ✅ Extensibilité
- Nouveau type d'input = ajout dans `SPECIFIC_PROPS`
- Nouvelle prop commune = ajout dans `COMMON_PROPS`
- Nouvelle fonction = ajout dans `inputHelper.js`

### ✅ Onboarding
- **Pattern unique** pour tous les inputs
- **API cohérente** entre Core et Field
- **Documentation centralisée**

## Migration depuis l'ancienne architecture

### ❌ Anciennes fonctions (obsolètes)
```javascript
// À NE PLUS UTILISER
getInputAttrs()
getFilteredAttrs()
getFilteredEvents()
combineAttrs()
```

### ✅ Nouvelles fonctions
```javascript
// À UTILISER
getVBindAttrs($attrs, 'input', 'core', componentProps)
getVOnEvents($attrs, 'input', 'core')
generateCoreBindings(fieldProps, fieldAttrs, 'input')
getInputProps('input', 'core')
```

## Liens utiles

- [inputHelper.js](../../resources/js/Utils/atomic-design/inputHelper.js)
- [INPUTS.md](./INPUTS.md) — Guide d'utilisation
- [INPUT_STYLES.md](./INPUT_STYLES.md) — Styles et variants 