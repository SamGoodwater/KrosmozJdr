# Validation — Guide KrosmozJDR

## 1. Architecture du système

### Composants principaux
- **validationManager.js** : Gestionnaire centralisé des validations avec API unifiée
- **useValidation (Composable)** : Interface réactive pour la gestion des validations
- **Validator (Atom)** : Affichage des messages de validation avec classes DaisyUI
- **InputField (Molecule)** : Intégration automatique via la prop `validation`

### API unifiée
Toutes les validations utilisent maintenant une seule prop `validation` avec une structure claire :
```javascript
{
  state: 'error' | 'success' | 'warning' | 'info',
  message: 'Message à afficher',
  showNotification: true | false,
  notificationType: 'auto' | 'error' | 'success' | 'warning' | 'info' | 'primary' | 'secondary',
  notificationDuration: 5000, // ms
  notificationPlacement: null // null = position par défaut du système
}
```

## 2. API complète

### validationManager.js
```javascript
import { 
  createValidation, 
  processValidation, 
  quickValidation,
  validateSameAs,
  createSameAsValidation 
} from '@/Utils/atomic-design/validationManager';

// Créer une validation
const validation = createValidation({ state: 'error', message: 'Erreur' });

// Helpers rapides
const error = quickValidation.local.error('Message');
const success = quickValidation.withNotification.success('Succès !');

// Validation sameAs
const sameAsValidation = validateSameAs(password, passwordConfirm, 'Mots de passe différents');
```

### useValidation (Composable)
```javascript
import { useValidation } from '@/Composables/form/useValidation';

const { 
  validateField, 
  validateForm, 
  handleServerErrors,
  clearFieldValidation,
  clearAllValidations,
  errorCount,
  successCount,
  isValid,
  errors,
  successes,
  // Helpers rapides
  setFieldError,
  setFieldSuccess,
  setFieldErrorWithNotification,
  setFieldSuccessWithNotification
} = useValidation();
```

### Validator (Atom)
- Props : `state` (error, success, warning, info), `message`, `visible`
- Classes DaisyUI : `validator-hint`, `text-error`, `text-success`, etc.

## 3. Exemples d'utilisation

### Validation simple
```vue
<InputField
  v-model="email"
  label="Email"
  :validation="{ state: 'error', message: 'Email invalide' }"
/>
```

### Validation avec notification automatique
```vue
<InputField
  v-model="email"
  label="Email"
  :validation="{ 
    state: 'success', 
    message: 'Email valide !',
    showNotification: true,
    notificationType: 'auto' // Utilise automatiquement 'success'
  }"
/>
```

### Validation sameAs (confirmation mot de passe)
```vue
<InputField
  v-model="password"
  label="Mot de passe"
  type="password"
/>
<InputField
  v-model="passwordConfirm"
  label="Confirmer le mot de passe"
  type="password"
  :validation="passwordConfirmationValidation"
/>

<script setup>
const passwordConfirmationValidation = computed(() => {
  if (!passwordConfirm.value) return null;
  
  if (password.value !== passwordConfirm.value) {
    return { 
      state: 'error', 
      message: 'Les mots de passe ne correspondent pas' 
    };
  }
  
  return { 
    state: 'success', 
    message: 'Mots de passe identiques' 
  };
});
</script>
```

### Utilisation du composable
```javascript
// Validation d'un champ
validateField('email', { state: 'error', message: 'Email invalide' });

// Validation de formulaire
validateForm({
  email: { state: 'error', message: 'Email requis' },
  password: { state: 'error', message: 'Mot de passe requis' }
});

// Helpers rapides
setFieldError('email', 'Email invalide');
setFieldSuccess('email', 'Email valide !');
setFieldErrorWithNotification('email', 'Erreur importante !');

// Gestion des erreurs serveur
handleServerErrors(form.errors, {
  showNotifications: true,
  notificationDuration: 8000
});
```

## 4. Règles de validation communes

Le système inclut des règles prêtes à l'emploi :

```javascript
import { quickValidation } from '@/Utils/atomic-design/validationManager';

// Règles disponibles
quickValidation.rules.required(value)
quickValidation.rules.email(value)
quickValidation.rules.minLength(8)(value)
quickValidation.rules.maxLength(50)(value)
quickValidation.rules.pattern(/^[a-zA-Z]+$/)(value)
quickValidation.rules.numeric(value)
quickValidation.rules.integer(value)
quickValidation.rules.positive(value)
quickValidation.rules.between(1, 100)(value)
quickValidation.rules.min(0)(value)
quickValidation.rules.max(100)(value)
quickValidation.rules.sameAs(otherValue)(value)
quickValidation.rules.sameAsField('password')(value, formData)
quickValidation.rules.includeLetter(value)
quickValidation.rules.includeNumber(value)
quickValidation.rules.includeSpecialChar(value)
quickValidation.rules.includeUppercase(value)
quickValidation.rules.includeLowercase(value)
quickValidation.rules.url(value)
```

## 5. Migration depuis l'ancienne API

### Ancienne API (dépréciée)
```vue
<InputField 
  :validator="'Erreur'"
  :validator="true"
  :errorMessage="'Erreur'"
  :validatorError="'Email invalide'"
  :validatorSuccess="'Email valide'"
/>
```

### Nouvelle API (recommandée)
```vue
<InputField 
  :validation="{ state: 'error', message: 'Erreur' }"
  :validation="{ state: 'success', message: 'Champ valide' }"
  :validation="{ 
    state: 'error', 
    message: 'Email invalide',
    showNotification: true,
    notificationType: 'auto'
  }"
/>
```

## 6. Bonnes pratiques

### Configuration des notifications
- **`notificationType: 'auto'`** : Utilise automatiquement le type correspondant au state
- **`notificationPlacement: null`** : Laisse le système gérer la position par défaut
- **`showNotification: false`** : Pour les validations en temps réel (évite le spam)

### Performance
- Utiliser `computed` pour les validations réactives
- Éviter les validations inutiles avec `showNotification: false`
- Utiliser `clearAllValidations()` pour nettoyer l'état

### Cohérence
- Toujours utiliser la prop `validation` unifiée
- Préférer les helpers rapides (`quickValidation.local`, `quickValidation.withNotification`)
- Documenter les validations complexes avec des exemples 