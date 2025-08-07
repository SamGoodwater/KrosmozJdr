# ✅ Système de Validation Unifié

## 📋 Vue d'ensemble

Le système de validation du projet KrosmozJDR est **unifié et transparent**. Une seule prop `validation` gère tous les états de validation, avec intégration automatique des notifications.

---

## 🎯 **API de Validation Unifiée**

### **Prop validation unique**
```javascript
// Une seule prop pour tous les états
const validation = {
  state: 'error' | 'success' | 'warning' | 'info',
  message: 'Message à afficher',
  showNotification: true | false,
  notificationType: 'auto' | 'error' | 'success' | 'warning' | 'info',
  notificationDuration: 5000, // ms
  notificationPlacement: null // null = position par défaut
};
```

### **Utilisation simple**
```vue
<InputField 
  v-model="email"
  label="Email"
  :validation="{ 
    state: 'error', 
    message: 'Email invalide',
    showNotification: true 
  }"
/>
```

---

## 🎨 **États de Validation**

### **error** - Erreur
```vue
<InputField 
  v-model="password"
  label="Mot de passe"
  :validation="{ 
    state: 'error', 
    message: 'Mot de passe trop court',
    showNotification: true 
  }"
/>
```

### **success** - Succès
```vue
<InputField 
  v-model="email"
  label="Email"
  :validation="{ 
    state: 'success', 
    message: 'Email valide !',
    showNotification: true 
  }"
/>
```

### **warning** - Avertissement
```vue
<InputField 
  v-model="description"
  label="Description"
  :validation="{ 
    state: 'warning', 
    message: 'Description un peu courte',
    showNotification: false 
  }"
/>
```

### **info** - Information
```vue
<InputField 
  v-model="username"
  label="Nom d'utilisateur"
  :validation="{ 
    state: 'info', 
    message: 'Nom d\'utilisateur disponible',
    showNotification: false 
  }"
/>
```

---

## 🔄 **Validation Locale vs Notifications**

### **Validation locale uniquement**
```vue
<InputField 
  v-model="name"
  label="Nom"
  :validation="{ 
    state: 'error', 
    message: 'Nom requis' 
  }"
/>
```

### **Validation avec notification**
```vue
<InputField 
  v-model="email"
  label="Email"
  :validation="{ 
    state: 'success', 
    message: 'Email valide !',
    showNotification: true 
  }"
/>
```

### **Combinaison des deux**
```vue
<InputField 
  v-model="password"
  label="Mot de passe"
  :validation="{ 
    state: 'error', 
    message: 'Mot de passe trop court',
    showNotification: true,
    notificationType: 'error',
    notificationDuration: 3000
  }"
/>
```

---

## 🧠 **Validation Conditionnelle**

### **Validation réactive**
```vue
<template>
  <InputField 
    v-model="password"
    label="Mot de passe"
    :validation="passwordValidation"
  />
</template>

<script setup>
const password = ref('')
const passwordValidation = computed(() => {
  if (!password.value) return null
  
  if (password.value.length < 8) {
    return {
      state: 'error',
      message: 'Mot de passe trop court',
      showNotification: true
    }
  }
  
  if (password.value.length < 12) {
    return {
      state: 'warning',
      message: 'Mot de passe moyen',
      showNotification: false
    }
  }
  
  return {
    state: 'success',
    message: 'Mot de passe fort !',
    showNotification: true
  }
})
</script>
```

### **Validation avec conditions complexes**
```vue
<template>
  <InputField 
    v-model="email"
    label="Email"
    :validation="emailValidation"
  />
</template>

<script setup>
const email = ref('')
const emailValidation = computed(() => {
  if (!email.value) return null
  
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  
  if (!emailRegex.test(email.value)) {
    return {
      state: 'error',
      message: 'Format d\'email invalide',
      showNotification: true
    }
  }
  
  // Vérification de domaine spécifique
  if (email.value.endsWith('@example.com')) {
    return {
      state: 'warning',
      message: 'Évitez les emails de test',
      showNotification: false
    }
  }
  
  return {
    state: 'success',
    message: 'Email valide',
    showNotification: true
  }
})
</script>
```

---

## 🔧 **Validation Avancée**

### **Validation avec délai**
```vue
<template>
  <InputField 
    v-model="username"
    label="Nom d'utilisateur"
    :validation="usernameValidation"
  />
</template>

<script setup>
const username = ref('')
const usernameValidation = ref(null)

// Validation avec délai pour éviter trop de requêtes
watch(username, async (newValue) => {
  if (!newValue) {
    usernameValidation.value = null
    return
  }
  
  // Délai de 500ms
  await new Promise(resolve => setTimeout(resolve, 500))
  
  // Vérification de disponibilité
  const isAvailable = await checkUsernameAvailability(newValue)
  
  usernameValidation.value = {
    state: isAvailable ? 'success' : 'error',
    message: isAvailable ? 'Nom disponible' : 'Nom déjà pris',
    showNotification: true
  }
})
</script>
```

### **Validation avec règles personnalisées**
```vue
<template>
  <InputField 
    v-model="phone"
    label="Téléphone"
    :validation="phoneValidation"
  />
</template>

<script setup>
const phone = ref('')
const phoneValidation = computed(() => {
  if (!phone.value) return null
  
  // Règles personnalisées
  const rules = [
    { test: /^[0-9+\-\s()]+$/, message: 'Caractères autorisés uniquement' },
    { test: /^.{10,}$/, message: 'Minimum 10 caractères' },
    { test: /^.{0,15}$/, message: 'Maximum 15 caractères' }
  ]
  
  for (const rule of rules) {
    if (!rule.test.test(phone.value)) {
      return {
        state: 'error',
        message: rule.message,
        showNotification: false
      }
    }
  }
  
  return {
    state: 'success',
    message: 'Numéro valide',
    showNotification: true
  }
})
</script>
```

---

## 🎯 **Intégration avec les Notifications**

### **Notifications automatiques**
```vue
<InputField 
  v-model="email"
  label="Email"
  :validation="{ 
    state: 'success', 
    message: 'Email valide !',
    showNotification: true,
    notificationType: 'success',
    notificationDuration: 3000
  }"
/>
```

### **Notifications personnalisées**
```vue
<InputField 
  v-model="password"
  label="Mot de passe"
  :validation="{ 
    state: 'error', 
    message: 'Mot de passe trop court',
    showNotification: true,
    notificationType: 'error',
    notificationDuration: 5000,
    notificationPlacement: 'top-end'
  }"
/>
```

---

## 🔄 **Validation Transparente**

### **Préservation de la logique métier**
```vue
<template>
  <InputField 
    v-model="form.identifier"
    label="Email ou pseudo"
    :validation="identifierValidation"
    @blur="validateIdentifier"
    @input="handleIdentifierInput"
  />
</template>

<script setup>
// Logique spécifique à la vue
const identifierValidation = ref(null)

function validateIdentifier() {
  const identifier = form.identifier
  
  if (!identifier) {
    identifierValidation.value = { 
      state: 'error', 
      message: 'Champ requis' 
    }
    return false
  }
  
  // Validation spécifique email OU pseudo
  if (identifier.includes('@')) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(identifier)) {
      identifierValidation.value = { 
        state: 'error', 
        message: 'Email invalide' 
      }
      return false
    }
  }
  
  identifierValidation.value = { 
    state: 'success', 
    message: 'Format valide' 
  }
  return true
}

function handleIdentifierInput() {
  // Logique spécifique lors de la saisie
  console.log('Identifiant modifié:', form.identifier)
}
</script>
```

---

## 🎨 **Styles de Validation**

### **Couleurs automatiques**
- **error** : Rouge (DaisyUI error)
- **success** : Vert (DaisyUI success)
- **warning** : Orange (DaisyUI warning)
- **info** : Bleu (DaisyUI info)

### **Personnalisation des couleurs**
```vue
<InputField 
  v-model="value"
  label="Champ"
  :validation="{ 
    state: 'error', 
    message: 'Erreur personnalisée',
    color: 'secondary' // Couleur personnalisée
  }"
/>
```

---

## 🔧 **Configuration Avancée**

### **Validation avec contrôle d'affichage**
```vue
<InputField 
  v-model="email"
  label="Email"
  :validation="emailValidation"
  :validation-enabled="showValidation"
/>
```

### **Validation avec messages dynamiques**
```vue
<template>
  <InputField 
    v-model="password"
    label="Mot de passe"
    :validation="passwordValidation"
  />
</template>

<script setup>
const password = ref('')
const passwordValidation = computed(() => {
  if (!password.value) return null
  
  const length = password.value.length
  const hasNumber = /\d/.test(password.value)
  const hasLetter = /[a-zA-Z]/.test(password.value)
  const hasSpecial = /[!@#$%^&*]/.test(password.value)
  
  const score = [length >= 8, hasNumber, hasLetter, hasSpecial].filter(Boolean).length
  
  const messages = {
    0: 'Très faible',
    1: 'Faible',
    2: 'Moyen',
    3: 'Bon',
    4: 'Excellent'
  }
  
  const states = {
    0: 'error',
    1: 'error',
    2: 'warning',
    3: 'success',
    4: 'success'
  }
  
  return {
    state: states[score],
    message: `Force du mot de passe : ${messages[score]}`,
    showNotification: score >= 3
  }
})
</script>
```

---

## 🚀 **Bonnes Pratiques**

### ✅ **À faire**
- Utiliser la prop `validation` unique
- Combiner validation locale et notifications selon le contexte
- Créer des validations réactives avec `computed`
- Préserver la logique métier des vues
- Utiliser des messages clairs et utiles

### ❌ **À éviter**
- Ne pas dupliquer la logique de validation
- Ne pas bloquer la logique métier avec la validation
- Ne pas utiliser d'anciennes APIs de validation
- Ne pas oublier l'accessibilité

---

## 🔗 **Liens utiles**

- **[ACTIONS.md](./ACTIONS.md)** - Actions contextuelles
- **[API_REFERENCE.md](./API_REFERENCE.md)** - Référence complète
- **[USAGE_EXAMPLES.md](./USAGE_EXAMPLES.md)** - Exemples d'utilisation
- **[Système de notifications](../NOTIFICATIONS.md)** - Notifications toast

---

*Documentation générée le : {{ date('Y-m-d H:i:s') }}*
*Système de Validation KrosmozJDR v2.0*
