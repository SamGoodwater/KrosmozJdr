# 🎨 Styles et Personnalisation

## 📋 Vue d'ensemble

Le système d'input de KrosmozJDR utilise **DaisyUI** comme base de styles avec un système de personnalisation flexible. Tous les composants partagent la même API de styles pour une cohérence parfaite.

---

## 🎯 **API de Styles Unifiée**

### **Props de style principales**
```javascript
// API unifiée pour tous les composants
const styleProps = {
  variant: 'glass' | 'dash' | 'outline' | 'ghost' | 'soft',
  color: 'primary' | 'secondary' | 'accent' | 'info' | 'success' | 'warning' | 'error' | 'neutral',
  size: 'xs' | 'sm' | 'md' | 'lg' | 'xl',
  animation: boolean | string, // Booléen par défaut, ou string pour animation spécifique
  rounded: true | false,
  shadow: 'none' | 'sm' | 'md' | 'lg' | 'xl'
}
```

### **Utilisation simple**
```vue
<InputField 
  v-model="text"
  label="Texte"
  variant="glass"
  color="primary"
  size="md"
/>
```

---

## 🎨 **Variants de Style**

### **glass** - Effet glassmorphisme (par défaut)
Effet de verre avec transparence, blur et bordures glassmorphisme.
```vue
<InputField 
  v-model="text"
  label="Texte"
  variant="glass"
  color="primary"
/>
```

### **dash** - Bordure pointillée
Bordure pointillée avec fond semi-transparent, style discret.
```vue
<InputField 
  v-model="text"
  label="Texte"
  variant="dash"
  color="secondary"
/>
```

### **outline** - Bordure visible
Bordure visible avec fond transparent, met en avant le contour.
```vue
<InputField 
  v-model="text"
  label="Texte"
  variant="outline"
  color="accent"
/>
```

### **ghost** - Transparent
Fond transparent, bordure invisible, style minimaliste.
```vue
<InputField 
  v-model="text"
  label="Texte"
  variant="ghost"
  color="info"
/>
```

### **soft** - Style discret
Bordure inférieure uniquement, fond transparent, style élégant.
```vue
<InputField 
  v-model="text"
  label="Texte"
  variant="soft"
  color="success"
/>
```

---

## 🌈 **Couleurs**

### **Couleurs primaires**
```vue
<InputField v-model="text" label="Primary" color="primary" />
<InputField v-model="text" label="Secondary" color="secondary" />
<InputField v-model="text" label="Accent" color="accent" />
```

### **Couleurs sémantiques**
```vue
<InputField v-model="text" label="Success" color="success" />
<InputField v-model="text" label="Warning" color="warning" />
<InputField v-model="text" label="Error" color="error" />
<InputField v-model="text" label="Info" color="info" />
```

### **Couleurs neutres**
```vue
<InputField v-model="text" label="Neutral" color="neutral" />
```

### **Système de couleurs avec variable CSS**

Tous les composants utilisent la variable CSS `--color` définie via les classes :
- `color-primary` : `--color: var(--color-primary-500)`
- `color-secondary` : `--color: var(--color-secondary-500)`
- `color-accent` : `--color: var(--color-accent-500)`
- `color-info` : `--color: var(--color-info-500)`
- `color-success` : `--color: var(--color-success-500)`
- `color-warning` : `--color: var(--color-warning-500)`
- `color-error` : `--color: var(--color-error-500)`
- `color-neutral` : `--color: var(--color-neutral-500)`

Les styles utilisent ensuite `var(--color)` pour les couleurs dynamiques avec `color-mix()` pour les transparences.

---

## 📏 **Tailles**

### **Tailles disponibles**
```vue
<InputField v-model="text" label="XS" size="xs" />
<InputField v-model="text" label="SM" size="sm" />
<InputField v-model="text" label="MD" size="md" />
<InputField v-model="text" label="LG" size="lg" />
<InputField v-model="text" label="XL" size="xl" />
```

### **Tailles responsives**
```vue
<InputField 
  v-model="text"
  label="Responsive"
  size="sm md:md lg:lg"
/>
```

---

## ✨ **Animations**

### **Animations disponibles**
Par défaut, `animation` est un booléen qui active/désactive les animations de transition.
```vue
<InputField v-model="text" label="Avec animation" :animation="true" />
<InputField v-model="text" label="Sans animation" :animation="false" />
```

### **Animations spécifiques**
Certains composants supportent des animations spécifiques via une string.
```vue
<InputField v-model="text" label="Animation personnalisée" animation="pulse" />
```

### **Animations conditionnelles**
```vue
<InputField 
  v-model="text"
  label="Animé"
  :animation="isFocused"
/>
```

---

## 🔧 **Personnalisation Avancée**

### **Styles personnalisés via inputStyle**
```vue
<InputField 
  v-model="text"
  label="Personnalisé"
  :input-style="{
    backgroundColor: '#f0f0f0',
    borderColor: '#333',
    borderRadius: '8px',
    fontSize: '16px'
  }"
/>
```

### **Classes CSS personnalisées**
```vue
<InputField 
  v-model="text"
  label="Classes custom"
  class="my-custom-input"
  input-class="my-input-core"
/>
```

### **Styles conditionnels**
```vue
<InputField 
  v-model="text"
  label="Conditionnel"
  :class="{
    'input-error': hasError,
    'input-success': isValid,
    'input-warning': hasWarning
  }"
/>
```

---

## 🎯 **Styles par Type d'Input**

### **Input standard**
```vue
<InputField 
  v-model="text"
  label="Input"
  variant="glass"
  color="primary"
  size="md"
/>
```

### **Textarea**
```vue
<TextareaField 
  v-model="description"
  label="Textarea"
  variant="outline"
  color="secondary"
  size="lg"
/>
```

### **Select**
```vue
<SelectField 
  v-model="category"
  label="Select"
  variant="glass"
  color="accent"
  size="md"
/>
```

### **Checkbox**
```vue
<CheckboxField 
  v-model="accepted"
  label="Checkbox"
  color="success"
  size="md"
/>
```

### **Radio**
```vue
<RadioField 
  v-model="gender"
  label="Radio"
  color="primary"
  size="md"
/>
```

### **Toggle**
```vue
<ToggleField 
  v-model="notifications"
  label="Toggle"
  color="accent"
  size="lg"
/>
```

### **Range**
```vue
<RangeField 
  v-model="volume"
  label="Range"
  color="primary"
  size="md"
/>
```

### **Rating**
```vue
<RatingField 
  v-model="rating"
  label="Rating"
  color="warning"
  size="lg"
/>
```

### **File**
```vue
<FileField 
  v-model="file"
  label="File"
  variant="outline"
  color="info"
  size="md"
/>
```

### **Color**
```vue
<ColorField 
  v-model="color"
  label="Color"
  variant="glass"
  color="primary"
  size="md"
/>
```

### **Date**
```vue
<DateField 
  v-model="date"
  label="Date"
  variant="glass"
  color="secondary"
  size="md"
/>
```

---

## 🎨 **Thèmes et Modes**

### **Mode sombre automatique**
```vue
<InputField 
  v-model="text"
  label="Auto dark"
  variant="glass"
  color="primary"
  class="dark:bg-gray-800 dark:text-white"
/>
```

### **Thème personnalisé**
```vue
<InputField 
  v-model="text"
  label="Thème custom"
  :input-style="{
    '--tw-bg-opacity': '0.1',
    '--tw-border-opacity': '0.2',
    '--tw-text-opacity': '0.9'
  }"
/>
```

---

## 🔄 **Styles Réactifs**

### **Styles basés sur l'état**
```vue
<InputField 
  v-model="text"
  label="Réactif"
  :class="{
    'input-focus': isFocused,
    'input-filled': text.length > 0,
    'input-valid': isValid,
    'input-error': hasError
  }"
/>
```

### **Styles basés sur la validation**
```vue
<InputField 
  v-model="email"
  label="Email"
  :validation="emailValidation"
  :class="{
    'border-success': emailValidation?.state === 'success',
    'border-error': emailValidation?.state === 'error',
    'border-warning': emailValidation?.state === 'warning'
  }"
/>
```

---

## 🎯 **Styles pour les Actions**

### **Actions avec styles personnalisés**
```vue
<InputField 
  v-model="text"
  label="Actions stylées"
  :actions="[
    { key: 'copy', color: 'success', size: 'sm' },
    { key: 'clear', color: 'error', size: 'sm' }
  ]"
/>
```

### **Actions avec variants**
```vue
<InputField 
  v-model="text"
  label="Actions variants"
  :actions="[
    { key: 'copy', variant: 'outline' },
    { key: 'clear', variant: 'ghost' }
  ]"
/>
```

---

## 🚀 **Optimisation des Styles**

### **Styles optimisés**
```vue
<InputField 
  v-model="text"
  label="Optimisé"
  variant="glass"
  color="primary"
  size="md"
  class="transition-all duration-200 ease-in-out"
/>
```

### **Styles avec focus**
```vue
<InputField 
  v-model="text"
  label="Focus"
  class="focus-within:ring-2 focus-within:ring-primary focus-within:ring-opacity-50"
/>
```

### **Styles avec hover**
```vue
<InputField 
  v-model="text"
  label="Hover"
  class="hover:shadow-lg hover:scale-105 transition-all duration-200"
/>
```

---

## 🎨 **Classes Utilitaires Glassmorphisme**

### **Bordures Glass**
Classes pour les bordures glassmorphisme avec différentes intensités :
```vue
<div class="border-glass-md">Contenu avec bordure glass</div>
<div class="border-glass-tl-lg">Bordure glass top-left</div>
<div class="border-glass-x-xl">Bordure glass horizontale</div>
```

**Tailles disponibles** : `xs`, `sm`, `md`, `lg`, `xl`  
**Directions** : `t` (top), `r` (right), `b` (bottom), `l` (left), `x` (horizontal), `y` (vertical), et combinaisons (`tl`, `tr`, `bl`, `br`, etc.)

### **Box Glass**
Classes pour les box glass complètes (bordure + backdrop) :
```vue
<div class="box-glass-md">Contenu avec box glass</div>
<div class="box-glass-tl-lg">Box glass top-left</div>
```

**Tailles disponibles** : `xs`, `sm`, `md`, `lg`, `xl`  
**Directions** : Mêmes directions que `border-glass-*`

### **Backdrop Glass**
Classes pour le backdrop blur uniquement :
```vue
<div class="bd-glass-md">Contenu avec backdrop blur</div>
```

**Tailles disponibles** : `xs`, `sm`, `md`, `lg`, `xl`

## 🎨 **Personnalisation CSS**

### **Variables CSS personnalisées**
```css
/* Dans votre CSS */
:root {
  /* Les couleurs sont définies via les classes color-* */
  /* Utilisez var(--color) dans vos styles personnalisés */
}

.input-custom {
  --color: var(--color-primary-500); /* Définir la couleur */
  color: var(--color);
  border-color: color-mix(in srgb, var(--color) 50%, transparent);
  background-color: color-mix(in srgb, var(--color) 10%, transparent);
}
```

### **Classes utilitaires**
```vue
<InputField 
  v-model="text"
  label="Utilitaires"
  class="
    bg-gradient-to-r from-blue-500 to-purple-500
    text-white
    border-2 border-white border-opacity-20
    rounded-xl
    shadow-xl
    backdrop-blur-sm
  "
/>
```

---

## 🔧 **Responsive Design**

### **Styles responsifs**
```vue
<InputField 
  v-model="text"
  label="Responsive"
  size="sm md:md lg:lg"
  class="
    w-full
    md:w-96
    lg:w-128
  "
/>
```

### **Breakpoints personnalisés**
```vue
<InputField 
  v-model="text"
  label="Breakpoints"
  class="
    text-sm md:text-base lg:text-lg
    p-2 md:p-3 lg:p-4
    rounded-md md:rounded-lg lg:rounded-xl
  "
/>
```

---

## 🚀 **Bonnes Pratiques**

### ✅ **À faire**
- Utiliser les variants DaisyUI pour la cohérence
- Respecter la hiérarchie des couleurs
- Utiliser les tailles appropriées selon le contexte
- Optimiser les performances avec des transitions CSS
- Tester sur différents écrans et thèmes

### ❌ **À éviter**
- Ne pas surcharger avec trop de styles personnalisés
- Ne pas ignorer l'accessibilité (contraste, focus)
- Ne pas utiliser de couleurs non sémantiques
- Ne pas oublier le responsive design

---

## 🔗 **Liens utiles**

- **[COMPONENTS.md](./COMPONENTS.md)** - Guide des composants
- **[API_REFERENCE.md](./API_REFERENCE.md)** - Référence complète
- **[USAGE_EXAMPLES.md](./USAGE_EXAMPLES.md)** - Exemples d'utilisation
- **[DaisyUI Documentation](https://daisyui.com/)** - Documentation DaisyUI

---

*Documentation générée le : {{ date('Y-m-d H:i:s') }}*
*Styles et Personnalisation KrosmozJDR v2.0*
