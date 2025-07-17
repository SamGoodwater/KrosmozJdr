# Intégration Validation + Notifications

## Vue d'ensemble

Ce document explique l'intégration complète entre le système de validation des inputs et le système de notifications toast. Cette intégration permet d'avoir **deux niveaux de feedback** pour les utilisateurs :

1. **Validation locale** : Messages d'erreur/succès directement sous les inputs
2. **Notifications globales** : Toast notifications pour les événements importants

## Architecture

### Composants impliqués

```
InputField (Molecule)
├── InputCore (Atom) - Champ de saisie
├── InputLabel (Atom) - Labels positionnables  
├── Validator (Atom) - Messages de validation locale
└── NotificationContainer (Organism) - Notifications globales
```

### Helpers et composables

- **atomManager.js** : Nouveaux helpers de validation
- **useValidation.js** : Composable pour gestion avancée
- **useNotificationStore.js** : Store des notifications
- **useNotificationProvider.js** : Provider pour injection

## API de Validation

### Nouvelle prop `validation`

L'InputField accepte maintenant une prop `validation` avec une API claire :

```vue
<InputField 
  label="Email" 
  v-model="email"
  :validation="{ 
    state: 'error', 
    message: 'Email invalide',
    showNotification: false 
  }"
/>
```

### Configuration complète

```javascript
{
  state: 'error' | 'success' | 'warning' | 'info',
  message: 'Message à afficher',
  showNotification: true | false,
  notificationType: 'error' | 'success' | 'warning' | 'info' | 'primary' | 'secondary',
  notificationDuration: 5000, // ms
  notificationPlacement: 'top-right' | 'top-left' | 'bottom-right' | 'bottom-left'
}
```

## Cas d'usage

### 1. Validation locale uniquement

Pour les validations en temps réel, format, règles métier locales :

```vue
<InputField 
  label="Email" 
  v-model="email"
  :validation="{ 
    state: 'error', 
    message: 'Format d\'email invalide',
    showNotification: false 
  }"
/>
```

**Quand utiliser :**
- Validation en temps réel
- Format de champ
- Règles métier locales
- Messages d'aide contextuels

### 2. Validation avec notification

Pour les événements importants, réponses serveur, actions significatives :

```vue
<InputField 
  label="Email" 
  v-model="email"
  :validation="{ 
    state: 'success', 
    message: 'Email valide et disponible !',
    showNotification: true,
    notificationType: 'success',
    notificationDuration: 3000
  }"
/>
```

**Quand utiliser :**
- Réponse du serveur
- Actions importantes réussies
- Événements significatifs
- Confirmations d'actions

## Helpers rapides

### quickValidation.local

Pour les validations locales uniquement :

```javascript
import { quickValidation } from '@/Utils/atomic-design/atomManager';

// Validation locale
const validation = quickValidation.local.error('Message d\'erreur');
const validation = quickValidation.local.success('Champ valide');
const validation = quickValidation.local.warning('Attention');
const validation = quickValidation.local.info('Information');
```

### quickValidation.withNotification

Pour les validations avec notification automatique :

```javascript
// Validation avec notification
const validation = quickValidation.withNotification.error('Erreur importante', {
  notificationDuration: 8000,
  notificationPlacement: 'top-left'
});

const validation = quickValidation.withNotification.success('Succès !', {
  notificationDuration: 3000
});
```

## Composable useValidation

### Installation

```javascript
import { useValidation } from '@/Composables/form/useValidation';

const { 
  validateField, 
  validateForm, 
  handleServerErrors,
  clearFieldValidation,
  errorCount,
  isValid 
} = useValidation();
```

### Exemples d'utilisation

#### Validation d'un champ

```javascript
// Validation locale
validateField('email', quickValidation.local.error('Email invalide'));

// Validation avec notification
validateField('email', quickValidation.withNotification.success('Email valide !'));
```

#### Validation de formulaire

```javascript
const validations = {
  email: quickValidation.local.error('Email requis'),
  password: quickValidation.local.error('Mot de passe requis'),
  name: quickValidation.withNotification.success('Nom valide !')
};

validateForm(validations);
```

#### Gestion des erreurs serveur

```javascript
// Dans le callback d'erreur d'un formulaire
form.post(route('user.update'), {
  onError: (errors) => {
    handleServerErrors(errors, {
      showNotifications: true,
      notificationDuration: 8000
    });
  }
});
```

## Intégration dans l'application

### 1. Provider de notifications

Dans `App.vue` ou le composant racine :

```vue
<script setup>
import { useNotificationProvider } from '@/Composables/providers/useNotificationProvider';

// Injecte le store de notifications
useNotificationProvider();
</script>

<template>
  <div>
    <!-- Contenu de l'application -->
    <NotificationContainer />
  </div>
</template>
```

### 2. Utilisation dans les composants

```vue
<script setup>
import { useValidation } from '@/Composables/form/useValidation';
import { quickValidation } from '@/Utils/atomic-design/atomManager';

const { validateField, handleServerErrors } = useValidation();

// Validation en temps réel
function validateEmail() {
  const email = form.email;
  
  if (!email) {
    validateField('email', quickValidation.local.error('Email requis'));
    return false;
  }
  
  if (isValidEmail(email)) {
    validateField('email', quickValidation.withNotification.success('Email valide !'));
  }
}

// Soumission avec gestion d'erreurs
function submit() {
  form.post(route('login'), {
    onError: (errors) => {
      handleServerErrors(errors, {
        showNotifications: true,
        notificationDuration: 8000
      });
    },
    onSuccess: () => {
      validateField('form', quickValidation.withNotification.success('Connexion réussie !'));
    }
  });
}
</script>
```

## Migration depuis l'ancienne API

### Ancienne API (dépréciée)

```vue
<InputField 
  :validator="'Message d\'erreur'"
  :validator="true"
  :errorMessage="'Erreur'"
/>
```

### Nouvelle API (recommandée)

```vue
<InputField 
  :validation="{ state: 'error', message: 'Message d\'erreur' }"
  :validation="{ state: 'success', message: 'Champ valide' }"
  :validation="{ state: 'error', message: 'Erreur', showNotification: true }"
/>
```

## Bonnes pratiques

### 1. Quand utiliser la validation locale

- **Validation en temps réel** : Format, longueur, caractères spéciaux
- **Messages d'aide contextuels** : Conseils, exemples
- **Feedback immédiat** : Pendant la saisie
- **Erreurs de format** : Email invalide, mot de passe faible

### 2. Quand utiliser les notifications

- **Réponses serveur** : Erreurs d'authentification, conflits
- **Actions importantes** : Sauvegarde réussie, suppression confirmée
- **Événements significatifs** : Connexion réussie, déconnexion
- **Messages globaux** : Maintenance, mises à jour

### 3. Combinaison des deux

```javascript
// Validation locale + notification pour événement important
if (isStrongPassword(password)) {
  validateField('password', quickValidation.withNotification.success('Mot de passe sécurisé !'));
} else {
  validateField('password', quickValidation.local.error('Mot de passe trop faible'));
}
```

## Exemples complets

### Formulaire de connexion

```vue
<script setup>
import { ref } from 'vue';
import { useValidation } from '@/Composables/form/useValidation';
import { quickValidation } from '@/Utils/atomic-design/atomManager';

const { validateField, handleServerErrors } = useValidation();
const emailValidation = ref(null);
const passwordValidation = ref(null);

function validateEmail() {
  const email = form.email;
  
  if (!email) {
    emailValidation.value = quickValidation.local.error('Email requis');
    return false;
  }
  
  if (!isValidEmail(email)) {
    emailValidation.value = quickValidation.local.error('Format invalide');
    return false;
  }
  
  emailValidation.value = quickValidation.withNotification.success('Email valide !');
  return true;
}

function submit() {
  form.post(route('login'), {
    onError: (errors) => {
      handleServerErrors(errors);
    },
    onSuccess: () => {
      validateField('login', quickValidation.withNotification.success('Connexion réussie !'));
    }
  });
}
</script>

<template>
  <InputField 
    label="Email" 
    v-model="form.email"
    :validation="emailValidation"
    @blur="validateEmail"
  />
  
  <InputField 
    label="Mot de passe" 
    type="password"
    v-model="form.password"
    :validation="passwordValidation"
  />
</template>
```

### Gestion des erreurs serveur

```javascript
// Mapping automatique des erreurs Laravel/Inertia
function handleFormErrors(errors) {
  handleServerErrors(errors, {
    showNotifications: true,
    notificationDuration: 8000,
    notificationPlacement: 'top-right'
  });
}

// Utilisation
form.post(route('user.update'), {
  onError: handleFormErrors,
  onSuccess: () => {
    validateField('user', quickValidation.withNotification.success('Profil mis à jour !'));
  }
});
```

## Avantages de cette approche

1. **Séparation claire** : Validation locale vs notifications globales
2. **API unifiée** : Une seule prop `validation` pour tout
3. **Flexibilité** : Choix entre local, notification, ou les deux
4. **Automatisation** : Gestion automatique des erreurs serveur
5. **Cohérence** : Même système partout dans l'application
6. **Performance** : Notifications seulement quand nécessaire
7. **UX optimisée** : Feedback approprié selon le contexte
8. **Maintenabilité** : Code centralisé et réutilisable 