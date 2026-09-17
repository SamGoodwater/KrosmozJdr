# 🏷️ Système de Labels

## 📋 Vue d'ensemble

Le système de labels de KrosmozJDR est **complexe et flexible**, offrant 7 positions différentes avec des règles d'exclusion intelligentes. Il distingue les labels **externes** (gérés par les Fields) et **internes** (gérés par les Cores).

---

## 🎯 **Positions de Labels Disponibles**

### **Labels Externes (Field)**
```javascript
// Gérés par FieldTemplate.vue
const externalLabels = {
  top: 'Label au-dessus du champ',
  bottom: 'Label en-dessous du champ', 
  start: 'Label à gauche du champ',
  end: 'Label à droite du champ'
}
```

### **Labels Internes (Core)**
```javascript
// Gérés par les composants Core (InputCore, TextareaCore, etc.)
const internalLabels = {
  inStart: 'Label inline à gauche (dans la balise label)',
  inEnd: 'Label inline à droite (dans la balise label)',
  floating: 'Label flottant (placeholder animé)'
}
```

### **Contenu Positionné (Field)**
```javascript
// Slots pour contenu personnalisé
const positionedContent = {
  overStart: 'Contenu au début du champ (actions, icônes)',
  overEnd: 'Contenu à la fin du champ (actions, icônes)'
}
```

---

## 🔧 **Types de Composants et Support des Labels**

### **Composants à Taille Dynamique**
```vue
<!-- Input, Textarea, Select, Filter, File, Color, Date -->
<InputField 
  v-model="text"
  label="Nom"
  variant="glass"
  color="primary"
  size="md"
/>
```

**Support complet :**
- ✅ Labels externes : `top`, `bottom`, `start`, `end`
- ✅ Labels internes : `inStart`, `inEnd`, `floating`
- ✅ Contenu positionné : `overStart`, `overEnd`
- ✅ Position par défaut : `floating`

### **Composants à Taille Fixe**
```vue
<!-- Checkbox, Radio, Toggle, Rating -->
<CheckboxField 
  v-model="accepted"
  label="J'accepte les conditions"
  color="primary"
/>
```

**Support limité :**
- ✅ Labels externes : `top`, `bottom`, `start`, `end`
- ❌ Labels internes : `inStart`, `inEnd`, `floating`
- ✅ Contenu positionné : `overStart`, `overEnd`
- ✅ Position par défaut : `start`

---

## 🎨 **Utilisation des Labels**

### **Label Simple (String)**
```vue
<!-- Position par défaut selon le type de composant -->
<InputField label="Nom" v-model="name" />
<CheckboxField label="Conditions" v-model="accepted" />
```

### **Label avec Position Spécifique**
```vue
<!-- Override de la position par défaut -->
<InputField 
  label="Email" 
  v-model="email" 
  defaultLabelPosition="top"
/>
```

### **Label Complexe (Objet)**
```vue
<!-- Configuration complète avec positions multiples -->
<InputField 
  :label="{ 
    top: 'Nom complet',
    inStart: 'M.',
    inEnd: '(requis)'
  }" 
  v-model="name"
/>
```

### **Label avec Slots**
```vue
<!-- Contenu complexe dans les slots -->
<InputField :label="{ top: 'Email' }" v-model="email">
  <template #labelTop>
    <span class="flex items-center gap-2">
      <i class="fa-solid fa-envelope"></i>
      Email professionnel
      <span class="text-red-500">*</span>
    </span>
  </template>
</InputField>
```

---

## 🚫 **Règles d'Exclusion**

### **Floating Label**
```javascript
// Le label flottant exclut les autres labels interne
const floatingExclusions = {
  floating: ['inStart', 'inEnd']
}
```

**Exemple :**
```vue
<!-- ❌ Combinaison interdite -->
<InputField 
  :label="{ 
    floating: 'Nom',
    inStart: 'Nom complet'  // Ignoré à cause de floating
  }" 
  v-model="name"
/>

<!-- ✅ Combinaison valide -->
<InputField 
  :label="{ floating: 'Nom' }" 
  v-model="name"
/>
```
---

## 🎯 **Positions par Défaut par Type**

### **Composants à Taille Dynamique**
```javascript
const defaultPositions = {
  input: 'floating',      // Input standard
  textarea: 'top',        // Textarea
  select: 'top',          // Select
  filter: 'top',          // Filter
  file: 'top',            // File
  color: 'top',           // Color
  date: 'top'             // Date
}
```

### **Composants à Taille Fixe**
```javascript
const defaultPositions = {
  checkbox: 'start',      // Checkbox
  radio: 'start',         // Radio
  toggle: 'start',        // Toggle
  rating: 'top'           // Rating
}
```

---

## 🎨 **Styles et Personnalisation**

### **Styles Automatiques**
```vue
<!-- Les labels héritent des styles du composant -->
<InputField 
  label="Email"
  color="primary"
  size="lg"
  variant="glass"
/>
```

### **Styles Personnalisés**
```vue
<!-- Personnalisation via slots -->
<InputField :label="{ top: 'Email' }" v-model="email">
  <template #labelTop>
    <span class="text-blue-600 font-bold">
      <i class="fa-solid fa-envelope"></i>
      Email
    </span>
  </template>
</InputField>
```

### **Classes CSS Automatiques**
```javascript
// Classes appliquées automatiquement selon la position
const positionClasses = {
  top: 'mb-1',
  bottom: 'mt-1', 
  start: 'mr-2',
  end: 'ml-2'
}
```

---

## 🔧 **Configuration Avancée**

### **Label avec Validation**
```vue
<InputField 
  :label="{ top: 'Mot de passe' }"
  v-model="password"
  type="password"
  :validation="{ 
    state: 'error', 
    message: 'Mot de passe trop court' 
  }"
/>
```

### **Label avec Actions**
```vue
<InputField 
  :label="{ top: 'Recherche' }"
  v-model="search"
  :actions="['clear']"
>
  <template #overStart>
    <Btn variant="ghost" size="xs">
      <i class="fa-solid fa-search"></i>
    </Btn>
  </template>
</InputField>
```

### **Label avec Helper**
```vue
<InputField 
  :label="{ top: 'URL' }"
  v-model="url"
  helper="Format: https://exemple.com"
/>
```

---

## 🎯 **Cas d'Usage Spécialisés**

### **Formulaire de Contact**
```vue
<template>
  <form class="space-y-4">
    <!-- Nom avec préfixe et suffixe -->
    <InputField 
      :label="{ 
        top: 'Nom complet',
        inStart: 'M.',
        inEnd: '(requis)'
      }" 
      v-model="form.name"
      required
    />
    
    <!-- Email avec icône -->
    <InputField 
      :label="{ top: 'Email' }" 
      v-model="form.email"
      type="email"
    >
      <template #labelTop>
        <span class="flex items-center gap-2">
          <i class="fa-solid fa-envelope"></i>
          Email professionnel
        </span>
      </template>
    </InputField>
    
    <!-- Message avec label flottant -->
    <TextareaField 
      label="Message"
      v-model="form.message"
      rows="4"
    />
    
    <!-- Newsletter avec label à gauche -->
    <CheckboxField 
      :label="{ start: 'Recevoir la newsletter' }"
      v-model="form.newsletter"
    />
  </form>
</template>
```

### **Formulaire de Configuration**
```vue
<template>
  <div class="space-y-6">
    <!-- Paramètres avec labels complexes -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Volume avec label à droite -->
      <RangeField 
        :label="{ 
          top: 'Volume',
          end: 'dB'
        }"
        v-model="config.volume"
        :min="0"
        :max="100"
      />
      
      <!-- Couleur avec label en-dessous -->
      <ColorField 
        :label="{ bottom: 'Couleur principale' }"
        v-model="config.color"
      />
    </div>
    
    <!-- Notifications avec toggle -->
    <ToggleField 
      :label="{ 
        start: 'Notifications push',
        end: 'Activer'
      }"
      v-model="config.notifications"
    />
    
    <!-- Date avec label au-dessus -->
    <DateField 
      :label="{ top: 'Date d\'expiration' }"
      v-model="config.expiryDate"
    />
  </div>
</template>
```

---

## 🔧 **API Technique**

### **Configuration de Label**
```javascript
// Structure de la prop label
const labelConfig = {
  // Labels externes (Field)
  top: 'Label au-dessus',
  bottom: 'Label en-dessous',
  start: 'Label à gauche',
  end: 'Label à droite',
  
  // Labels internes (Core)
  inStart: 'Label inline gauche',
  inEnd: 'Label inline droite',
  floating: 'Label flottant'
}
```

### **Slots Disponibles**
```vue
<template>
  <!-- Labels externes -->
  <slot name="labelTop" />
  <slot name="labelBottom" />
  <slot name="labelStart" />
  <slot name="labelEnd" />
  
  <!-- Labels internes -->
  <slot name="labelInStart" />
  <slot name="labelInEnd" />
  <slot name="labelFloating" />
  
  <!-- Contenu positionné -->
  <slot name="overStart" />
  <slot name="overEnd" />
</template>
```

### **Fonctions Utilitaires**
```javascript
import { 
  processLabelConfig,
  validateLabel,
  hasFloatingLabel,
  hasInlineLabels,
  extractCoreLabels,
  extractFieldLabels
} from '@/Utils/atomic-design/labelManager'

// Traiter une configuration
const config = processLabelConfig('Nom', 'floating')

// Valider une configuration
const isValid = validateLabel({ top: 'Nom', inStart: 'M.' })

// Extraire les labels pour un Core
const coreLabels = extractCoreLabels({ floating: 'Nom' })

// Extraire les labels pour un Field
const fieldLabels = extractFieldLabels({ top: 'Nom', start: 'Préfixe' })
```

---

## 🚀 **Bonnes Pratiques**

### ✅ **À faire**
- Utiliser la position par défaut quand possible
- Respecter les règles d'exclusion
- Utiliser les slots pour du contenu complexe
- Adapter la position selon le type de composant
- Maintenir la cohérence visuelle

### ❌ **À éviter**
- Ne pas combiner `floating` avec d'autres labels
- Ne pas surcharger avec trop de labels
- Ne pas ignorer l'accessibilité
- Ne pas oublier les règles d'exclusion

---

## 🔗 **Liens utiles**

- **[COMPONENTS.md](./COMPONENTS.md)** - Guide des composants
- **[STYLING.md](./STYLING.md)** - Styles et personnalisation
- **[API_REFERENCE.md](./API_REFERENCE.md)** - Référence complète
- **[USAGE_EXAMPLES.md](./USAGE_EXAMPLES.md)** - Exemples d'utilisation

---

*Documentation générée le : {{ date('Y-m-d H:i:s') }}*
*Système de Labels KrosmozJDR v2.0*
