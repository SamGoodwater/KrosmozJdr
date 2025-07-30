# UI & Atomic Design — KrosmozJDR

## Navigation rapide

- **[INPUTS.md](./INPUTS.md)** — Structure factorisée des champs de saisie (Core/Field)
- **[INPUT_ARCHITECTURE.md](./INPUT_ARCHITECTURE.md)** — Factorisation et patterns unifiés
- **[INPUT_MIGRATION.md](./INPUT_MIGRATION.md)** — Migration vers la nouvelle architecture
- **[BEST_PRACTICES.md](./BEST_PRACTICES.md)** — Bonnes pratiques et patterns
- **[VALIDATION.md](./VALIDATION.md)** — Système de validation unifié avec notifications
- **[NOTIFICATIONS.md](./NOTIFICATIONS.md)** — Toasts et feedback utilisateur
- **[INPUT_STYLES.md](./INPUT_STYLES.md)** — Styles et variants des inputs
- **[DESIGN_GUIDE.md](./DESIGN_GUIDE.md)** — Guide complet du design system

## Architecture

- **Atoms** : Composants de base (`InputCore`, `SelectCore`, etc.)
- **Molecules** : Compositions avec logique (`InputField`, `SelectField`, etc.)
- **Composables** : Logique réutilisable (`useInputActions`, `useValidation`, etc.)
- **Helpers** : API centralisée (`inputHelper.js`, `useInputStyle.js`)

## Pattern unifié

```javascript
// Toutes les props sont factorisées
import { getInputProps } from '@/Utils/atomic-design/inputHelper';
const props = defineProps({ ...getInputProps('input', 'field') });
```

**Pour toute question UI, commencez par le fichier correspondant à votre besoin.** 