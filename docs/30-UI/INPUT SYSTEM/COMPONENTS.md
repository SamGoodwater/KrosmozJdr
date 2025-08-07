# 🧩 Composants du Système d'Input

## 📋 Vue d'ensemble des composants

Le système d'input de KrosmozJDR est organisé selon l'Atomic Design avec des **Atoms (Core)** et des **Molecules (Field)**. Chaque type d'input dispose d'un composant Core pour la saisie et d'un composant Field pour l'interface complète.

---

## 🎯 **Composants Core (Atoms)**

Les composants Core sont les **atomes de base** qui gèrent uniquement la saisie, les styles et l'accessibilité. Ils ne contiennent aucune logique métier.

### **InputCore.vue** - Champ de saisie standard
```vue
<InputCore 
  v-model="value"
  type="text"
  placeholder="Saisissez du texte"
  variant="glass"
  color="primary"
  size="md"
/>
```

**Props principales :**
- `type` : Type d'input (text, email, password, number, etc.)
- `modelValue` : Valeur du v-model
- `placeholder` : Texte d'aide
- `variant`, `color`, `size` : Styles DaisyUI
- `disabled`, `readonly` : États du champ

### **TextareaCore.vue** - Zone de texte multiligne
```vue
<TextareaCore 
  v-model="description"
  placeholder="Description..."
  rows="4"
  variant="glass"
  color="primary"
/>
```

**Props principales :**
- `rows` : Nombre de lignes
- `maxlength` : Longueur maximale
- `resize` : Redimensionnement (vertical, horizontal, none)

### **SelectCore.vue** - Liste déroulante
```vue
<SelectCore v-model="category" variant="glass" color="primary">
  <option value="">Sélectionnez...</option>
  <option value="tech">Technologie</option>
  <option value="design">Design</option>
</SelectCore>
```

**Props principales :**
- `multiple` : Sélection multiple
- `size` : Nombre d'options visibles

### **CheckboxCore.vue** - Case à cocher
```vue
<CheckboxCore 
  v-model="accepted"
  variant="glass"
  color="primary"
/>
```

### **RadioCore.vue** - Bouton radio
```vue
<RadioCore 
  v-model="gender"
  value="male"
  variant="glass"
  color="primary"
/>
```

### **ToggleCore.vue** - Interrupteur
```vue
<ToggleCore 
  v-model="notifications"
  variant="glass"
  color="primary"
/>
```

### **RangeCore.vue** - Curseur de valeur
```vue
<RangeCore 
  v-model="volume"
  :min="0"
  :max="100"
  :step="1"
  variant="glass"
  color="primary"
/>
```

### **RatingCore.vue** - Système de notation
```vue
<RatingCore 
  v-model="rating"
  :max="5"
  variant="glass"
  color="primary"
/>
```

### **FilterCore.vue** - Filtre de recherche
```vue
<FilterCore 
  v-model="search"
  placeholder="Rechercher..."
  variant="glass"
  color="primary"
/>
```

### **FileCore.vue** - Upload de fichiers
```vue
<FileCore 
  v-model="file"
  accept=".pdf,.doc,.docx"
  multiple
  variant="glass"
  color="primary"
/>
```

### **ColorCore.vue** - Sélecteur de couleur
```vue
<ColorCore 
  v-model="color"
  format="hex"
  theme="dark"
  variant="glass"
  color="primary"
/>
```

### **DateCore.vue** - Sélecteur de date
```vue
<DateCore 
  v-model="date"
  format="YYYY-MM-DD"
  :min="'2024-01-01'"
  :max="'2024-12-31'"
  variant="glass"
  color="primary"
/>
```

---

## 🧬 **Composants Field (Molecules)**

Les composants Field sont les **molécules complètes** qui orchestrent les composants Core avec labels, validation, actions et helpers.

### **Pattern unifié**
Tous les composants Field suivent le même pattern :

```vue
<script setup>
import { useSlots, useAttrs } from 'vue'
import InputCore from '@/Pages/Atoms/data-input/InputCore.vue'
import FieldTemplate from '@/Pages/Molecules/data-input/FieldTemplate.vue'
import useInputField from '@/Composables/form/useInputField'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'

// Props héritées automatiquement
const props = defineProps(getInputPropsDefinition('input', 'field'))
const emit = defineEmits(['update:modelValue'])
const $attrs = useAttrs()

// Composable unifié
const {
  currentValue, actionsToDisplay, inputRef, focus,
  inputAttrs, listeners,
  labelConfig,
  validationState, validationMessage,
  styleProperties, containerClasses
} = useInputField({
  modelValue: props.modelValue,
  type: 'input',
  mode: 'field',
  props,
  attrs: $attrs,
  emit
})
</script>

<template>
  <FieldTemplate
    :container-classes="containerClasses"
    :label-config="labelConfig"
    :input-attrs="inputAttrs"
    :listeners="listeners"
    :input-ref="inputRef"
    :actions-to-display="actionsToDisplay"
    :style-properties="styleProperties"
    :validation-state="validationState"
    :validation-message="validationMessage"
    :helper="props.helper"
  >
    <template #core="{ inputAttrs, listeners, inputRef }">
      <InputCore v-bind="inputAttrs" v-on="listeners" ref="inputRef" />
    </template>
    <template #helper><slot name="helper" /></template>
  </FieldTemplate>
</template>
```

### **InputField.vue** - Champ de saisie complet
```vue
<InputField 
  v-model="email"
  label="Email"
  type="email"
  placeholder="votre@email.com"
  :validation="{ state: 'error', message: 'Email invalide' }"
  :actions="['clear', 'copy']"
  helper="Format: nom@domaine.com"
/>
```

### **TextareaField.vue** - Zone de texte complète
```vue
<TextareaField 
  v-model="description"
  label="Description"
  placeholder="Décrivez votre projet..."
  :validation="{ state: 'warning', message: 'Description trop courte' }"
  :actions="['clear']"
  helper="Minimum 50 caractères"
/>
```

### **SelectField.vue** - Liste déroulante complète
```vue
<SelectField 
  v-model="category"
  label="Catégorie"
  :options="[
    { value: 'tech', label: 'Technologie' },
    { value: 'design', label: 'Design' }
  ]"
  :validation="{ state: 'error', message: 'Catégorie requise' }"
  :actions="['reset']"
/>
```

### **CheckboxField.vue** - Case à cocher complète
```vue
<CheckboxField 
  v-model="accepted"
  label="J'accepte les conditions"
  :validation="{ state: 'error', message: 'Vous devez accepter les conditions' }"
/>
```

### **RadioField.vue** - Bouton radio complet
```vue
<RadioField 
  v-model="gender"
  label="Genre"
  :options="[
    { value: 'male', label: 'Homme' },
    { value: 'female', label: 'Femme' }
  ]"
  :validation="{ state: 'error', message: 'Genre requis' }"
/>
```

### **ToggleField.vue** - Interrupteur complet
```vue
<ToggleField 
  v-model="notifications"
  label="Notifications"
  helper="Recevoir les notifications par email"
  :actions="['reset']"
/>
```

### **RangeField.vue** - Curseur complet
```vue
<RangeField 
  v-model="volume"
  label="Volume"
  :min="0"
  :max="100"
  :step="5"
  helper="Ajustez le volume de 0 à 100"
  :actions="['reset']"
/>
```

### **RatingField.vue** - Notation complète
```vue
<RatingField 
  v-model="rating"
  label="Note"
  :max="5"
  helper="Notez de 1 à 5 étoiles"
  :actions="['reset']"
/>
```

### **FilterField.vue** - Filtre complet
```vue
<FilterField 
  v-model="search"
  label="Rechercher"
  placeholder="Tapez pour rechercher..."
  :actions="['clear']"
  helper="Recherche en temps réel"
/>
```

### **FileField.vue** - Upload complet
```vue
<FileField 
  v-model="file"
  label="Document"
  accept=".pdf,.doc,.docx"
  multiple
  helper="Formats acceptés: PDF, DOC, DOCX"
  :validation="{ state: 'error', message: 'Fichier trop volumineux' }"
/>
```

### **ColorField.vue** - Sélecteur de couleur complet
```vue
<ColorField 
  v-model="color"
  label="Couleur principale"
  format="hex"
  theme="dark"
  helper="Choisissez la couleur principale"
  :actions="['reset']"
/>
```

### **DateField.vue** - Sélecteur de date complet
```vue
<DateField 
  v-model="date"
  label="Date de naissance"
  format="YYYY-MM-DD"
  :min="'1900-01-01'"
  :max="'2024-12-31'"
  helper="Format: AAAA-MM-JJ"
  :validation="{ state: 'error', message: 'Date invalide' }"
/>
```

---

## 🎨 **FieldTemplate.vue** - Template unifié

Le `FieldTemplate` est le **template standardisé** utilisé par tous les composants Field. Il gère la structure, les labels, la validation et les actions.

### **Structure du template**
```vue
<template>
  <div :class="containerClasses">
    <!-- Label au-dessus -->
    <InputLabel v-if="labelConfig.top" :value="labelConfig.top" />
    
    <div class="relative flex items-center w-full">
      <!-- Label à gauche -->
      <InputLabel v-if="labelConfig.start" :value="labelConfig.start" />
      
      <!-- Bloc principal -->
      <div class="relative flex-1">
        <!-- Slot core (InputCore, TextareaCore, etc.) -->
        <slot name="core" :input-attrs="inputAttrs" :listeners="listeners" :input-ref="inputRef" />
        
        <!-- Actions contextuelles -->
        <div v-if="actionsToDisplay.length" class="absolute right-2 top-1/2 transform -translate-y-1/2">
          <Btn v-for="action in actionsToDisplay" :key="action.key" @click="action.onClick">
            <i :class="action.icon"></i>
          </Btn>
        </div>
      </div>
      
      <!-- Label à droite -->
      <InputLabel v-if="labelConfig.end" :value="labelConfig.end" />
    </div>
    
    <!-- Label en-dessous -->
    <InputLabel v-if="labelConfig.bottom" :value="labelConfig.bottom" />
    
    <!-- Validation -->
    <Validator v-if="validationState" :state="validationState" :message="validationMessage" />
    
    <!-- Helper -->
    <Helper v-if="helper" :value="helper" />
  </div>
</template>
```

### **Slots disponibles**
- `#core` : Composant Core (InputCore, TextareaCore, etc.)
- `#helper` : Contenu d'aide personnalisé
- `#overStart` : Actions au début du champ
- `#overEnd` : Actions à la fin du champ

---

## 🔧 **Composants utilitaires**

### **InputLabel.vue** - Label atomique
```vue
<InputLabel 
  value="Nom"
  :for="inputId"
  color="primary"
  size="md"
/>
```

### **Validator.vue** - Validateur atomique
```vue
<Validator 
  state="error"
  message="Champ requis"
  color="error"
/>
```

### **Helper.vue** - Helper atomique
```vue
<Helper 
  value="Minimum 8 caractères"
  color="info"
  size="sm"
/>
```

---

## 🎯 **Utilisation recommandée**

### **Pour les développeurs**
1. **Utiliser les composants Field** pour les interfaces complètes
2. **Utiliser les composants Core** uniquement pour des cas très spécifiques
3. **Hériter automatiquement** des props via `getInputPropsDefinition()`
4. **Utiliser le composable unifié** `useInputField()`

### **Pour les designers**
1. **Tous les composants** partagent la même API
2. **Styles cohérents** via les variants et couleurs
3. **Accessibilité native** intégrée
4. **Responsive design** automatique

---

## 🔗 **Liens utiles**

- **[ARCHITECTURE.md](./ARCHITECTURE.md)** - Architecture technique
- **[API_REFERENCE.md](./API_REFERENCE.md)** - Référence complète
- **[VALIDATION.md](./VALIDATION.md)** - Système de validation
- **[ACTIONS.md](./ACTIONS.md)** - Actions contextuelles

---

*Documentation générée le : {{ date('Y-m-d H:i:s') }}*
*Composants du Système d'Input KrosmozJDR v2.0*
