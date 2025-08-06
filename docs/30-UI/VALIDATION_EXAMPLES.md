# Exemples de Validation — KrosmozJDR

Ce fichier contient des exemples pratiques d'utilisation de la nouvelle API de validation simplifiée.

## 1. Validations simples

### Validation email
```vue
<InputField
  v-model="email"
  label="Email"
  :validation="'email'"
/>
```

### Validation champ requis
```vue
<InputField
  v-model="name"
  label="Nom"
  :validation="'required'"
/>
```

### Validation avec regex
```vue
<InputField
  v-model="phone"
  label="Téléphone"
  :validation="/^[0-9]{10}$/"
/>
```

## 2. Validations avec messages personnalisés

### Validation email avec messages
```vue
<InputField
  v-model="email"
  label="Email"
  :validation="{
    condition: 'email',
    messages: {
      success: { text: 'Email valide', notified: false },
      error: { text: 'Format d\'email invalide', notified: true }
    }
  }"
/>
```

### Validation longueur avec messages
```vue
<InputField
  v-model="username"
  label="Nom d'utilisateur"
  :validation="{
    condition: (val) => val.length >= 3,
    messages: {
      success: { text: 'Nom valide', notified: false },
      error: { text: 'Minimum 3 caractères', notified: false }
    }
  }"
/>
```

## 3. Validations complexes

### Validation mot de passe avec niveaux
```vue
<InputField
  v-model="password"
  label="Mot de passe"
  type="password"
  :validation="{
    condition: (val) => {
      if (val.length < 8) return 'error';
      
      const hasUpperCase = /[A-Z]/.test(val);
      const hasLowerCase = /[a-z]/.test(val);
      const hasNumbers = /\d/.test(val);
      const hasSpecial = /[!@#$%^&*]/.test(val);
      
      if (hasUpperCase && hasLowerCase && hasNumbers && hasSpecial) {
        return 'success';
      }
      
      return 'warning';
    },
    messages: {
      success: { text: 'Mot de passe très sécurisé !', notified: false },
      warning: { text: 'Le mot de passe pourrait être plus sécurisé', notified: false },
      error: { text: 'Minimum 8 caractères', notified: true }
    }
  }"
/>
```

### Validation confirmation mot de passe
```vue
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

## 4. Validations avec notifications

### Validation avec notification de succès
```vue
<InputField
  v-model="email"
  label="Email"
  :validation="{
    condition: 'email',
    messages: {
      success: { text: 'Email valide !', notified: true },
      error: { text: 'Email invalide', notified: true }
    }
  }"
/>
```

### Validation avec notification d'erreur uniquement
```vue
<InputField
  v-model="username"
  label="Nom d'utilisateur"
  :validation="{
    condition: (val) => val.length >= 3,
    messages: {
      success: { text: 'Nom valide', notified: false },
      error: { text: 'Nom trop court', notified: true }
    }
  }"
/>
```

## 5. Validations conditionnelles

### Validation selon le type d'identifiant
```vue
<InputField
  v-model="identifier"
  label="Email ou pseudo"
  :validation="{
    condition: (val) => {
      if (val.includes('@')) {
        // Validation email
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
      } else {
        // Validation pseudo
        return val.length >= 3;
      }
    },
    messages: {
      success: { text: 'Identifiant valide', notified: false },
      error: { text: 'Format invalide', notified: false }
    }
  }"
/>
```

### Validation selon la valeur d'un autre champ
```vue
<InputField
  v-model="confirmEmail"
  label="Confirmer l'email"
  :validation="{
    condition: (val) => val === email && val.length > 0,
    messages: {
      success: { text: 'Emails identiques', notified: false },
      error: { text: 'Les emails ne correspondent pas', notified: true }
    }
  }"
/>
```

## 6. Utilisation directe du composable

### Dans un composant Vue
```javascript
import { useValidation } from '@/Composables/form/useValidation';

// Dans setup()
const emailValidation = useValidation({
  value: email,
  condition: 'email',
  messages: {
    success: { text: 'Email valide', notified: false },
    error: { text: 'Email invalide', notified: true }
  }
});

// Écouter l'état
watch(emailValidation.state, (newState) => {
  console.log('État de validation:', newState);
});

// Validation manuelle
const state = emailValidation.validate(email.value);
```

### Validation de formulaire complet
```javascript
// Validation de plusieurs champs
const formValidations = {
  email: useValidation({
    value: form.email,
    condition: 'email',
    messages: {
      success: { text: 'Email valide', notified: false },
      error: { text: 'Email invalide', notified: true }
    }
  }),
  password: useValidation({
    value: form.password,
    condition: (val) => val.length >= 8,
    messages: {
      success: { text: 'Mot de passe sécurisé', notified: false },
      error: { text: 'Minimum 8 caractères', notified: true }
    }
  })
};

// Vérifier si tout le formulaire est valide
const isFormValid = computed(() => {
  return Object.values(formValidations).every(validation => 
    validation.isValid.value
  );
});
```

## 7. Patterns courants

### Validation de plage de valeurs
```vue
<InputField
  v-model="age"
  label="Âge"
  type="number"
  :validation="{
    condition: (val) => val >= 0 && val <= 120,
    messages: {
      success: { text: 'Âge valide', notified: false },
      error: { text: 'Âge invalide (0-120)', notified: true }
    }
  }"
/>
```

### Validation de format spécifique
```vue
<InputField
  v-model="postalCode"
  label="Code postal"
  :validation="{
    condition: /^[0-9]{5}$/,
    messages: {
      success: { text: 'Code postal valide', notified: false },
      error: { text: 'Format invalide (5 chiffres)', notified: true }
    }
  }"
/>
```

### Validation avec transformation
```vue
<InputField
  v-model="phone"
  label="Téléphone"
  :validation="{
    condition: (val) => {
      // Nettoyer le numéro
      const clean = val.replace(/[^0-9]/g, '');
      return clean.length === 10;
    },
    messages: {
      success: { text: 'Numéro valide', notified: false },
      error: { text: 'Format invalide', notified: true }
    }
  }"
/>
```

## 8. Bonnes pratiques

### Éviter les notifications en temps réel
```vue
<!-- ❌ Éviter -->
<InputField
  v-model="email"
  :validation="{
    condition: 'email',
    messages: {
      success: { text: 'Email valide !', notified: true }, // Spam !
      error: { text: 'Email invalide', notified: true }
    }
  }"
/>

<!-- ✅ Préférer -->
<InputField
  v-model="email"
  :validation="{
    condition: 'email',
    messages: {
      success: { text: 'Email valide', notified: false },
      error: { text: 'Email invalide', notified: false }
    }
  }"
/>
```

### Utiliser des computed pour les validations réactives
```javascript
// ✅ Bon
const emailValidation = computed(() => ({
  condition: 'email',
  messages: {
    success: { text: 'Email valide', notified: false },
    error: { text: 'Email invalide', notified: false }
  }
}));

// ❌ Éviter
const emailValidation = {
  condition: 'email',
  messages: { /* ... */ }
};
```

### Grouper les validations similaires
```javascript
// Validation commune pour les mots de passe
const passwordValidation = {
  condition: (val) => val.length >= 8,
  messages: {
    success: { text: 'Mot de passe sécurisé', notified: false },
    error: { text: 'Minimum 8 caractères', notified: true }
  }
};

// Réutiliser
<InputField v-model="password" :validation="passwordValidation" />
<InputField v-model="confirmPassword" :validation="passwordValidation" />
``` 