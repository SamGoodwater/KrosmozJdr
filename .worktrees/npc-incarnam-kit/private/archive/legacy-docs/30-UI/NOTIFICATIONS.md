# Notifications — Guide KrosmozJDR

## 1. Architecture du système

### Composants principaux
- **useNotificationStore (Composable)** : Centralise la gestion des notifications toast
- **NotificationContainer (Organism)** : Composant d'affichage global des notifications
- **Intégration automatique** : Notifications déclenchées via la prop `validation` des inputs

### API unifiée
Toutes les notifications utilisent une API cohérente :
```javascript
{
  message: 'Message à afficher',
  type: 'success' | 'error' | 'info' | 'warning' | 'primary' | 'secondary',
  placement: 'top-right' | 'top-left' | 'bottom-right' | 'bottom-left' | 'top-center' | 'bottom-center',
  duration: 5000, // ms (0 = permanent)
  icon: 'fa-solid fa-check', // optionnel
  onClick: () => {}, // callback optionnel
  actions: [] // actions optionnelles
}
```

## 2. API complète

### useNotificationStore
```javascript
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

const { 
  success, 
  error, 
  info, 
  warning, 
  primary, 
  secondary,
  // Notifications permanentes
  permanentSuccess,
  permanentError,
  permanentInfo,
  permanentWarning,
  permanentPrimary,
  permanentSecondary,
  // Gestion
  removeNotification,
  clearAllNotifications
} = useNotificationStore();
```

### Méthodes principales
- `success(message, options)` : Notification de succès
- `error(message, options)` : Notification d'erreur
- `info(message, options)` : Notification d'information
- `warning(message, options)` : Notification d'avertissement
- `primary(message, options)` : Notification primaire
- `secondary(message, options)` : Notification secondaire

### Options disponibles
```javascript
const options = {
  placement: 'top-right', // Position de la notification
  duration: 5000, // Durée en millisecondes (0 = permanent)
  icon: 'fa-solid fa-check', // Icône personnalisée
  onClick: () => console.log('Clické'), // Action au clic
  actions: [ // Actions dans la notification
    {
      label: 'Annuler',
      onClick: () => console.log('Annulé'),
      variant: 'ghost'
    }
  ]
};
```

## 3. Exemples d'utilisation

### Notifications simples
```javascript
// Notification de succès
success('Opération réussie !');

// Notification d'erreur
error('Une erreur est survenue.');

// Notification d'information
info('Nouveau message reçu.');

// Notification d'avertissement
warning('Attention, action irréversible.');
```

### Notifications avec options
```javascript
// Notification avec placement personnalisé
success('Sauvegardé !', { 
  placement: 'bottom-right',
  duration: 3000 
});

// Notification avec icône personnalisée
error('Erreur de connexion', { 
  icon: 'fa-solid fa-wifi-slash',
  duration: 8000 
});

// Notification avec action
warning('Fichier volumineux', {
  actions: [
    {
      label: 'Continuer',
      onClick: () => uploadFile(),
      variant: 'primary'
    },
    {
      label: 'Annuler',
      onClick: () => cancelUpload(),
      variant: 'ghost'
    }
  ]
});
```

### Notifications permanentes
```javascript
// Notification permanente (ne disparaît pas)
permanentSuccess('Système en maintenance');

// Notification permanente avec action de fermeture
permanentWarning('Mise à jour disponible', {
  actions: [
    {
      label: 'Installer',
      onClick: () => installUpdate(),
      variant: 'primary'
    },
    {
      label: 'Fermer',
      onClick: () => removeNotification(id),
      variant: 'ghost'
    }
  ]
});
```

## 4. Intégration avec la validation

### Déclenchement automatique
Les notifications sont déclenchées automatiquement via la prop `validation` des inputs :

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

### Configuration de la validation
```javascript
const validation = {
  state: 'error' | 'success' | 'warning' | 'info',
  message: 'Message à afficher',
  showNotification: true | false,
  notificationType: 'auto' | 'error' | 'success' | 'warning' | 'info' | 'primary' | 'secondary',
  notificationDuration: 5000, // ms
  notificationPlacement: null // null = position par défaut
};
```

## 5. Installation et configuration

### NotificationContainer
Placer dans le layout principal :

```vue
<template>
  <div>
    <!-- Contenu de l'application -->
    <NotificationContainer />
  </div>
</template>
```

### Configuration globale
```javascript
// Dans app.js ou main.js
import { createNotificationStore } from '@/Composables/store/useNotificationStore';

const notificationStore = createNotificationStore({
  defaultPlacement: 'top-right',
  defaultDuration: 5000,
  maxNotifications: 5
});

app.provide('notificationStore', notificationStore);
```

## 6. Bonnes pratiques

### Quand utiliser les notifications
- ✅ **Événements importants** : Succès, erreurs critiques, avertissements
- ✅ **Feedback utilisateur** : Confirmation d'actions, états de chargement
- ✅ **Messages globaux** : Maintenance, mises à jour, annonces

### Quand ne pas utiliser les notifications
- ❌ **Messages fréquents** : Éviter le spam
- ❌ **Informations locales** : Utiliser les validations d'input à la place
- ❌ **Messages de debug** : Utiliser la console pour le développement

### Personnalisation
- **Placement** : Adapter selon le contexte (formulaire = bottom-right, actions = top-right)
- **Durée** : Plus longue pour les erreurs, plus courte pour les succès
- **Actions** : Proposer des actions utiles (Annuler, Réessayer, Voir détails)

## 7. Liens utiles
- [DaisyUI - Toast](https://daisyui.com/components/toast/)
- [Vue 3 - Provide/Inject](https://vuejs.org/guide/components/provide-inject.html) 