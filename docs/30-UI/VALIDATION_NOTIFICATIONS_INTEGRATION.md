# Validation & Notifications — Intégration Avancée

## 1. Vue d'ensemble

Ce guide couvre les cas d'usage avancés et les patterns d'intégration entre les systèmes de validation et de notifications.

## 2. Patterns d'intégration

### Pattern 1 : Validation en temps réel avec debounce
```vue
<script setup>
import { debounce } from 'lodash-es';

const email = ref('');
const emailValidation = ref(null);

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
      showNotification: isAvailable, // Notification seulement si disponible
      notificationType: isAvailable ? 'success' : 'error'
    };
  } catch (error) {
    emailValidation.value = {
      state: 'warning',
      message: 'Impossible de vérifier la disponibilité',
      showNotification: false // Pas de notification pour les erreurs techniques
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

### Pattern 2 : Validation de formulaire avec gestion d'état
```vue
<script setup>
import { useValidation } from '@/Composables/form/useValidation';

const { validateForm, handleServerErrors } = useValidation();

const formData = reactive({
  email: '',
  password: '',
  confirmPassword: ''
});

const formState = reactive({
  isSubmitting: false,
  hasErrors: false
});

const formValidation = computed(() => {
  const validations = {};
  
  // Validation email
  if (formData.email && !isValidEmail(formData.email)) {
    validations.email = {
      state: 'error',
      message: 'Email invalide',
      showNotification: false // Pas de notification pour validation locale
    };
  }
  
  // Validation mot de passe
  if (formData.password && formData.password.length < 8) {
    validations.password = {
      state: 'error',
      message: 'Mot de passe trop court',
      showNotification: false
    };
  }
  
  // Validation confirmation
  if (formData.confirmPassword && formData.password !== formData.confirmPassword) {
    validations.confirmPassword = {
      state: 'error',
      message: 'Mots de passe différents',
      showNotification: false
    };
  }
  
  return validations;
});

async function submitForm() {
  formState.isSubmitting = true;
  formState.hasErrors = false;
  
  try {
    await api.submitForm(formData);
    
    // Succès global avec notification
    success('Formulaire envoyé avec succès !', {
      duration: 5000,
      placement: 'top-center'
    });
    
  } catch (error) {
    formState.hasErrors = true;
    
    // Erreurs serveur avec notifications
    handleServerErrors(error.response.data.errors, {
      showNotifications: true,
      notificationDuration: 8000,
      notificationPlacement: 'top-right'
    });
  } finally {
    formState.isSubmitting = false;
  }
}
</script>

<template>
  <form @submit.prevent="submitForm">
    <InputField 
      label="Email" 
      v-model="formData.email"
      :validation="formValidation.email"
      :disabled="formState.isSubmitting"
    />
    
    <InputField 
      label="Mot de passe" 
      v-model="formData.password"
      type="password"
      :validation="formValidation.password"
      :disabled="formState.isSubmitting"
    />
    
    <InputField 
      label="Confirmer le mot de passe" 
      v-model="formData.confirmPassword"
      type="password"
      :validation="formValidation.confirmPassword"
      :disabled="formState.isSubmitting"
    />
    
    <Btn 
      type="submit" 
      variant="primary"
      :loading="formState.isSubmitting"
      :disabled="formState.hasErrors"
    >
      Envoyer
    </Btn>
  </form>
</template>
```

### Pattern 3 : Validation conditionnelle avec notifications intelligentes
```vue
<script setup>
const password = ref('');
const confirmPassword = ref('');

const passwordValidation = computed(() => {
  if (!password.value) return null;
  
  const hasMinLength = password.value.length >= 8;
  const hasLetter = /[a-zA-Z]/.test(password.value);
  const hasNumber = /\d/.test(password.value);
  const hasSpecial = /[!@#$%^&*]/.test(password.value);
  
  const validations = [];
  if (!hasMinLength) validations.push('8 caractères minimum');
  if (!hasLetter) validations.push('au moins une lettre');
  if (!hasNumber) validations.push('au moins un chiffre');
  if (!hasSpecial) validations.push('au moins un caractère spécial');
  
  if (validations.length > 0) {
    return {
      state: 'error',
      message: `Mot de passe invalide : ${validations.join(', ')}`,
      showNotification: false // Pas de notification pour validation locale
    };
  }
  
  return {
    state: 'success',
    message: 'Mot de passe valide',
    showNotification: true, // Notification pour confirmation
    notificationType: 'success',
    notificationDuration: 3000
  };
});

const confirmPasswordValidation = computed(() => {
  if (!confirmPassword.value) return null;
  
  if (password.value !== confirmPassword.value) {
    return {
      state: 'error',
      message: 'Mots de passe différents',
      showNotification: true, // Notification pour erreur critique
      notificationType: 'error',
      notificationDuration: 5000
    };
  }
  
  return {
    state: 'success',
    message: 'Mots de passe identiques',
    showNotification: true,
    notificationType: 'success',
    notificationDuration: 3000
  };
});
</script>

<template>
  <InputField 
    label="Mot de passe" 
    v-model="password"
    type="password"
    :validation="passwordValidation"
    helper="Minimum 8 caractères avec lettre, chiffre et caractère spécial"
  />
  
  <InputField 
    label="Confirmer le mot de passe" 
    v-model="confirmPassword"
    type="password"
    :validation="confirmPasswordValidation"
  />
</template>
```

## 3. Gestion des erreurs serveur

### Pattern 1 : Mapping automatique des erreurs
```javascript
import { useValidation } from '@/Composables/form/useValidation';

const { handleServerErrors } = useValidation();

async function submitForm() {
  try {
    await api.submitForm(formData);
    success('Formulaire envoyé !');
  } catch (error) {
    // Mapping automatique des erreurs Laravel/Inertia
    handleServerErrors(error.response.data.errors, {
      showNotifications: true,
      notificationDuration: 8000,
      notificationPlacement: 'top-right',
      // Mapping personnalisé
      fieldMapping: {
        'user.email': 'email',
        'user.password': 'password',
        'profile.name': 'name'
      }
    });
  }
}
```

### Pattern 2 : Gestion d'erreurs avec retry
```javascript
async function submitWithRetry(data, maxRetries = 3) {
  let attempts = 0;
  
  while (attempts < maxRetries) {
    try {
      await api.submitForm(data);
      success('Formulaire envoyé avec succès !');
      return;
    } catch (error) {
      attempts++;
      
      if (attempts === maxRetries) {
        // Dernière tentative échouée
        error('Échec de l\'envoi après plusieurs tentatives', {
          duration: 10000,
          actions: [
            {
              label: 'Réessayer',
              onClick: () => submitWithRetry(data, maxRetries),
              variant: 'primary'
            }
          ]
        });
        return;
      }
      
      // Tentative échouée, notification d'avertissement
      warning(`Tentative ${attempts}/${maxRetries} échouée, nouvelle tentative...`, {
        duration: 3000
      });
      
      // Attendre avant de réessayer
      await new Promise(resolve => setTimeout(resolve, 1000 * attempts));
    }
  }
}
```

## 4. Notifications contextuelles

### Pattern 1 : Notifications selon le contexte
```javascript
// Notifications pour formulaires
function showFormNotification(type, message) {
  const options = {
    placement: 'bottom-right', // Position adaptée aux formulaires
    duration: type === 'error' ? 8000 : 4000
  };
  
  switch (type) {
    case 'success':
      success(message, options);
      break;
    case 'error':
      error(message, options);
      break;
    case 'warning':
      warning(message, options);
      break;
  }
}

// Notifications pour actions utilisateur
function showActionNotification(type, message) {
  const options = {
    placement: 'top-right', // Position adaptée aux actions
    duration: type === 'error' ? 6000 : 3000
  };
  
  switch (type) {
    case 'success':
      success(message, options);
      break;
    case 'error':
      error(message, options);
      break;
  }
}
```

### Pattern 2 : Notifications avec actions
```javascript
function showDeleteConfirmation(itemName) {
  warning(`Êtes-vous sûr de vouloir supprimer "${itemName}" ?`, {
    duration: 0, // Permanent jusqu'à action
    actions: [
      {
        label: 'Supprimer',
        onClick: () => deleteItem(itemName),
        variant: 'error'
      },
      {
        label: 'Annuler',
        onClick: () => removeNotification(),
        variant: 'ghost'
      }
    ]
  });
}

function showUpdateAvailable() {
  info('Une mise à jour est disponible', {
    duration: 0,
    actions: [
      {
        label: 'Installer',
        onClick: () => installUpdate(),
        variant: 'primary'
      },
      {
        label: 'Plus tard',
        onClick: () => removeNotification(),
        variant: 'ghost'
      }
    ]
  });
}
```

## 5. Bonnes pratiques avancées

### Gestion de la concurrence
```javascript
// Éviter les notifications multiples
let notificationId = null;

function showUniqueNotification(type, message) {
  // Supprimer la notification précédente
  if (notificationId) {
    removeNotification(notificationId);
  }
  
  // Créer la nouvelle notification
  const id = type(message, {
    duration: 5000
  });
  
  notificationId = id;
}
```

### Notifications avec progression
```javascript
function showProgressNotification(message, progress) {
  const notificationId = info(`${message} (${progress}%)`, {
    duration: 0,
    actions: [
      {
        label: 'Annuler',
        onClick: () => cancelOperation(),
        variant: 'ghost'
      }
    ]
  });
  
  // Mettre à jour la progression
  updateNotification(notificationId, {
    message: `${message} (${progress}%)`
  });
  
  return notificationId;
}
```

### Notifications avec timeout
```javascript
function showTemporaryNotification(type, message, timeout = 5000) {
  const id = type(message, {
    duration: timeout
  });
  
  // Action automatique après timeout
  setTimeout(() => {
    removeNotification(id);
  }, timeout);
  
  return id;
}
```

## 6. Dépannage avancé

### Problèmes de performance
```javascript
// Debounce pour éviter trop de notifications
const debouncedNotification = debounce((type, message) => {
  type(message);
}, 300);

// Throttle pour les notifications fréquentes
const throttledNotification = throttle((type, message) => {
  type(message);
}, 1000);
```

### Gestion de la mémoire
```javascript
// Nettoyer les notifications lors du changement de page
onBeforeUnmount(() => {
  clearAllNotifications();
});

// Limiter le nombre de notifications
const MAX_NOTIFICATIONS = 5;

function showLimitedNotification(type, message) {
  const notifications = getAllNotifications();
  
  if (notifications.length >= MAX_NOTIFICATIONS) {
    // Supprimer la plus ancienne
    removeNotification(notifications[0].id);
  }
  
  type(message);
}
```

## 7. Liens utiles
- [Validation](./VALIDATION.md) : Guide complet de la validation
- [Notifications](./NOTIFICATIONS.md) : Guide complet des notifications
- [Intégration Validation + Notifications](./INTEGRATION_VALIDATION_NOTIFICATIONS.md) : Cas d'usage courants 