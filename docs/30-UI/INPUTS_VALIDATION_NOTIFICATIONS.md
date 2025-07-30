# Inputs, Validation & Notifications — Guide KrosmozJDR

## 1. Vue d'ensemble

Ce guide présente l'architecture unifiée des inputs, de la validation et des notifications dans le projet KrosmozJDR. Tous ces systèmes sont conçus pour fonctionner ensemble de manière cohérente et factorisée.

## 2. Architecture factorisée

### Principe DRY (Don't Repeat Yourself)
- **Toutes les props** sont centralisées dans `inputHelper.js`
- **Toute la logique** est factorisée dans des composables
- **Toute l'API** est unifiée et cohérente

### Composants principaux
```
InputField (Molecule)
├── InputCore (Atom) - Saisie et styles
├── InputLabel (Atom) - Labels avec positions multiples
├── Validator (Atom) - Messages de validation
├── Helper (Atom) - Textes d'aide
└── Actions contextuelles - Boutons d'action
```

### Système de validation unifié
```
validation prop → validationManager → useNotificationStore → NotificationContainer
```

## 3. API factorisée

### Props héritées automatiquement
Tous les composants héritent automatiquement de toutes les props via `getInputProps()` :

```javascript
// Dans chaque composant
const props = defineProps({
  ...getCommonProps(),           // Props communes (id, class, etc.)
  ...getCustomUtilityProps(),    // Utilitaires (shadow, backdrop, etc.)
  ...getInputProps('input', 'field'), // Props spécifiques au type
});
```

### Props disponibles automatiquement
- **HTML** : `id`, `name`, `required`, `readonly`, `disabled`, `autocomplete`, `autofocus`
- **Styles** : `variant`, `size`, `color`, `animation`, `style`
- **Accessibilité** : `ariaLabel`, `aria-invalid`, `role`, `tabindex`
- **Validation** : `validation` (objet unifié)
- **Actions** : `actions` (array, string, ou objet)
- **Labels** : `label` (string ou objet avec positions)
- **Helpers** : `helper` (string ou objet)

## 4. Exemples d'utilisation

### Input simple avec validation
```vue
<InputField
  v-model="email"
  label="Email"
  type="email"
  :validation="{ 
    state: 'error', 
    message: 'Email invalide',
    showNotification: true 
  }"
/>
```

### Input avec actions contextuelles
```vue
<InputField
  v-model="password"
  label="Mot de passe"
  type="password"
  :actions="['reset', 'clear', 'copy']"
  helper="Minimum 8 caractères"
/>
```

### Input avec style personnalisé
```vue
<InputField
  v-model="search"
  label="Recherche"
  :style="{ variant: 'glass', color: 'primary', size: 'lg' }"
  :actions="['clear']"
>
  <template #overStart>
    <i class="fa-solid fa-search"></i>
  </template>
</InputField>
```

### Input avec labels complexes
```vue
<InputField
  v-model="price"
  :label="{ 
    start: 'Prix', 
    end: '€',
    bottom: 'Prix HT sans taxes'
  }"
  type="number"
  :validation="{ 
    state: 'success', 
    message: 'Prix valide',
    showNotification: true 
  }"
/>
```

## 5. Système de validation unifié

### API de validation
Une seule prop `validation` pour tous les états :

```javascript
const validation = {
  state: 'error' | 'success' | 'warning' | 'info',
  message: 'Message à afficher',
  showNotification: true | false,
  notificationType: 'auto' | 'error' | 'success' | 'warning' | 'info',
  notificationDuration: 5000, // ms
  notificationPlacement: null // null = position par défaut
};
```

### Validation locale vs notifications
- **Validation locale** : Affichage sous le champ (Validator)
- **Notifications** : Toast globaux pour événements importants
- **Combinaison** : Les deux peuvent être utilisés ensemble

### Exemples de validation
```vue
<!-- Validation locale uniquement -->
<InputField 
  label="Nom" 
  v-model="name"
  :validation="{ state: 'error', message: 'Nom requis' }"
/>

<!-- Validation avec notification -->
<InputField 
  label="Email" 
  v-model="email"
  :validation="{ 
    state: 'success', 
    message: 'Email valide !',
    showNotification: true 
  }"
/>

<!-- Validation conditionnelle -->
<InputField 
  label="Mot de passe" 
  v-model="password"
  :validation="passwordValidation"
/>

<script setup>
const passwordValidation = computed(() => {
  if (!password.value) return null;
  
  if (password.value.length < 8) {
    return {
      state: 'error',
      message: 'Mot de passe trop court',
      showNotification: true
    };
  }
  
  return {
    state: 'success',
    message: 'Mot de passe valide',
    showNotification: true
  };
});
</script>
```

## 6. Système d'actions contextuelles

### Actions disponibles
- `reset` : Remet la valeur initiale
- `back` : Annule la dernière modification
- `clear` : Vide le champ
- `copy` : Copie le contenu
- `password` : Affiche/masque le mot de passe
- `edit` : Bascule édition/lecture seule
- `lock` : Active/désactive le champ

### Configuration des actions
```vue
<!-- Actions simples -->
<InputField 
  label="Texte" 
  v-model="text"
  :actions="['copy', 'clear']"
/>

<!-- Actions avec options -->
<InputField 
  label="Texte" 
  v-model="text"
  :actions="[
    { key: 'reset', color: 'warning', confirm: true },
    { key: 'copy', notify: { message: 'Copié !' } }
  ]"
/>

<!-- Actions personnalisées -->
<InputField label="Recherche" v-model="search">
  <template #overStart>
    <Btn variant="ghost" size="xs">
      <i class="fa-solid fa-search"></i>
    </Btn>
  </template>
  <template #overEnd>
    <Btn variant="ghost" size="xs" @click="advancedSearch">
      <i class="fa-solid fa-cog"></i>
    </Btn>
  </template>
</InputField>
```

## 7. Bonnes pratiques

### Factorisation
- ✅ **Utiliser getInputProps()** : Hériter automatiquement de toutes les props
- ✅ **Factoriser la logique** : Utiliser les composables (useValidation, useInputActions)
- ✅ **Éviter la duplication** : Ne jamais redéclarer une prop déjà factorisée

### Validation
- ✅ **Validation locale** : Pour les erreurs de format, aide à la saisie
- ✅ **Notifications** : Pour les événements importants, erreurs critiques
- ✅ **Combinaison** : Les deux peuvent être utilisés ensemble

### Actions
- ✅ **Actions automatiques** : Utiliser les actions prédéfinies
- ✅ **Actions personnalisées** : Utiliser les slots overStart/overEnd
- ✅ **Options** : Personnaliser le comportement des actions

### Styles
- ✅ **API unifiée** : Utiliser `style` (objet) ou `variant` (string)
- ✅ **Transmission automatique** : Les styles sont transmis aux labels et helpers
- ✅ **Cohérence** : Utiliser les mêmes styles dans toute l'application

## 8. Migration et onboarding

### Pour les nouveaux développeurs
1. **Commencer par INPUTS.md** : Comprendre la structure factorisée
2. **Lire VALIDATION.md** : Maîtriser le système de validation
3. **Consulter NOTIFICATIONS.md** : Utiliser les notifications
4. **Pratiquer** : Utiliser les exemples fournis

### Pour la migration
- **Remplacer** : `validator`, `validatorError`, `validatorSuccess` → `validation`
- **Factoriser** : Utiliser `getInputProps()` au lieu de déclarer les props manuellement
- **Unifier** : Utiliser la même API pour tous les types d'input

## 9. Liens utiles
- [Inputs](./INPUTS.md) : Guide complet des composants d'input
- [Validation](./VALIDATION.md) : Guide du système de validation
- [Notifications](./NOTIFICATIONS.md) : Guide des notifications
- [Intégration Validation + Notifications](./INTEGRATION_VALIDATION_NOTIFICATIONS.md) : Cas d'usage avancés
- [Styles des Inputs](./INPUT_STYLES.md) : Guide du système de styles 