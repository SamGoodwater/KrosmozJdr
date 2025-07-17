# Intégration Validation + Notifications — Guide KrosmozJDR

## 1. Principes
- **Validation locale** : feedback immédiat sous le champ (format, aide, erreurs de saisie)
- **Notification** : événements importants, réponses serveur, confirmations, erreurs globales
- **API factorisée** : toutes les props utiles (dont `validation`) sont héritées automatiquement via `inputHelper.js` et `getInputProps`.

## 2. Exemples

### Validation locale
```vue
<InputField label="Email" v-model="email" :validation="{ state: 'error', message: 'Format invalide' }" />
```

### Validation + notification
```vue
<InputField label="Email" v-model="email" :validation="{ state: 'success', message: 'OK', showNotification: true }" />
```

### Erreurs serveur
```js
handleServerErrors(errors, { showNotifications: true });
```

## 3. FAQ & Bonnes pratiques
- Utiliser la prop unique `validation` pour tout (locale + notification)
- Ne jamais redéclarer une prop déjà factorisée dans inputHelper.js
- Factoriser la logique métier dans les composables
- Utiliser les slots pour personnaliser l’UI 