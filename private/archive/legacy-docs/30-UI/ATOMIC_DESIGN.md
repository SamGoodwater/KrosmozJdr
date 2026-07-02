# Atomic Design — KrosmozJDR

## Philosophie

- **Atoms** : Composants de base réutilisables (Core inputs, boutons, etc.)
- **Molecules** : Compositions avec logique métier (Field inputs, formulaires, etc.)
- **Organisms** : Sections complètes (pages, modales, etc.)
- **Composables** : Logique réutilisable (useInputActions, useValidation, etc.)

## Structure factorisée

### Pattern unifié
```javascript
// Toutes les props sont centralisées
import { getInputProps } from '@/Utils/atomic-design/inputHelper';
const props = defineProps({ ...getInputProps('input', 'field') });
```

### Architecture Core → Field
```vue
<!-- Core (Atom) -->
<InputCore v-bind="inputBindings" v-on="vOnEvents" />

<!-- Field (Molecule) -->
<InputField v-bind="fieldBindings">
  <InputCore v-bind="coreBindings" />
</InputField>
```

## Navigation

- **[INPUTS.md](./INPUTS.md)** — Architecture des champs de saisie
- **[INPUT_ARCHITECTURE.md](./INPUT_ARCHITECTURE.md)** — Factorisation et patterns
- **[VALIDATION.md](./VALIDATION.md)** — Système de validation
- **[NOTIFICATIONS.md](./NOTIFICATIONS.md)** — Feedback utilisateur

## Avantages

- ✅ **DRY** : API centralisée, 0 duplication
- ✅ **Cohérence** : Pattern unique pour tous les composants
- ✅ **Maintenabilité** : 1 seul endroit pour modifier l'API
- ✅ **Extensibilité** : Facile d'ajouter de nouveaux composants 