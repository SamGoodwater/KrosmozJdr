# 🎯 Actions Contextuelles

## 📋 Vue d'ensemble

Le système d'actions contextuelles permet d'ajouter des **boutons d'action** directement dans les champs de saisie. Ces actions sont **intégrées et non bloquantes**, offrant une UX moderne et intuitive.

---

## 🎯 **Actions Disponibles**

### **reset** - Remettre la valeur initiale
```vue
<InputField 
  v-model="name"
  label="Nom"
  :actions="['reset']"
/>
```

### **back** - Annuler la dernière modification
```vue
<InputField 
  v-model="description"
  label="Description"
  :actions="['back']"
/>
```

### **clear** - Vider le champ
```vue
<InputField 
  v-model="search"
  label="Recherche"
  :actions="['clear']"
/>
```

### **copy** - Copier le contenu
```vue
<InputField 
  v-model="url"
  label="URL"
  :actions="['copy']"
/>
```

### **password** - Afficher/masquer le mot de passe
```vue
<InputField 
  v-model="password"
  label="Mot de passe"
  type="password"
  :actions="['password']"
/>
```

### **edit** - Bascule édition/lecture seule
```vue
<InputField 
  v-model="title"
  label="Titre"
  :actions="['edit']"
/>
```

### **lock** - Activer/désactiver le champ
```vue
<InputField 
  v-model="email"
  label="Email"
  :actions="['lock']"
/>
```

---

## 🔧 **Configuration des Actions**

### **Actions simples**
```vue
<InputField 
  v-model="text"
  label="Texte"
  :actions="['copy', 'clear']"
/>
```

### **Actions avec options**
```vue
<InputField 
  v-model="text"
  label="Texte"
  :actions="[
    { key: 'reset', color: 'warning', confirm: true },
    { key: 'copy', notify: { message: 'Copié !' } }
  ]"
/>
```

### **Actions conditionnelles**
```vue
<InputField 
  v-model="search"
  label="Recherche"
  :actions="search ? ['clear'] : []"
/>
```

---

## 🎨 **Actions Personnalisées**

### **Actions au début du champ (overStart)**
```vue
<InputField 
  v-model="search"
  label="Recherche"
  :actions="['clear']"
>
  <template #overStart>
    <Btn variant="ghost" size="xs">
      <i class="fa-solid fa-search"></i>
    </Btn>
  </template>
</InputField>
```

### **Actions à la fin du champ (overEnd)**
```vue
<InputField 
  v-model="url"
  label="URL"
  :actions="['copy']"
>
  <template #overEnd>
    <Btn variant="ghost" size="xs" @click="openUrl">
      <i class="fa-solid fa-external-link-alt"></i>
    </Btn>
  </template>
</InputField>
```

### **Actions multiples personnalisées**
```vue
<InputField 
  v-model="color"
  label="Couleur"
>
  <template #overStart>
    <Btn variant="ghost" size="xs" @click="setRandomColor">
      <i class="fa-solid fa-dice"></i>
    </Btn>
  </template>
  <template #overEnd>
    <Btn variant="ghost" size="xs" @click="setBrandColor">
      <i class="fa-solid fa-palette"></i>
    </Btn>
  </template>
</InputField>
```

---

## 🔄 **Intégration avec les Notifications**

### **Notifications automatiques**
```vue
<InputField 
  v-model="text"
  label="Texte"
  :actions="[
    { 
      key: 'copy', 
      notify: { 
        message: 'Texte copié !',
        type: 'success',
        duration: 2000
      }
    }
  ]"
/>
```

### **Notifications personnalisées**
```vue
<InputField 
  v-model="text"
  label="Texte"
  :actions="[
    { 
      key: 'reset', 
      notify: { 
        message: 'Valeur réinitialisée',
        type: 'info',
        icon: 'fa-solid fa-undo'
      }
    }
  ]"
/>
```

---

## 🎯 **Actions par Type d'Input**

### **Input standard**
```vue
<InputField 
  v-model="text"
  label="Texte"
  :actions="['clear', 'copy', 'reset']"
/>
```

### **Input password**
```vue
<InputField 
  v-model="password"
  label="Mot de passe"
  type="password"
  :actions="['password', 'clear']"
/>
```

### **Textarea**
```vue
<TextareaField 
  v-model="description"
  label="Description"
  :actions="['clear', 'copy']"
/>
```

### **Select**
```vue
<SelectField 
  v-model="category"
  label="Catégorie"
  :options="categories"
  :actions="['reset']"
/>
```

### **File**
```vue
<FileField 
  v-model="file"
  label="Document"
  :actions="['clear']"
/>
```

### **Color**
```vue
<ColorField 
  v-model="color"
  label="Couleur"
  :actions="['reset']"
/>
```

### **Date**
```vue
<DateField 
  v-model="date"
  label="Date"
  :actions="['clear', 'reset']"
/>
```

---

## 🔧 **Configuration Avancée**

### **Actions avec confirmation**
```vue
<InputField 
  v-model="importantText"
  label="Texte important"
  :actions="[
    { 
      key: 'reset', 
      confirm: true,
      confirmMessage: 'Êtes-vous sûr de vouloir réinitialiser ?'
    }
  ]"
/>
```

### **Actions avec délai**
```vue
<InputField 
  v-model="text"
  label="Texte"
  :actions="[
    { 
      key: 'reset', 
      delay: 1000, // Délai avant de pouvoir refaire l'action
      autofocus: true // Autofocus sur le champ après l'action
    }
  ]"
/>
```

### **Actions avec destruction**
```vue
<InputField 
  v-model="text"
  label="Texte"
  :actions="[
    { 
      key: 'clear', 
      destroy: true // L'action disparaît après utilisation
    }
  ]"
/>
```

---

## 🎨 **Personnalisation Visuelle**

### **Couleurs personnalisées**
```vue
<InputField 
  v-model="text"
  label="Texte"
  :actions="[
    { key: 'copy', color: 'success' },
    { key: 'clear', color: 'error' },
    { key: 'reset', color: 'warning' }
  ]"
/>
```

### **Tailles personnalisées**
```vue
<InputField 
  v-model="text"
  label="Texte"
  :actions="[
    { key: 'copy', size: 'sm' },
    { key: 'clear', size: 'lg' }
  ]"
/>
```

### **Variants personnalisés**
```vue
<InputField 
  v-model="text"
  label="Texte"
  :actions="[
    { key: 'copy', variant: 'outline' },
    { key: 'clear', variant: 'ghost' }
  ]"
/>
```

---

## 🚀 **Actions Avancées**

### **Actions avec logique personnalisée**
```vue
<InputField 
  v-model="search"
  label="Recherche"
>
  <template #overStart>
    <Btn variant="ghost" size="xs" @click="advancedSearch">
      <i class="fa-solid fa-cog"></i>
    </Btn>
  </template>
  <template #overEnd>
    <Btn variant="ghost" size="xs" @click="saveSearch">
      <i class="fa-solid fa-bookmark"></i>
    </Btn>
  </template>
</InputField>
```

### **Actions avec état**
```vue
<InputField 
  v-model="text"
  label="Texte"
>
  <template #overEnd>
    <Btn 
      variant="ghost" 
      size="xs" 
      :class="{ 'text-success': isSaved }"
      @click="toggleSave"
    >
      <i :class="isSaved ? 'fa-solid fa-check' : 'fa-regular fa-bookmark'"></i>
    </Btn>
  </template>
</InputField>
```

### **Actions avec menu déroulant**
```vue
<InputField 
  v-model="text"
  label="Texte"
>
  <template #overEnd>
    <div class="dropdown dropdown-end">
      <Btn variant="ghost" size="xs" tabindex="0">
        <i class="fa-solid fa-ellipsis-v"></i>
      </Btn>
      <ul class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
        <li><a @click="copyText">Copier</a></li>
        <li><a @click="clearText">Effacer</a></li>
        <li><a @click="resetText">Réinitialiser</a></li>
      </ul>
    </div>
  </template>
</InputField>
```

---

## 🔧 **Compatibilité des Actions**

| Action | Input | Textarea | Select | Checkbox | Radio | Toggle | Range | Rating | Filter | File | Color | Date |
|--------|-------|----------|--------|----------|-------|--------|-------|--------|--------|------|-------|------|
| **reset** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **back** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **clear** | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **copy** | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ |
| **password** | ✅* | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **edit** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **lock** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

*Uniquement pour les inputs de type `password`

---

## 🚀 **Bonnes Pratiques**

### ✅ **À faire**
- Utiliser les actions intégrées quand possible
- Ajouter des actions personnalisées via les slots
- Intégrer les notifications pour le feedback
- Respecter la compatibilité des actions par type
- Utiliser des icônes claires et compréhensibles

### ❌ **À éviter**
- Ne pas surcharger les champs avec trop d'actions
- Ne pas utiliser des actions incompatibles
- Ne pas oublier l'accessibilité (aria-labels)
- Ne pas bloquer la logique métier avec les actions

---

## 🔗 **Liens utiles**

- **[VALIDATION.md](./VALIDATION.md)** - Système de validation
- **[API_REFERENCE.md](./API_REFERENCE.md)** - Référence complète
- **[USAGE_EXAMPLES.md](./USAGE_EXAMPLES.md)** - Exemples d'utilisation
- **[Système de notifications](../NOTIFICATIONS.md)** - Notifications toast

---

*Documentation générée le : {{ date('Y-m-d H:i:s') }}*
*Actions Contextuelles KrosmozJDR v2.0*
