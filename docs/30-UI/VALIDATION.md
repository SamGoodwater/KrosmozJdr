# Validation — Guide KrosmozJDR

## 1. Fonctionnement général

- **useValidation (Composable)** : centralise la gestion de la validation des champs et du formulaire, expose une API factorisée et réactive.
- **Validator (Atom)** : affiche un message d'aide/erreur sous un champ input avec la classe DaisyUI `validator-hint`.
- **Props de validation factorisées** : toutes les props de validation (`validation`, `validator`, `errorMessage`, etc.) sont centralisées dans `inputHelper.js` et héritées automatiquement par InputField via `getInputProps('input', 'field')`.

## 2. API

### useValidation
- `validateField(fieldName, validation)`
- `handleServerErrors(errors, options)`
- Helpers rapides : `quickValidation.local`, `quickValidation.withNotification`

### Validator
- Props : `state` (error, success, warning, info), `message`, `visible`

## 3. Exemples d’intégration

```vue
<InputField
  v-model="email"
  label="Email"
  :validation="{ state: 'error', message: 'Email invalide' }"
/>
```

```js
import { useValidation } from '@/Composables/form/useValidation';
const { validateField, handleServerErrors } = useValidation();
validateField('email', { state: 'error', message: 'Email invalide' });
```

## 4. Migration
Ancienne API (dépréciée) :
```vue
<InputField :validator="'Erreur'" :validator="true" :errorMessage="'Erreur'" />
```
Nouvelle API (recommandée) :
```vue
<InputField :validation="{ state: 'error', message: 'Erreur' }" />
```

## 5. Bonnes pratiques
- Utiliser la validation factorisée via InputField et inputHelper.js
- Utiliser les helpers pour un code plus lisible
- Documenter chaque usage spécifique 