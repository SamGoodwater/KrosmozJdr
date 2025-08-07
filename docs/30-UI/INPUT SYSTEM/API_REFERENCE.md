# 📚 Référence API - Système d'Input

## 📋 Vue d'ensemble

Cette référence documente l'**API complète** du système d'input KrosmozJDR. Tous les composants Field partagent la même interface unifiée.

---

## 🎯 **API Unifiée - Tous les Composants Field**

### **Props communes héritées automatiquement**
```javascript
// Toutes les props sont héritées via getInputPropsDefinition()
const props = defineProps({
  // === V-MODEL ===
  modelValue: {
    type: [String, Number, Boolean, Array, Object, File],
    default: null
  },
  
  // === LABELS ===
  label: {
    type: [String, Object],
    default: null
  },
  labelPosition: {
    type: String,
    default: 'top',
    validator: (value) => ['top', 'bottom', 'start', 'end', 'floating', 'inside', 'none'].includes(value)
  },
  
  // === VALIDATION ===
  validation: {
    type: [Object, String, Boolean],
    default: null
  },
  validationEnabled: {
    type: Boolean,
    default: true
  },
  
  // === ACTIONS ===
  actions: {
    type: [Array, String],
    default: []
  },
  
  // === STYLES ===
  variant: {
    type: String,
    default: 'glass',
    validator: (value) => ['glass', 'bordered', 'filled', 'ghost'].includes(value)
  },
  color: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'accent', 'success', 'warning', 'error', 'info', 'neutral', 'base'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  animation: {
    type: String,
    default: 'none',
    validator: (value) => ['none', 'fade', 'slide', 'bounce'].includes(value)
  },
  
  // === ÉTATS ===
  disabled: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  },
  
  // === AIDE ===
  helper: {
    type: [String, Object],
    default: null
  },
  placeholder: {
    type: String,
    default: null
  },
  
  // === PERSONNALISATION ===
  inputStyle: {
    type: Object,
    default: () => ({})
  },
  inputClass: {
    type: String,
    default: ''
  },
  
  // === ACCESSIBILITÉ ===
  id: {
    type: String,
    default: null
  },
  name: {
    type: String,
    default: null
  },
  required: {
    type: Boolean,
    default: false
  },
  autocomplete: {
    type: String,
    default: null
  },
  autofocus: {
    type: Boolean,
    default: false
  },
  
  // === ÉVÉNEMENTS ===
  debounce: {
    type: Number,
    default: 0
  }
})
```

---

## 🔧 **Props Spécifiques par Type**

### **InputField**
```javascript
// Props spécifiques à InputField
const inputProps = {
  type: {
    type: String,
    default: 'text',
    validator: (value) => [
      'text', 'email', 'password', 'number', 'tel', 'url', 
      'search', 'date', 'time', 'datetime-local', 'month', 'week',
      'color', 'file', 'range', 'hidden'
    ].includes(value)
  },
  maxlength: {
    type: Number,
    default: null
  },
  minlength: {
    type: Number,
    default: null
  },
  pattern: {
    type: String,
    default: null
  },
  step: {
    type: [String, Number],
    default: null
  },
  min: {
    type: [String, Number],
    default: null
  },
  max: {
    type: [String, Number],
    default: null
  }
}
```

### **TextareaField**
```javascript
// Props spécifiques à TextareaField
const textareaProps = {
  rows: {
    type: Number,
    default: 3
  },
  cols: {
    type: Number,
    default: null
  },
  maxlength: {
    type: Number,
    default: null
  },
  minlength: {
    type: Number,
    default: null
  },
  resize: {
    type: String,
    default: 'vertical',
    validator: (value) => ['none', 'vertical', 'horizontal', 'both'].includes(value)
  }
}
```

### **SelectField**
```javascript
// Props spécifiques à SelectField
const selectProps = {
  options: {
    type: Array,
    default: () => [],
    validator: (value) => value.every(option => 
      typeof option === 'object' && 
      (option.value !== undefined || option.label !== undefined)
    )
  },
  multiple: {
    type: Boolean,
    default: false
  },
  size: {
    type: Number,
    default: null
  }
}
```

### **CheckboxField**
```javascript
// Props spécifiques à CheckboxField
const checkboxProps = {
  value: {
    type: [String, Number, Boolean],
    default: true
  },
  indeterminate: {
    type: Boolean,
    default: false
  }
}
```

### **RadioField**
```javascript
// Props spécifiques à RadioField
const radioProps = {
  value: {
    type: [String, Number, Boolean],
    required: true
  },
  options: {
    type: Array,
    default: () => [],
    validator: (value) => value.every(option => 
      typeof option === 'object' && 
      (option.value !== undefined || option.label !== undefined)
    )
  }
}
```

### **ToggleField**
```javascript
// Props spécifiques à ToggleField
const toggleProps = {
  value: {
    type: [String, Number, Boolean],
    default: true
  }
}
```

### **RangeField**
```javascript
// Props spécifiques à RangeField
const rangeProps = {
  min: {
    type: Number,
    default: 0
  },
  max: {
    type: Number,
    default: 100
  },
  step: {
    type: Number,
    default: 1
  },
  showValue: {
    type: Boolean,
    default: true
  }
}
```

### **RatingField**
```javascript
// Props spécifiques à RatingField
const ratingProps = {
  max: {
    type: Number,
    default: 5
  },
  half: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  },
  clearable: {
    type: Boolean,
    default: true
  }
}
```

### **FilterField**
```javascript
// Props spécifiques à FilterField
const filterProps = {
  debounce: {
    type: Number,
    default: 300
  },
  minLength: {
    type: Number,
    default: 1
  },
  suggestions: {
    type: Array,
    default: () => []
  },
  highlight: {
    type: Boolean,
    default: true
  }
}
```

### **FileField**
```javascript
// Props spécifiques à FileField
const fileProps = {
  accept: {
    type: String,
    default: null
  },
  multiple: {
    type: Boolean,
    default: false
  },
  maxSize: {
    type: Number,
    default: null
  },
  maxFiles: {
    type: Number,
    default: null
  },
  dragDrop: {
    type: Boolean,
    default: true
  }
}
```

### **ColorField**
```javascript
// Props spécifiques à ColorField
const colorProps = {
  format: {
    type: String,
    default: 'hex',
    validator: (value) => ['hex', 'rgb', 'hsl'].includes(value)
  },
  theme: {
    type: String,
    default: 'light',
    validator: (value) => ['light', 'dark'].includes(value)
  },
  swatches: {
    type: Array,
    default: () => []
  }
}
```

### **DateField**
```javascript
// Props spécifiques à DateField
const dateProps = {
  format: {
    type: String,
    default: 'YYYY-MM-DD'
  },
  min: {
    type: String,
    default: null
  },
  max: {
    type: String,
    default: null
  },
  locale: {
    type: String,
    default: 'fr'
  },
  firstDayOfWeek: {
    type: Number,
    default: 1
  }
}
```

---

## 🎯 **API de Validation**

### **Structure de l'objet validation**
```javascript
const validation = {
  // État de validation
  state: {
    type: String,
    validator: (value) => ['error', 'success', 'warning', 'info'].includes(value)
  },
  
  // Message à afficher
  message: {
    type: String,
    default: ''
  },
  
  // Afficher une notification
  showNotification: {
    type: Boolean,
    default: false
  },
  
  // Type de notification
  notificationType: {
    type: String,
    default: 'auto',
    validator: (value) => ['auto', 'error', 'success', 'warning', 'info'].includes(value)
  },
  
  // Durée de la notification (ms)
  notificationDuration: {
    type: Number,
    default: 5000
  },
  
  // Position de la notification
  notificationPlacement: {
    type: String,
    default: null
  }
}
```

---

## 🎯 **API des Actions**

### **Structure des actions**
```javascript
// Action simple (string)
const actions = ['clear', 'copy', 'reset']

// Action avec options (object)
const actions = [
  {
    key: 'clear',
    color: 'error',
    size: 'sm',
    variant: 'ghost',
    confirm: true,
    confirmMessage: 'Êtes-vous sûr ?',
    notify: {
      message: 'Champ vidé',
      type: 'success',
      duration: 2000
    },
    delay: 1000,
    autofocus: true,
    destroy: false
  }
]
```

### **Actions disponibles**
```javascript
const availableActions = {
  reset: 'Remettre la valeur initiale',
  back: 'Annuler la dernière modification',
  clear: 'Vider le champ',
  copy: 'Copier le contenu',
  password: 'Afficher/masquer le mot de passe',
  edit: 'Bascule édition/lecture seule',
  lock: 'Activer/désactiver le champ'
}
```

---

## 🎨 **API des Styles**

### **Props de style**
```javascript
const styleProps = {
  // Variant de style
  variant: {
    type: String,
    default: 'glass',
    validator: (value) => ['glass', 'bordered', 'filled', 'ghost'].includes(value)
  },
  
  // Couleur
  color: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'accent', 'success', 'warning', 'error', 'info', 'neutral', 'base'].includes(value)
  },
  
  // Taille
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  
  // Animation
  animation: {
    type: String,
    default: 'none',
    validator: (value) => ['none', 'fade', 'slide', 'bounce'].includes(value)
  },
  
  // Styles personnalisés
  inputStyle: {
    type: Object,
    default: () => ({})
  },
  
  // Classes CSS personnalisées
  inputClass: {
    type: String,
    default: ''
  }
}
```

---

## 🔄 **Événements**

### **Événements communs**
```javascript
// Événements émis par tous les composants Field
const events = {
  'update:modelValue': (value) => {
    // Émis quand la valeur change
  },
  'focus': (event) => {
    // Émis quand le champ reçoit le focus
  },
  'blur': (event) => {
    // Émis quand le champ perd le focus
  },
  'input': (event) => {
    // Émis lors de la saisie
  },
  'change': (event) => {
    // Émis lors du changement de valeur
  },
  'keydown': (event) => {
    // Émis lors de l'appui sur une touche
  },
  'keyup': (event) => {
    // Émis lors du relâchement d'une touche
  },
  'keypress': (event) => {
    // Émis lors de l'appui sur une touche de caractère
  }
}
```

### **Événements spécifiques**
```javascript
// Événements spécifiques à certains types
const specificEvents = {
  // FileField
  'file-selected': (files) => {
    // Émis quand des fichiers sont sélectionnés
  },
  'file-removed': (file) => {
    // Émis quand un fichier est supprimé
  },
  
  // DateField
  'date-selected': (date) => {
    // Émis quand une date est sélectionnée
  },
  
  // ColorField
  'color-changed': (color) => {
    // Émis quand une couleur est sélectionnée
  },
  
  // RatingField
  'rating-changed': (rating) => {
    // Émis quand une note est donnée
  }
}
```

---

## 🎯 **Slots**

### **Slots communs**
```vue
<template>
  <!-- Slot pour contenu d'aide personnalisé -->
  <slot name="helper" />
  
  <!-- Slot pour actions au début du champ -->
  <slot name="overStart" />
  
  <!-- Slot pour actions à la fin du champ -->
  <slot name="overEnd" />
</template>
```

### **Slots spécifiques**
```vue
<template>
  <!-- SelectField - Options personnalisées -->
  <slot name="option" :option="option" :index="index" />
  
  <!-- FileField - Zone de drop personnalisée -->
  <slot name="dropzone" :isDragOver="isDragOver" />
  
  <!-- DateField - Cellules du calendrier -->
  <slot name="day" :day="day" :isSelected="isSelected" />
</template>
```

---

## 🔧 **Méthodes Exposées**

### **Méthodes communes**
```javascript
// Méthodes exposées par tous les composants Field
const exposedMethods = {
  focus: () => {
    // Donne le focus au champ
  },
  blur: () => {
    // Retire le focus du champ
  },
  select: () => {
    // Sélectionne tout le contenu
  },
  validate: () => {
    // Déclenche la validation
  },
  reset: () => {
    // Remet la valeur initiale
  },
  clear: () => {
    // Vide le champ
  },
  enableValidation: () => {
    // Active la validation
  },
  disableValidation: () => {
    // Désactive la validation
  }
}
```

---

## 🚀 **Utilisation TypeScript**

### **Types TypeScript**
```typescript
// Types pour les props
interface InputFieldProps {
  modelValue?: string | number | boolean | Array<any> | Object | File
  label?: string | LabelConfig
  labelPosition?: 'top' | 'bottom' | 'start' | 'end' | 'floating' | 'inside' | 'none'
  validation?: ValidationConfig
  validationEnabled?: boolean
  actions?: ActionConfig[]
  variant?: 'glass' | 'bordered' | 'filled' | 'ghost'
  color?: 'primary' | 'secondary' | 'accent' | 'success' | 'warning' | 'error' | 'info' | 'neutral' | 'base'
  size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl'
  animation?: 'none' | 'fade' | 'slide' | 'bounce'
  disabled?: boolean
  readonly?: boolean
  helper?: string | HelperConfig
  placeholder?: string
  inputStyle?: Record<string, any>
  inputClass?: string
  id?: string
  name?: string
  required?: boolean
  autocomplete?: string
  autofocus?: boolean
  debounce?: number
}

// Types pour la validation
interface ValidationConfig {
  state: 'error' | 'success' | 'warning' | 'info'
  message: string
  showNotification?: boolean
  notificationType?: 'auto' | 'error' | 'success' | 'warning' | 'info'
  notificationDuration?: number
  notificationPlacement?: string
}

// Types pour les actions
interface ActionConfig {
  key: string
  color?: string
  size?: string
  variant?: string
  confirm?: boolean
  confirmMessage?: string
  notify?: NotificationConfig
  delay?: number
  autofocus?: boolean
  destroy?: boolean
}
```

---

## 🔗 **Liens utiles**

- **[COMPONENTS.md](./COMPONENTS.md)** - Guide des composants
- **[VALIDATION.md](./VALIDATION.md)** - Système de validation
- **[ACTIONS.md](./ACTIONS.md)** - Actions contextuelles
- **[STYLING.md](./STYLING.md)** - Styles et personnalisation
- **[USAGE_EXAMPLES.md](./USAGE_EXAMPLES.md)** - Exemples d'utilisation

---

*Documentation générée le : {{ date('Y-m-d H:i:s') }}*
*Référence API du Système d'Input KrosmozJDR v2.0*
