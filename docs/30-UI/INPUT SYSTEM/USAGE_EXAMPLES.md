# 💡 Exemples d'Utilisation

## 📋 Vue d'ensemble

Cette page présente des **exemples pratiques** d'utilisation du système d'input KrosmozJDR dans différents contextes réels.

---

## 🚀 **Exemples de Base**

### **Formulaire de connexion simple**
```vue
<template>
  <form @submit.prevent="handleLogin" class="space-y-4">
    <InputField 
      v-model="form.email"
      label="Email"
      type="email"
      placeholder="votre@email.com"
      required
      autocomplete="email"
    />
    
    <InputField 
      v-model="form.password"
      label="Mot de passe"
      type="password"
      placeholder="Votre mot de passe"
      required
      autocomplete="current-password"
      :actions="['password']"
    />
    
    <Btn type="submit" variant="primary" :loading="loading">
      Se connecter
    </Btn>
  </form>
</template>

<script setup>
const form = ref({
  email: '',
  password: ''
})
const loading = ref(false)

async function handleLogin() {
  loading.value = true
  try {
    await login(form.value)
  } finally {
    loading.value = false
  }
}
</script>
```

### **Formulaire d'inscription avec validation**
```vue
<template>
  <form @submit.prevent="handleRegister" class="space-y-4">
    <InputField 
      v-model="form.name"
      label="Nom complet"
      placeholder="Votre nom complet"
      :validation="nameValidation"
      required
    />
    
    <InputField 
      v-model="form.email"
      label="Email"
      type="email"
      placeholder="votre@email.com"
      :validation="emailValidation"
      required
    />
    
    <InputField 
      v-model="form.password"
      label="Mot de passe"
      type="password"
      placeholder="Créez un mot de passe"
      :validation="passwordValidation"
      :actions="['password']"
      required
    />
    
    <InputField 
      v-model="form.passwordConfirmation"
      label="Confirmer le mot de passe"
      type="password"
      placeholder="Confirmez votre mot de passe"
      :validation="passwordConfirmationValidation"
      :actions="['password']"
      required
    />
    
    <CheckboxField 
      v-model="form.accepted"
      label="J'accepte les conditions d'utilisation"
      :validation="termsValidation"
    />
    
    <Btn type="submit" variant="primary" :disabled="!isFormValid">
      S'inscrire
    </Btn>
  </form>
</template>

<script setup>
const form = ref({
  name: '',
  email: '',
  password: '',
  passwordConfirmation: '',
  accepted: false
})

// Validations réactives
const nameValidation = computed(() => {
  if (!form.value.name) return null
  if (form.value.name.length < 2) {
    return { state: 'error', message: 'Le nom doit contenir au moins 2 caractères' }
  }
  return { state: 'success', message: 'Nom valide' }
})

const emailValidation = computed(() => {
  if (!form.value.email) return null
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(form.value.email)) {
    return { state: 'error', message: 'Format d\'email invalide' }
  }
  return { state: 'success', message: 'Email valide' }
})

const passwordValidation = computed(() => {
  if (!form.value.password) return null
  const length = form.value.password.length
  const hasNumber = /\d/.test(form.value.password)
  const hasLetter = /[a-zA-Z]/.test(form.value.password)
  
  if (length < 8) {
    return { state: 'error', message: 'Minimum 8 caractères' }
  }
  if (!hasNumber || !hasLetter) {
    return { state: 'warning', message: 'Ajoutez des lettres et chiffres' }
  }
  return { state: 'success', message: 'Mot de passe fort' }
})

const passwordConfirmationValidation = computed(() => {
  if (!form.value.passwordConfirmation) return null
  if (form.value.password !== form.value.passwordConfirmation) {
    return { state: 'error', message: 'Les mots de passe ne correspondent pas' }
  }
  return { state: 'success', message: 'Mots de passe identiques' }
})

const termsValidation = computed(() => {
  if (!form.value.accepted) {
    return { state: 'error', message: 'Vous devez accepter les conditions' }
  }
  return null
})

const isFormValid = computed(() => {
  return form.value.name && 
         form.value.email && 
         form.value.password && 
         form.value.passwordConfirmation && 
         form.value.accepted &&
         nameValidation.value?.state === 'success' &&
         emailValidation.value?.state === 'success' &&
         passwordValidation.value?.state === 'success' &&
         passwordConfirmationValidation.value?.state === 'success'
})
</script>
```

---

## 🎨 **Exemples Avancés**

### **Formulaire de profil utilisateur**
```vue
<template>
  <form @submit.prevent="handleProfileUpdate" class="space-y-6">
    <!-- Informations personnelles -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <InputField 
        v-model="profile.firstName"
        label="Prénom"
        placeholder="Votre prénom"
        :validation="firstNameValidation"
        required
      />
      
      <InputField 
        v-model="profile.lastName"
        label="Nom"
        placeholder="Votre nom"
        :validation="lastNameValidation"
        required
      />
    </div>
    
    <InputField 
      v-model="profile.email"
      label="Email"
      type="email"
      placeholder="votre@email.com"
      :validation="emailValidation"
      :actions="['copy']"
      required
    />
    
    <TextareaField 
      v-model="profile.bio"
      label="Biographie"
      placeholder="Parlez-nous de vous..."
      :validation="bioValidation"
      :actions="['clear']"
      rows="4"
      maxlength="500"
    />
    
    <!-- Préférences -->
    <div class="space-y-4">
      <h3 class="text-lg font-semibold">Préférences</h3>
      
      <SelectField 
        v-model="profile.language"
        label="Langue"
        :options="languageOptions"
        :actions="['reset']"
      />
      
      <SelectField 
        v-model="profile.timezone"
        label="Fuseau horaire"
        :options="timezoneOptions"
        :actions="['reset']"
      />
      
      <ToggleField 
        v-model="profile.notifications"
        label="Notifications par email"
        helper="Recevoir les notifications importantes par email"
      />
      
      <ToggleField 
        v-model="profile.newsletter"
        label="Newsletter"
        helper="Recevoir notre newsletter mensuelle"
      />
    </div>
    
    <!-- Avatar -->
    <FileField 
      v-model="profile.avatar"
      label="Photo de profil"
      accept="image/*"
      :max-size="5 * 1024 * 1024" // 5MB
      helper="Formats acceptés: JPG, PNG, GIF (max 5MB)"
      :validation="avatarValidation"
    />
    
    <Btn type="submit" variant="primary" :loading="loading">
      Mettre à jour le profil
    </Btn>
  </form>
</template>

<script setup>
const profile = ref({
  firstName: '',
  lastName: '',
  email: '',
  bio: '',
  language: 'fr',
  timezone: 'Europe/Paris',
  notifications: true,
  newsletter: false,
  avatar: null
})

const loading = ref(false)

const languageOptions = [
  { value: 'fr', label: 'Français' },
  { value: 'en', label: 'English' },
  { value: 'es', label: 'Español' }
]

const timezoneOptions = [
  { value: 'Europe/Paris', label: 'Paris (UTC+1)' },
  { value: 'Europe/London', label: 'Londres (UTC+0)' },
  { value: 'America/New_York', label: 'New York (UTC-5)' }
]

// Validations
const firstNameValidation = computed(() => {
  if (!profile.value.firstName) return null
  if (profile.value.firstName.length < 2) {
    return { state: 'error', message: 'Prénom trop court' }
  }
  return { state: 'success', message: 'Prénom valide' }
})

const lastNameValidation = computed(() => {
  if (!profile.value.lastName) return null
  if (profile.value.lastName.length < 2) {
    return { state: 'error', message: 'Nom trop court' }
  }
  return { state: 'success', message: 'Nom valide' }
})

const emailValidation = computed(() => {
  if (!profile.value.email) return null
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(profile.value.email)) {
    return { state: 'error', message: 'Email invalide' }
  }
  return { state: 'success', message: 'Email valide' }
})

const bioValidation = computed(() => {
  if (!profile.value.bio) return null
  if (profile.value.bio.length < 10) {
    return { state: 'warning', message: 'Biographie un peu courte' }
  }
  return { state: 'success', message: 'Biographie complète' }
})

const avatarValidation = computed(() => {
  if (!profile.value.avatar) return null
  if (profile.value.avatar.size > 5 * 1024 * 1024) {
    return { state: 'error', message: 'Fichier trop volumineux (max 5MB)' }
  }
  return { state: 'success', message: 'Avatar valide' }
})

async function handleProfileUpdate() {
  loading.value = true
  try {
    await updateProfile(profile.value)
  } finally {
    loading.value = false
  }
}
</script>
```

---

## 🎯 **Exemples Spécialisés**

### **Formulaire de création d'article**
```vue
<template>
  <form @submit.prevent="handleArticleCreate" class="space-y-6">
    <InputField 
      v-model="article.title"
      label="Titre de l'article"
      placeholder="Titre accrocheur..."
      :validation="titleValidation"
      :actions="['clear']"
      required
    />
    
    <TextareaField 
      v-model="article.excerpt"
      label="Résumé"
      placeholder="Résumé court de l'article..."
      :validation="excerptValidation"
      :actions="['clear']"
      rows="3"
      maxlength="200"
    />
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <SelectField 
        v-model="article.category"
        label="Catégorie"
        :options="categoryOptions"
        :validation="categoryValidation"
        required
      />
      
      <SelectField 
        v-model="article.status"
        label="Statut"
        :options="statusOptions"
        required
      />
      
      <DateField 
        v-model="article.publishDate"
        label="Date de publication"
        :min="new Date().toISOString().split('T')[0]"
        :validation="publishDateValidation"
      />
    </div>
    
    <div class="space-y-2">
      <label class="text-sm font-medium">Tags</label>
      <FilterField 
        v-model="tagInput"
        placeholder="Ajouter un tag..."
        :actions="['clear']"
        @keydown.enter.prevent="addTag"
      />
      <div class="flex flex-wrap gap-2">
        <span 
          v-for="tag in article.tags" 
          :key="tag"
          class="badge badge-primary"
        >
          {{ tag }}
          <button @click="removeTag(tag)" class="ml-1">×</button>
        </span>
      </div>
    </div>
    
    <FileField 
      v-model="article.cover"
      label="Image de couverture"
      accept="image/*"
      :max-size="2 * 1024 * 1024" // 2MB
      helper="Formats acceptés: JPG, PNG (max 2MB)"
      :validation="coverValidation"
    />
    
    <TextareaField 
      v-model="article.content"
      label="Contenu"
      placeholder="Contenu de l'article..."
      :validation="contentValidation"
      rows="10"
      required
    />
    
    <div class="flex gap-4">
      <Btn type="submit" variant="primary" :loading="loading">
        Publier l'article
      </Btn>
      <Btn type="button" variant="outline" @click="saveDraft">
        Sauvegarder brouillon
      </Btn>
    </div>
  </form>
</template>

<script setup>
const article = ref({
  title: '',
  excerpt: '',
  category: '',
  status: 'draft',
  publishDate: '',
  tags: [],
  cover: null,
  content: ''
})

const tagInput = ref('')
const loading = ref(false)

const categoryOptions = [
  { value: 'tech', label: 'Technologie' },
  { value: 'design', label: 'Design' },
  { value: 'business', label: 'Business' },
  { value: 'lifestyle', label: 'Lifestyle' }
]

const statusOptions = [
  { value: 'draft', label: 'Brouillon' },
  { value: 'published', label: 'Publié' },
  { value: 'scheduled', label: 'Programmé' }
]

// Validations
const titleValidation = computed(() => {
  if (!article.value.title) return null
  if (article.value.title.length < 10) {
    return { state: 'error', message: 'Titre trop court (min 10 caractères)' }
  }
  if (article.value.title.length > 100) {
    return { state: 'warning', message: 'Titre un peu long' }
  }
  return { state: 'success', message: 'Titre parfait' }
})

const excerptValidation = computed(() => {
  if (!article.value.excerpt) return null
  if (article.value.excerpt.length < 50) {
    return { state: 'warning', message: 'Résumé un peu court' }
  }
  return { state: 'success', message: 'Résumé complet' }
})

const categoryValidation = computed(() => {
  if (!article.value.category) {
    return { state: 'error', message: 'Catégorie requise' }
  }
  return null
})

const publishDateValidation = computed(() => {
  if (!article.value.publishDate) return null
  const selectedDate = new Date(article.value.publishDate)
  const today = new Date()
  if (selectedDate < today) {
    return { state: 'error', message: 'Date dans le passé' }
  }
  return { state: 'success', message: 'Date valide' }
})

const coverValidation = computed(() => {
  if (!article.value.cover) return null
  if (article.value.cover.size > 2 * 1024 * 1024) {
    return { state: 'error', message: 'Image trop volumineuse (max 2MB)' }
  }
  return { state: 'success', message: 'Image valide' }
})

const contentValidation = computed(() => {
  if (!article.value.content) return null
  if (article.value.content.length < 500) {
    return { state: 'error', message: 'Contenu trop court (min 500 caractères)' }
  }
  return { state: 'success', message: 'Contenu complet' }
})

function addTag() {
  const tag = tagInput.value.trim()
  if (tag && !article.value.tags.includes(tag)) {
    article.value.tags.push(tag)
    tagInput.value = ''
  }
}

function removeTag(tag) {
  article.value.tags = article.value.tags.filter(t => t !== tag)
}

async function handleArticleCreate() {
  loading.value = true
  try {
    await createArticle(article.value)
  } finally {
    loading.value = false
  }
}

async function saveDraft() {
  article.value.status = 'draft'
  await handleArticleCreate()
}
</script>
```

---

## 🎨 **Exemples de Design**

### **Formulaire avec thème sombre**
```vue
<template>
  <div class="min-h-screen bg-gray-900 text-white p-6">
    <div class="max-w-2xl mx-auto">
      <h1 class="text-3xl font-bold mb-8">Créer un compte</h1>
      
      <form @submit.prevent="handleSignup" class="space-y-6">
        <InputField 
          v-model="form.username"
          label="Nom d'utilisateur"
          placeholder="Choisissez un nom d'utilisateur"
          variant="glass"
          color="primary"
          size="lg"
          :validation="usernameValidation"
          :actions="['clear']"
          class="backdrop-blur-sm"
        />
        
        <InputField 
          v-model="form.email"
          label="Email"
          type="email"
          placeholder="votre@email.com"
          variant="glass"
          color="secondary"
          size="lg"
          :validation="emailValidation"
          :actions="['copy']"
          class="backdrop-blur-sm"
        />
        
        <InputField 
          v-model="form.password"
          label="Mot de passe"
          type="password"
          placeholder="Créez un mot de passe sécurisé"
          variant="glass"
          color="accent"
          size="lg"
          :validation="passwordValidation"
          :actions="['password']"
          class="backdrop-blur-sm"
        />
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <ToggleField 
            v-model="form.newsletter"
            label="Newsletter"
            color="success"
            size="lg"
            helper="Recevoir nos actualités"
          />
          
          <ToggleField 
            v-model="form.notifications"
            label="Notifications"
            color="info"
            size="lg"
            helper="Notifications push"
          />
        </div>
        
        <Btn 
          type="submit" 
          variant="glass" 
          color="primary" 
          size="lg"
          :loading="loading"
          class="w-full backdrop-blur-sm"
        >
          Créer mon compte
        </Btn>
      </form>
    </div>
  </div>
</template>

<script setup>
const form = ref({
  username: '',
  email: '',
  password: '',
  newsletter: false,
  notifications: true
})

const loading = ref(false)

// Validations avec thème sombre
const usernameValidation = computed(() => {
  if (!form.value.username) return null
  if (form.value.username.length < 3) {
    return { 
      state: 'error', 
      message: 'Nom d\'utilisateur trop court',
      showNotification: true
    }
  }
  return { 
    state: 'success', 
    message: 'Nom d\'utilisateur disponible',
    showNotification: true
  }
})

const emailValidation = computed(() => {
  if (!form.value.email) return null
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(form.value.email)) {
    return { 
      state: 'error', 
      message: 'Format d\'email invalide',
      showNotification: true
    }
  }
  return { 
    state: 'success', 
    message: 'Email valide',
    showNotification: true
  }
})

const passwordValidation = computed(() => {
  if (!form.value.password) return null
  const score = calculatePasswordStrength(form.value.password)
  
  if (score < 2) {
    return { 
      state: 'error', 
      message: 'Mot de passe trop faible',
      showNotification: false
    }
  }
  if (score < 4) {
    return { 
      state: 'warning', 
      message: 'Mot de passe moyen',
      showNotification: false
    }
  }
  return { 
    state: 'success', 
    message: 'Mot de passe fort',
    showNotification: true
  }
})

async function handleSignup() {
  loading.value = true
  try {
    await signup(form.value)
  } finally {
    loading.value = false
  }
}
</script>
```

---

## 🔧 **Exemples Techniques**

### **Formulaire avec validation asynchrone**
```vue
<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <InputField 
      v-model="form.username"
      label="Nom d'utilisateur"
      placeholder="Choisissez un nom d'utilisateur"
      :validation="usernameValidation"
      :actions="['clear']"
      @blur="validateUsername"
    />
    
    <InputField 
      v-model="form.email"
      label="Email"
      type="email"
      placeholder="votre@email.com"
      :validation="emailValidation"
      :actions="['copy']"
      @blur="validateEmail"
    />
    
    <Btn type="submit" variant="primary" :disabled="!isFormValid">
      Valider
    </Btn>
  </form>
</template>

<script setup>
const form = ref({
  username: '',
  email: ''
})

const usernameValidation = ref(null)
const emailValidation = ref(null)
const isFormValid = ref(false)

// Validation asynchrone avec debounce
let usernameTimeout
async function validateUsername() {
  clearTimeout(usernameTimeout)
  
  if (!form.value.username) {
    usernameValidation.value = null
    return
  }
  
  usernameTimeout = setTimeout(async () => {
    try {
      const isAvailable = await checkUsernameAvailability(form.value.username)
      usernameValidation.value = {
        state: isAvailable ? 'success' : 'error',
        message: isAvailable ? 'Nom disponible' : 'Nom déjà pris',
        showNotification: true
      }
    } catch (error) {
      usernameValidation.value = {
        state: 'error',
        message: 'Erreur de vérification',
        showNotification: true
      }
    }
  }, 500)
}

let emailTimeout
async function validateEmail() {
  clearTimeout(emailTimeout)
  
  if (!form.value.email) {
    emailValidation.value = null
    return
  }
  
  emailTimeout = setTimeout(async () => {
    try {
      const isAvailable = await checkEmailAvailability(form.value.email)
      emailValidation.value = {
        state: isAvailable ? 'success' : 'error',
        message: isAvailable ? 'Email disponible' : 'Email déjà utilisé',
        showNotification: true
      }
    } catch (error) {
      emailValidation.value = {
        state: 'error',
        message: 'Erreur de vérification',
        showNotification: true
      }
    }
  }, 500)
}

// Validation du formulaire
watch([usernameValidation, emailValidation], ([username, email]) => {
  isFormValid.value = username?.state === 'success' && email?.state === 'success'
}, { deep: true })

async function handleSubmit() {
  if (!isFormValid.value) return
  
  try {
    await submitForm(form.value)
  } catch (error) {
    console.error('Erreur lors de la soumission:', error)
  }
}
</script>
```

---

## 🔗 **Liens utiles**

- **[COMPONENTS.md](./COMPONENTS.md)** - Guide des composants
- **[VALIDATION.md](./VALIDATION.md)** - Système de validation
- **[ACTIONS.md](./ACTIONS.md)** - Actions contextuelles
- **[STYLING.md](./STYLING.md)** - Styles et personnalisation
- **[API_REFERENCE.md](./API_REFERENCE.md)** - Référence complète

---

*Documentation générée le : {{ date('Y-m-d H:i:s') }}*
*Exemples d'Utilisation du Système d'Input KrosmozJDR v2.0*
