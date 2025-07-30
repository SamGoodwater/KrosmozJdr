# Migration Input — Nouvelle architecture factorisée

## Changements récents

### ✅ Architecture refactorisée
- **API centralisée** dans `inputHelper.js`
- **Fonctions unifiées** pour tous les types d'input
- **Props factorisées** via `getInputProps()`
- **Bindings optimisés** avec `getVBindAttrs()` et `getVOnEvents()`

### ❌ Fonctions obsolètes supprimées
```javascript
// ANCIENNES FONCTIONS (À NE PLUS UTILISER)
getInputAttrs()
getFilteredAttrs()
getFilteredEvents()
combineAttrs()
```

### ✅ Nouvelles fonctions
```javascript
// NOUVELLES FONCTIONS (À UTILISER)
getInputProps('input', 'core')                    // Props pour Core
getInputProps('input', 'field')                   // Props pour Field
getVBindAttrs($attrs, 'input', 'core', props)     // Attributs pour v-bind
getVOnEvents($attrs, 'input', 'core')             // Événements pour v-on
generateCoreBindings(fieldProps, fieldAttrs, 'input') // Transmission Field → Core
```

## Migration des composants

### Avant (ancienne architecture)
```vue
<script setup>
import { getInputAttrs } from '@/Utils/atomic-design/inputHelper';

const combinedAttrs = computed(() => getInputAttrs(props, $attrs));
const eventListeners = computed(() => getFilteredEvents($attrs));
</script>

<template>
  <input v-bind="combinedAttrs" v-on="eventListeners" />
</template>
```

### Après (nouvelle architecture)
```vue
<script setup>
import { getInputProps, getVBindAttrs, getVOnEvents, getCoreAttrs } from '@/Utils/atomic-design/inputHelper';

const props = defineProps({ ...getInputProps('input', 'core') });
const $attrs = useAttrs();

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

## Composants mis à jour

### Core Components (12/12)
- ✅ `InputCore.vue`
- ✅ `SelectCore.vue`
- ✅ `TextareaCore.vue`
- ✅ `CheckboxCore.vue`
- ✅ `RadioCore.vue`
- ✅ `ToggleCore.vue`
- ✅ `RangeCore.vue`
- ✅ `RatingCore.vue`
- ✅ `FileCore.vue`
- ✅ `DateCore.vue`
- ✅ `ColorCore.vue`
- ✅ `FilterCore.vue`

### Field Components (13/13)
- ✅ `InputField.vue`
- ✅ `TextareaField.vue`
- ✅ `SelectField.vue`
- ✅ `CheckboxField.vue`
- ✅ `RadioField.vue`
- ✅ `ToggleField.vue`
- ✅ `RangeField.vue`
- ✅ `RatingField.vue`
- ✅ `FileField.vue`
- ✅ `DateField.vue`
- ✅ `ColorField.vue`
- ✅ `FilterField.vue`

## Avantages de la migration

### ✅ DRY (Don't Repeat Yourself)
- **1 seul endroit** pour modifier l'API des inputs
- **0 duplication** de code entre les composants
- **Cohérence garantie** sur tous les types d'input

### ✅ Maintenabilité
- Ajout d'une prop = modification dans `inputHelper.js` uniquement
- Suppression d'une prop = suppression dans `inputHelper.js` uniquement
- Évolution de l'API = impact maîtrisé

### ✅ Performance
- **Bindings optimisés** avec filtrage intelligent
- **Événements filtrés** selon le type d'input
- **Transmission efficace** du Field au Core

### ✅ Extensibilité
- Nouveau type d'input = ajout dans `SPECIFIC_PROPS`
- Nouvelle prop commune = ajout dans `COMMON_PROPS`
- Nouvelle fonction = ajout dans `inputHelper.js`

## Vérification

### ✅ Tests effectués
- [x] Suppression des fonctions obsolètes
- [x] Mise à jour des imports
- [x] Mise à jour des templates
- [x] Vérification des bindings
- [x] Test de la transmission Field → Core

### ✅ Résultats
- **25/25 composants** mis à jour avec succès
- **0 référence** aux anciennes fonctions
- **Architecture unifiée** et cohérente
- **Code plus maintenable** et robuste

## Liens utiles

- [INPUT_ARCHITECTURE.md](./INPUT_ARCHITECTURE.md) — Architecture détaillée
- [INPUTS.md](./INPUTS.md) — Guide d'utilisation
- [inputHelper.js](../../resources/js/Utils/atomic-design/inputHelper.js) — API centralisée 