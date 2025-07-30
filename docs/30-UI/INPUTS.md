# Inputs — Architecture factorisée KrosmozJDR

## Structure

- **Core (Atom)** : Input natif avec accessibilité et styles. Props héritées via `getInputProps('type', 'core')`
- **Field (Molecule)** : Composition avec label, validation, actions. Props héritées via `getInputProps('type', 'field')`
- **Helpers** : API centralisée dans `inputHelper.js` et `useInputStyle.js`

## API unifiée

### Props factorisées
```javascript
// Toutes les props sont centralisées
import { getInputProps } from '@/Utils/atomic-design/inputHelper';

// Core component
const props = defineProps({ ...getInputProps('input', 'core') });

// Field component  
const props = defineProps({ ...getInputProps('input', 'field') });
```

### Types supportés
- **Textuels** : `input`, `textarea`, `select`, `file`, `filter`
- **Numériques** : `number`, `range`, `rating`
- **Sélection** : `checkbox`, `radio`, `toggle`
- **Spéciaux** : `date`, `color`

## Architecture Core

```vue
<script setup>
import { getInputProps, getVBindAttrs, getVOnEvents, getCoreAttrs } from '@/Utils/atomic-design/inputHelper';
import { useAttrs } from 'vue';

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

## Architecture Field

```vue
<script setup>
import { generateCoreBindings } from '@/Utils/atomic-design/inputHelper';
import { useAttrs } from 'vue';

// Bindings transmis au Core
const coreBindings = computed(() => 
    generateCoreBindings(props, useAttrs(), 'input')
);
</script>

<template>
  <InputCore v-bind="coreBindings" />
</template>
```

## Actions contextuelles

```javascript
import useInputActions from '@/Composables/form/useInputActions';

const { actionsToDisplay, inputProps } = useInputActions({
    modelValue: props.modelValue,
    type: props.type,
    actions: props.actions,
});
```

### Actions disponibles
- `reset` : Revenir à la valeur initiale
- `back` : Annuler la dernière modification
- `clear` : Vider le champ
- `copy` : Copier le contenu
- `password` : Afficher/masquer le mot de passe
- `edit` : Basculer édition/lecture seule

## Exemples

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

## Bonnes pratiques

- ✅ Utiliser `getInputProps()` pour hériter toutes les props
- ✅ Utiliser `generateCoreBindings()` pour transmettre du Field au Core
- ✅ Utiliser `useInputActions()` pour les actions contextuelles
- ❌ Ne pas redéclarer les props déjà factorisées
- ❌ Ne pas utiliser les anciennes fonctions (`getInputAttrs`, etc.)

## Liens utiles

- [inputHelper.js](../../resources/js/Utils/atomic-design/inputHelper.js)
- [useInputStyle.js](../../resources/js/Composables/form/useInputStyle.js)
- [useInputActions.js](../../resources/js/Composables/form/useInputActions.js) 