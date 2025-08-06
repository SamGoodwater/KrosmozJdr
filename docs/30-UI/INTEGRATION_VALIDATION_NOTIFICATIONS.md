# Intégration Validation + Notifications — Guide KrosmozJDR

## 1. Vue d'ensemble

Ce guide explique comment utiliser ensemble les systèmes de validation et de notifications pour créer une expérience utilisateur cohérente et informative.

## 2. Architecture intégrée

### Flux de données
```
InputField → validation prop → validationManager → useNotificationStore → NotificationContainer
```

### Composants impliqués
- **InputField** : Point d'entrée avec prop `validation`
- **validationManager** : Traitement de la validation
- **useNotificationStore** : Gestion des notifications
- **NotificationContainer** : Affichage des notifications

## 3. Cas d'usage courants

### Validation avec notification automatique
```vue
<InputField 
  label="Email" 
  v-model="email"
  :validation="{ 
    state: 'success', 
    message: 'Email valide !',
    showNotification: true,
    notificationType: 'success',
    notificationDuration: 3000
  }"
/>
```

### Validation d'erreur avec notification
```vue
<InputField 
  label="Mot de passe" 
  v-model="password"
  type="password"
  :validation="{ 
    state: 'error', 
    message: 'Le mot de passe doit contenir au moins 8 caractères',
    showNotification: true,
    notificationType: 'error',
    notificationDuration: 5000
  }"
/>
```

### Validation conditionnelle
```vue
<script setup>
const emailValidation = computed(() => {
  if (!email.value) return null;
  
  if (!isValidEmail(email.value)) {
    return {
      state: 'error',
      message: 'Format d\'email invalide',
      showNotification: true,
      notificationType: 'error'
    };
  }
  
  return {
    state: 'success',
    message: 'Email valide',
    showNotification: true,
    notificationType: 'success'
  };
});
</script>

<template>
  <InputField 
    label="Email" 
    v-model="email"
    :validation="emailValidation"
  />
</template>
```

## 4. Patterns avancés

### Validation de formulaire complet
```vue
<script setup>
import { useValidation } from '@/Composables/form/useValidation';

const { validateForm, handleServerErrors } = useValidation();

const formData = reactive({
  email: '',
  password: '',
  confirmPassword: ''
});

const formValidation = computed(() => {
  const validations = {};
  
  // Validation email
  if (formData.email && !isValidEmail(formData.email)) {
    validations.email = {
      state: 'error',
      message: 'Email invalide',
      showNotification: true
    };
  }
  
  // Validation mot de passe
  if (formData.password && formData.password.length < 8) {
    validations.password = {
      state: 'error',
      message: 'Mot de passe trop court',
      showNotification: true
    };
  }
  
  // Validation confirmation
  if (formData.confirmPassword && formData.password !== formData.confirmPassword) {
    validations.confirmPassword = {
      state: 'error',
      message: 'Mots de passe différents',
      showNotification: true
    };
  }
  
  return validations;
});

async function submitForm() {
  try {
    await api.submitForm(formData);
    
    // Succès global
    success('Formulaire envoyé avec succès !');
    
  } catch (error) {
    // Erreurs serveur
    handleServerErrors(error.response.data.errors, {
      showNotifications: true,
      notificationDuration: 8000
    });
  }
}
</script>

<template>
  <form @submit.prevent="submitForm">
    <InputField 
      label="Email" 
      v-model="formData.email"
      :validation="formValidation.email"
    />
    
    <InputField 
      label="Mot de passe" 
      v-model="formData.password"
      type="password"
      :validation="formValidation.password"
    />
    
    <InputField 
      label="Confirmer le mot de passe" 
      v-model="formData.confirmPassword"
      type="password"
      :validation="formValidation.confirmPassword"
    />
    
    <Btn type="submit" variant="primary">
      Envoyer
    </Btn>
  </form>
</template>
```

### Validation en temps réel
```vue
<script setup>
import { debounce } from 'lodash-es';

const email = ref('');
const emailValidation = ref(null);

// Validation debounced pour éviter trop de notifications
const validateEmail = debounce(async (value) => {
  if (!value) {
    emailValidation.value = null;
    return;
  }
  
  try {
    const isAvailable = await api.checkEmailAvailability(value);
    
    emailValidation.value = {
      state: isAvailable ? 'success' : 'error',
      message: isAvailable ? 'Email disponible' : 'Email déjà utilisé',
      showNotification: true,
      notificationType: isAvailable ? 'success' : 'error'
    };
  } catch (error) {
    emailValidation.value = {
      state: 'warning',
      message: 'Impossible de vérifier la disponibilité',
      showNotification: true,
      notificationType: 'warning'
    };
  }
}, 500);

watch(email, validateEmail);
</script>

<template>
  <InputField 
    label="Email" 
    v-model="email"
    :validation="emailValidation"
  />
</template>
```

## 5. Migration depuis l'ancienne API

### Ancienne approche (dépréciée)
```vue
<InputField 
  :validator="'Erreur'"
  :validatorError="'Email invalide'"
  :validatorSuccess="'Email valide'"
/>

<script setup>
// Notifications séparées
const { success, error } = useNotificationStore();
success('Opération réussie');
error('Erreur survenue');
</script>
```

### Nouvelle approche (recommandée)
```vue
<InputField 
  :validation="{ 
    state: 'error', 
    message: 'Email invalide',
    showNotification: true 
  }"
/>
```

## 6. Bonnes pratiques

### Quand utiliser les notifications
- ✅ **Validation critique** : Erreurs bloquantes, succès importants
- ✅ **Feedback utilisateur** : Confirmation d'actions, états de chargement
- ✅ **Messages globaux** : Maintenance, mises à jour

### Quand ne pas utiliser les notifications
- ❌ **Validation locale** : Utiliser seulement le Validator
- ❌ **Messages fréquents** : Éviter le spam
- ❌ **Debug** : Utiliser la console

### Configuration recommandée
```javascript
// Durées par type
const notificationDurations = {
  success: 3000,    // Court pour les succès
  error: 8000,      // Plus long pour les erreurs
  warning: 5000,    // Moyen pour les avertissements
  info: 4000        // Moyen pour les informations
};

// Placements par contexte
const notificationPlacements = {
  form: 'bottom-right',    // Formulaires
  action: 'top-right',     // Actions utilisateur
  system: 'top-center'     // Messages système
};
```

## 7. Dépannage

### Problèmes courants

#### Notifications qui ne s'affichent pas
```javascript
// Vérifier que NotificationContainer est présent
<NotificationContainer />

// Vérifier que le store est injecté
const notificationStore = inject('notificationStore');
```

#### Validations qui ne déclenchent pas de notifications
```vue
<!-- Vérifier showNotification: true -->
<InputField 
  :validation="{ 
    state: 'error', 
    message: 'Erreur',
    showNotification: true  // ← Important !
  }"
/>
```

#### Notifications multiples
```javascript
// Utiliser debounce pour éviter le spam
const debouncedValidation = debounce(validateField, 500);
```

## 8. Liens utiles
- [Validation](./VALIDATION.md) : Guide complet de la validation
- [Notifications](./NOTIFICATIONS.md) : Guide complet des notifications
- [Inputs](./INPUTS.md) : Guide des composants d'input 