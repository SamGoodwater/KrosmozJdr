# Inputs, Validation & Notifications — Guide KrosmozJDR

## 1. Architecture
- **Atomic Design** : Atoms (InputCore), Molecules (InputField), Composables (useInputActions, useValidation).
- **API factorisée** : toutes les props (HTML, layout, validation, actions, etc.) sont centralisées dans `inputHelper.js` et héritées via `getInputProps`.
- **Extensible** : ajout d'une prop ou d'une action = 1 seul endroit à modifier.

## 2. Utilisation

### InputField (Molecule)
- Toutes les props utiles (`label`, `validation`, `actions`, etc.) sont héritées automatiquement.
- Slots : `overStart`, `overEnd`, `labelTop`, `labelBottom`, etc.

#### Exemple
```vue
<InputField
  v-model="email"
  label="Email"
  :actions="['reset', 'clear']"
  :validation="{ state: 'error', message: 'Email invalide' }"
/>
```

### useInputActions
- Centralise la logique des actions contextuelles (reset, clear, copy, etc.).
- Expose : `currentValue`, `actionsToDisplay`, handlers (`reset`, `clear`, ...).

### useValidation
- Centralise la validation locale et l'intégration notifications.
- Exemples :
```js
validateField('email', { state: 'error', message: 'Email invalide' });
handleServerErrors(errors);
```

## 3. Bonnes pratiques
- Utiliser InputField pour bénéficier de la factorisation et des actions/validations automatiques.
- Ne jamais redéclarer une prop déjà factorisée dans inputHelper.js.
- Utiliser les slots pour personnaliser l’UI.

## 4. Liens utiles
- [DaisyUI - Input](https://daisyui.com/components/input/)
- [DaisyUI - Validator](https://daisyui.com/components/validator/)
- [Tailwind CSS](https://tailwindcss.com/docs/utility-first) 