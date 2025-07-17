# Inputs — Guide KrosmozJDR

## 1. Structure

- **InputCore (Atom)** : input natif, accessibilité, classes, labels inline/floating. Pas de logique métier. Toutes les props (HTML, layout, actions, etc.) sont héritées automatiquement via `getInputProps('input', 'core')` depuis `inputHelper.js`.
- **InputField (Molecule)** : composition de InputCore, InputLabel, Btn, etc. Orchestration des actions contextuelles via useInputActions. Toutes les props (communes, spécifiques, layout, validation, actions, etc.) sont héritées via `getInputProps('input', 'field')`.
- **useInputActions (Composable)** : centralise la logique des actions contextuelles (reset, back, clear, password, copy, toggleEdit, etc.).

## 2. API et slots

### InputCore
- Props : **toutes les props sont factorisées** dans `inputHelper.js` et héritées via `getInputProps('input', 'core')` :
  - Exemples : `type`, `modelValue`, `placeholder`, `readonly`, `disabled`, `color`, `size`, `style`, `variant`, `labelFloating`, `labelStart`, `labelEnd`, etc.
- Slots : `labelStart`, `labelEnd`, `floatingLabel`

### InputField
- Props : **toutes les props de InputCore + toutes les props métier, layout, validation, actions, etc.** via `getInputProps('input', 'field')` :
  - Exemples : `label`, `defaultLabelPosition`, `helper`, `validation`, `validator`, `errorMessage`, `actions`, `useFieldComposable`, etc.
- Slots : `overStart`, `overEnd`, `labelTop`, `labelBottom`, etc.
- Utilise `actionsToDisplay` pour générer dynamiquement les boutons d’actions contextuelles

### useInputActions
- Expose :
  - `currentValue`, `isModified`, `isReadonly`, `inputRef`, `focus`
  - `actionsToDisplay` (tableau d’actions à boucler dans la vue)
  - Handlers : `reset`, `back`, `clear`, `togglePassword`, `copy`, `toggleEdit`
  - `inputProps` (props à transmettre à l’input atom)

#### Exemple d’utilisation factorisée
```vue
<script setup>
import { getInputProps } from '@/Utils/atomic-design/inputHelper';
const props = defineProps({
  ...getInputProps('input', 'field'),
});
```

## 3. Exemples d’utilisation

```vue
<InputField
  v-model="email"
  label="Email"
  :actions="['reset', 'clear', 'copy', 'password']"
  type="password"
  color="primary"
  size="md"
/>
```

## 4. Bonnes pratiques
- Toujours utiliser InputField pour bénéficier de toute la puissance du système
- Factoriser la logique métier dans useInputActions
- Utiliser les slots pour personnaliser l’UI sans casser la structure
- **Ne jamais redéclarer localement une prop déjà factorisée dans inputHelper.js**
- Documenter chaque usage spécifique

## 5. DRYness & Maintenabilité
- **Toute l’API des inputs est centralisée dans `inputHelper.js`** : ajout/suppression/évolution d’une prop = 1 seul endroit à modifier
- Les composants sont ultra-DRY, évolutifs, et cohérents
- La migration et l’onboarding sont facilités

## 6. Liens utiles
- [DaisyUI - Input](https://daisyui.com/components/input/)
- [Tailwind CSS](https://tailwindcss.com/docs/utility-first) 