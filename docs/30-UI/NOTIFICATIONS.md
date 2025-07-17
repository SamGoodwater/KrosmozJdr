# Notifications — Guide KrosmozJDR

## 1. Fonctionnement général

- **useNotificationStore (Composable)** : centralise la gestion des notifications toast (succès, erreur, info, etc.).
- **NotificationContainer (Organism)** : composant d’affichage global des notifications, à placer dans le layout principal.
- **Intégration factorisée** : toute notification liée à la validation est déclenchée automatiquement via la prop factorisée `validation` (héritée via inputHelper.js).

## 2. API

### useNotificationStore
- `success(message, options)`
- `error(message, options)`
- `info(message, options)`
- `warning(message, options)`
- `primary(message, options)`
- `secondary(message, options)`
- Helpers : `permanentSuccess`, `permanentError`, etc.

## 3. Exemples d’utilisation

```js
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
const { success, error } = useNotificationStore();
success('Opération réussie !');
error('Une erreur est survenue.');
```

## 4. Intégration avec la validation
- Si une validation a `showNotification: true`, une notification toast est affichée automatiquement (via la prop factorisée `validation`).

## 5. Bonnes pratiques
- Utiliser les notifications pour les événements importants ou globaux
- Ne pas surcharger l’utilisateur de toasts
- Personnaliser le placement et la durée selon le contexte 