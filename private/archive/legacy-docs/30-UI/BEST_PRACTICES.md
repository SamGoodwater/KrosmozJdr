# Bonnes pratiques UI — KrosmozJDR

## Architecture factorisée

### ✅ À faire
```javascript
// Props héritées automatiquement
import { getInputProps } from '@/Utils/atomic-design/inputHelper';
import { useAttrs } from 'vue';

const props = defineProps({ ...getInputProps('input', 'field') });

// Bindings optimisés
const vBindAttrs = computed(() => getVBindAttrs($attrs, 'input', 'core', props));
const vOnEvents = computed(() => getVOnEvents($attrs, 'input', 'core'));

// Transmission Field → Core
const coreBindings = computed(() => generateCoreBindings(props, useAttrs(), 'input'));
```

### ❌ À éviter
```javascript
// Ne pas redéclarer les props factorisées
const props = defineProps({
    modelValue: String,  // ❌ Déjà dans getInputProps()
    placeholder: String, // ❌ Déjà dans getInputProps()
});

// Ne pas utiliser les anciennes fonctions
import { getInputAttrs } from '@/Utils/atomic-design/inputHelper'; // ❌ Obsolète
```

## Composants

### Core (Atom)
- **Responsabilité** : Input natif + accessibilité + styles
- **Props** : Héritées via `getInputProps('type', 'core')`
- **Bindings** : `getVBindAttrs()` + `getVOnEvents()`
- **Pas de logique métier**

### Field (Molecule)
- **Responsabilité** : Composition + validation + actions
- **Props** : Héritées via `getInputProps('type', 'field')`
- **Transmission** : `generateCoreBindings()` vers le Core
- **Logique métier** : `useInputActions`, `useValidation`

## Styles

### ✅ Utilisation
```javascript
import { getInputStyle } from '@/Composables/form/useInputStyle';

const inputClasses = computed(() => 
    getInputStyle('input', { variant: 'glass', color: 'primary' })
);
```

### ✅ Variants disponibles
- `glass` : Transparent avec bordure
- `dash` : Bordure pointillée
- `outline` : Bordure simple
- `ghost` : Transparent sans bordure
- `soft` : Bordure inférieure uniquement

## Validation

### ✅ Pattern unifié
```javascript
import { useValidation } from '@/Composables/form/useValidation';

const { validateField, handleServerErrors } = useValidation();

// Validation locale
validateField('email', { state: 'error', message: 'Email invalide' });

// Erreurs serveur
handleServerErrors(form.errors);
```

## Notifications

### ✅ API simple
```javascript
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

const notificationStore = useNotificationStore();

// Notifications rapides
notificationStore.success('Opération réussie');
notificationStore.error('Erreur détectée');
```

## Performance

### ✅ Optimisations
- **Computed properties** pour les bindings
- **Filtrage intelligent** des attributs et événements
- **Transmission optimisée** Field → Core
- **Réactivité ciblée** avec `watch` et `computed`

### ❌ Anti-patterns
- **Re-calculs inutiles** dans les templates
- **Props non factorisées** redéclarées
- **Logique métier** dans les composants Core

## Extensibilité

### ✅ Ajouter un nouveau type d'input
1. Ajouter dans `SPECIFIC_PROPS` (inputHelper.js)
2. Créer les composants Core et Field
3. Utiliser le pattern unifié
4. Documenter dans INPUTS.md

### ✅ Ajouter une nouvelle prop
1. Ajouter dans `COMMON_PROPS` ou `SPECIFIC_PROPS`
2. Mettre à jour les validations si nécessaire
3. Tester sur tous les types d'input
4. Documenter les changements

## Tests

### ✅ Checklist
- [ ] Props héritées correctement
- [ ] Bindings transmis au Core
- [ ] Événements filtrés selon le type
- [ ] Styles appliqués
- [ ] Validation fonctionnelle
- [ ] Actions contextuelles opérationnelles

## Liens utiles

- [INPUT_ARCHITECTURE.md](./INPUT_ARCHITECTURE.md) — Architecture détaillée
- [INPUT_MIGRATION.md](./INPUT_MIGRATION.md) — Migration et changements
- [inputHelper.js](../../resources/js/Utils/atomic-design/inputHelper.js) — API centralisée 