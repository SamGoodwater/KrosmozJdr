# ✅ Système de Validation Granulaire

## 📋 Vue d'ensemble

Le système de validation de KrosmozJDR utilise une **approche granulaire et flexible** permettant de définir plusieurs règles de validation par champ, chacune avec son propre déclencheur, message et état.

---

## 🎯 **API de Validation Granulaire**

### **Prop validationRules**
```javascript
// Règles de validation granulaire
const validationRules = [
  {
    rule: (value) => value.length >= 3,
    message: 'Minimum 3 caractères',
    state: 'error',
    trigger: 'blur', // 'auto', 'manual', 'blur', 'change'
    priority: 1,
    showNotification: false
  },
  {
    rule: /^[a-zA-Z\s]+$/,
    message: 'Lettres et espaces uniquement',
    state: 'warning',
    trigger: 'change',
    priority: 2
  }
]
```

### **Utilisation simple**
```vue
<InputField 
  v-model="name"
  label="Nom"
  :validation-rules="[
    {
      rule: (value) => value.length >= 2,
      message: 'Nom trop court',
      state: 'error',
      trigger: 'blur'
    }
  ]"
/>
```

---

## 🔧 **Types de Règles**

### **Fonction de validation**
```javascript
{
  rule: (value) => {
    return value.length >= 8 && /[A-Z]/.test(value)
  },
  message: 'Minimum 8 caractères avec une majuscule',
  state: 'error',
  trigger: 'blur'
}
```

### **Expression régulière**
```javascript
{
  rule: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
  message: 'Format d\'email invalide',
  state: 'error',
  trigger: 'blur'
}
```

### **Pattern de validation**
```javascript
{
  rule: 'required',
  message: 'Ce champ est requis',
  state: 'error',
  trigger: 'blur'
}
```

---

## 🎨 **États de Validation**

### **error** - Erreur critique
```javascript
{
  rule: (value) => !value || value.length < 3,
  message: 'Champ requis (minimum 3 caractères)',
  state: 'error',
  trigger: 'blur'
}
```

### **warning** - Avertissement
```javascript
{
  rule: (value) => value && value.length < 8,
  message: 'Considérez un mot de passe plus long',
  state: 'warning',
  trigger: 'change'
}
```

### **info** - Information
```javascript
{
  rule: (value) => value && value.length > 0,
  message: 'Champ rempli correctement',
  state: 'info',
  trigger: 'change'
}
```

### **success** - Succès
```javascript
{
  rule: (value) => value && value.length >= 12,
  message: 'Mot de passe fort !',
  state: 'success',
  trigger: 'change'
}
```

---

## ⏰ **Déclencheurs de Validation**

### **auto** - Validation automatique
```javascript
{
  rule: (value) => value.length >= 3,
  message: 'Minimum 3 caractères',
  state: 'error',
  trigger: 'auto' // Se déclenche automatiquement
}
```

### **manual** - Validation manuelle
```javascript
{
  rule: (value) => value.length >= 3,
  message: 'Minimum 3 caractères',
  state: 'error',
  trigger: 'manual' // Se déclenche uniquement manuellement
}
```

### **blur** - Validation à la perte de focus
```javascript
{
  rule: (value) => value.length >= 3,
  message: 'Minimum 3 caractères',
  state: 'error',
  trigger: 'blur' // Se déclenche quand l'utilisateur quitte le champ
}
```

### **change** - Validation au changement
```javascript
{
  rule: (value) => value.length >= 3,
  message: 'Minimum 3 caractères',
  state: 'error',
  trigger: 'change' // Se déclenche à chaque modification
}
```

---

## 🎯 **Exemples d'Utilisation**

### **Validation d'email**
```vue
<InputField 
  v-model="email"
  label="Email"
  :validation-rules="[
    {
      rule: 'required',
      message: 'Email requis',
      state: 'error',
      trigger: 'blur'
    },
    {
      rule: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
      message: 'Format d\'email invalide',
      state: 'error',
      trigger: 'blur'
    },
    {
      rule: (value) => !value.includes('test'),
      message: 'Évitez les emails de test',
      state: 'warning',
      trigger: 'change'
    }
  ]"
/>
```

### **Validation de mot de passe**
```vue
<InputField 
  v-model="password"
  label="Mot de passe"
  type="password"
  :validation-rules="[
    {
      rule: 'required',
      message: 'Mot de passe requis',
      state: 'error',
      trigger: 'blur'
    },
    {
      rule: (value) => value && value.length >= 8,
      message: 'Minimum 8 caractères',
      state: 'error',
      trigger: 'blur'
    },
    {
      rule: (value) => value && /[A-Z]/.test(value),
      message: 'Au moins une majuscule',
      state: 'warning',
      trigger: 'change'
    },
    {
      rule: (value) => value && /\d/.test(value),
      message: 'Au moins un chiffre',
      state: 'warning',
      trigger: 'change'
    },
    {
      rule: (value) => value && value.length >= 12,
      message: 'Mot de passe fort !',
      state: 'success',
      trigger: 'change'
    }
  ]"
/>
```

### **Validation de nom d'utilisateur**
```vue
<InputField 
  v-model="username"
  label="Nom d'utilisateur"
  :validation-rules="[
    {
      rule: 'required',
      message: 'Nom d\'utilisateur requis',
      state: 'error',
      trigger: 'blur'
    },
    {
      rule: (value) => value && value.length >= 3,
      message: 'Minimum 3 caractères',
      state: 'error',
      trigger: 'blur'
    },
    {
      rule: /^[a-zA-Z0-9_]+$/,
      message: 'Lettres, chiffres et underscore uniquement',
      state: 'error',
      trigger: 'blur'
    },
    {
      rule: (value) => value && value.length <= 20,
      message: 'Maximum 20 caractères',
      state: 'warning',
      trigger: 'change'
    }
  ]"
/>
```

---

## 🔄 **Contrôle Parent**

### **Désactiver la validation automatique**
```vue
<InputField 
  v-model="name"
  label="Nom"
  :validation-rules="nameValidationRules"
  :parent-control="true"
  :auto-validate="false"
/>
```

### **Validation manuelle**
```vue
<template>
  <InputField 
    v-model="email"
    label="Email"
    :validation-rules="emailValidationRules"
    :parent-control="true"
    ref="emailField"
  />
  <button @click="validateEmail">Valider Email</button>
</template>

<script setup>
const emailField = ref(null)

function validateEmail() {
  emailField.value?.validate()
}
</script>
```

> **Note :** La validation est automatiquement activée dès qu'il y a des règles de validation. Il n'est plus nécessaire d'activer/désactiver manuellement le système.

---

## 🎨 **Intégration avec les Notifications**

### **Notifications automatiques**
```javascript
{
  rule: (value) => value && value.length >= 8,
  message: 'Mot de passe sécurisé !',
  state: 'success',
  trigger: 'change',
  showNotification: true,
  notificationType: 'success',
  notificationDuration: 3000
}
```

### **Notifications personnalisées**
```javascript
{
  rule: (value) => !value || value.length < 3,
  message: 'Nom trop court',
  state: 'error',
  trigger: 'blur',
  showNotification: true,
  notificationType: 'error',
  notificationDuration: 5000,
  notificationPlacement: 'top-end'
}
```

---

## 🔧 **Configuration Avancée**

### **Priorité des règles**
```javascript
const validationRules = [
  {
    rule: 'required',
    message: 'Champ requis',
    state: 'error',
    trigger: 'blur',
    priority: 1 // Priorité élevée
  },
  {
    rule: (value) => value && value.length < 8,
    message: 'Considérez une valeur plus longue',
    state: 'warning',
    trigger: 'change',
    priority: 2 // Priorité moyenne
  }
]
```

### **Validation conditionnelle**
```vue
<template>
  <InputField 
    v-model="password"
    label="Mot de passe"
    :validation-rules="passwordValidationRules"
  />
</template>

<script setup>
const password = ref('')
const passwordValidationRules = computed(() => {
  const rules = [
    {
      rule: 'required',
      message: 'Mot de passe requis',
      state: 'error',
      trigger: 'blur'
    }
  ]
  
  if (password.value && password.value.length > 0) {
    rules.push({
      rule: (value) => value.length >= 8,
      message: 'Minimum 8 caractères',
      state: 'error',
      trigger: 'blur'
    })
  }
  
  return rules
})
</script>
```

---

## 🚀 **Bonnes Pratiques**

### ✅ **À faire**
- Utiliser des règles spécifiques et claires
- Choisir le bon déclencheur selon le contexte
- Prioriser les règles importantes
- Utiliser des messages utiles pour l'utilisateur
- Combiner validation locale et serveur

### ❌ **À éviter**
- Ne pas surcharger avec trop de règles
- Ne pas utiliser des règles trop complexes
- Ne pas ignorer l'expérience utilisateur
- Ne pas oublier l'accessibilité

---

## 🔗 **Liens utiles**

- **[ACTIONS.md](./ACTIONS.md)** - Actions contextuelles
- **[API_REFERENCE.md](./API_REFERENCE.md)** - Référence complète
- **[USAGE_EXAMPLES.md](./USAGE_EXAMPLES.md)** - Exemples d'utilisation
- **[Système de notifications](../NOTIFICATIONS.md)** - Notifications toast

---

*Documentation générée le : {{ date('Y-m-d H:i:s') }}*
*Système de Validation Granulaire KrosmozJDR v2.0*
