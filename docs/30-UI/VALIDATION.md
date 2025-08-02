# Validation — Guide KrosmozJDR

## 1. Architecture du système

### Composants principaux
- **useValidation (Composable)** : Interface réactive simplifiée pour la gestion des validations
- **Validator (Atom)** : Affichage des messages de validation avec classes DaisyUI
- **InputField (Molecule)** : Intégration automatique via la prop `validation`

### API simplifiée
Le système de validation utilise maintenant une API simple et transparente :
```javascript
{
  condition: Function|RegExp|String,  // Condition de validation
  messages: {                         // Messages par état avec contrôle des notifications
    success: { text: 'Valide', notified: false },
    error: { text: 'Erreur', notified: true },
    warning: { text: 'Attention', notified: false },
    info: { text: 'Info', notified: false }
  }
}
```

## 2. API complète

### useValidation (Composable)
```javascript
import { useValidation } from '@/Composables/form/useValidation';

const validation = useValidation({
  value: currentValue,
  condition: (val) => val.length >= 8,
  messages: {
    success: { text: 'Mot de passe sécurisé', notified: false },
    error: { text: 'Minimum 8 caractères', notified: true }
  },
  validateOnChange: false,
  validateOnBlur: true
});

// API retournée
const {
  state,           // État actuel (success, error, warning, info, '')
  message,         // Message actuel
  hasInteracted,   // L'utilisateur a-t-il interagi ?
  validate,        // Fonction de validation
  setInteracted,   // Marquer comme interagi
  reset,           // Réinitialiser
  isValid,         // Helper boolean
  hasError,        // Helper boolean
  hasWarning,      // Helper boolean
  hasSuccess,      // Helper boolean
  hasInfo          // Helper boolean
} = validation;
```

### Validator (Atom)
- Props : `state` (error, success, warning, info), `message`, `visible`
- Classes DaisyUI : `validator-hint`, `text-error`, `text-success`, etc.

## 3. Exemples d'utilisation

### Validation simple avec condition
```vue
<InputField
  v-model="email"
  label="Email"
  :validation="'email'"
/>
```

### Validation avec condition personnalisée
```vue
<InputField
  v-model="password"
  label="Mot de passe"
  :validation="(val) => val.length >= 8"
/>
```

### Validation avec messages personnalisés
```vue
<InputField
  v-model="username"
  label="Nom d'utilisateur"
  :validation="{
    condition: (val) => val.length >= 3,
    messages: {
      success: { text: 'Nom valide', notified: false },
      error: { text: 'Minimum 3 caractères', notified: true }
    }
  }"
/>
```

### Validation avec regex
```vue
<InputField
  v-model="phone"
  label="Téléphone"
  :validation="{
    condition: /^[0-9]{10}$/,
    messages: {
      success: { text: 'Format valide', notified: false },
      error: { text: 'Format invalide (10 chiffres)', notified: true }
    }
  }"
/>
```

### Validation avec notification automatique
```vue
<InputField
  v-model="email"
  label="Email"
  :validation="{
    condition: 'email',
    messages: {
      success: { text: 'Email valide !', notified: true },
      error: { text: 'Format d\'email invalide', notified: true }
    }
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
  :validation="{
    condition: (val) => val === password,
    messages: {
      success: { text: 'Mots de passe identiques', notified: false },
      error: { text: 'Les mots de passe ne correspondent pas', notified: true }
    }
  }"
/>
```

### Utilisation directe du composable
```javascript
// Dans un composant
const emailValidation = useValidation({
  value: email,
  condition: 'email',
  messages: {
    success: { text: 'Email valide', notified: false },
    error: { text: 'Email invalide', notified: true }
  }
});

// Validation manuelle
const state = emailValidation.validate(email.value);

// Écouter l'état
watch(emailValidation.state, (newState) => {
  console.log('État de validation:', newState);
});
```

## 4. Conditions de validation prêtes à l'emploi

### Conditions string
```javascript
// Conditions disponibles
'required'    // Champ requis
'email'       // Format email
'password'    // Validation mot de passe (warning si faible)
```

### Conditions regex
```javascript
// Exemples de regex
/^[0-9]{10}$/           // 10 chiffres
/^[a-zA-Z]+$/           // Lettres uniquement
/^[a-zA-Z0-9]{3,20}$/   // Alphanumérique 3-20 caractères
```

### Conditions fonction
```javascript
// Fonctions personnalisées
(val) => val.length >= 8                    // Longueur minimale
(val) => /[A-Z]/.test(val)                  // Contient majuscule
(val) => val === otherValue                 // Égalité
(val) => val >= 0 && val <= 100             // Plage de valeurs
(val) => val && val.trim().length > 0       // Non vide après trim
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
  :validation="{ state: 'error', message: 'Erreur' }"
/>
```

### Nouvelle API (recommandée)
```vue
<!-- Condition simple -->
<InputField :validation="'email'" />

<!-- Condition avec messages -->
<InputField 
  :validation="{
    condition: 'email',
    messages: {
      success: { text: 'Email valide', notified: false },
      error: { text: 'Email invalide', notified: true }
    }
  }"
/>

<!-- Compatibilité avec l'ancienne API -->
<InputField 
  :validation="{ state: 'error', message: 'Erreur', showNotification: true }"
/>
```

## 6. Bonnes pratiques

### Structure des messages
```javascript
// Structure recommandée
messages: {
  success: { text: 'Message de succès', notified: false },
  error: { text: 'Message d\'erreur', notified: true },
  warning: { text: 'Message d\'avertissement', notified: false },
  info: { text: 'Message d\'information', notified: false }
}
```

### Contrôle des notifications
- **`notified: false`** : Pour les validations en temps réel (évite le spam)
- **`notified: true`** : Pour les erreurs importantes ou confirmations
- **Notifications automatiques** : Le système utilise le type correspondant à l'état

### Performance
- Utiliser `computed` pour les validations réactives
- Éviter les validations inutiles avec `notified: false`
- Utiliser `reset()` pour nettoyer l'état

### Transparence
- Le validateur lit le contenu sans bloquer le v-model
- Compatible avec toutes les logiques existantes
- Aucun conflit avec les vues ou autres composables

### Cohérence
- Toujours utiliser la prop `validation` unifiée
- Préférer les conditions simples (string, regex, fonction)
- Documenter les validations complexes avec des exemples 