# 📦 Guide d'Installation

## 📋 Vue d'ensemble

Ce guide explique comment installer et configurer les **dépendances optionnelles** pour les composants spécialisés Date et Color du système d'input KrosmozJDR.

---

## 🎯 **Composants Concernés**

### **Composants avec Dépendances Externes**
- **DateField/DateCore** : Dépend de **Cally** (web component)
- **ColorField/ColorCore** : Dépend de **vue-color-kit** (composant Vue)

### **Composants sans Dépendances**
- **InputField, TextareaField, SelectField, etc.** : Fonctionnent nativement
- **CheckboxField, RadioField, ToggleField, etc.** : Fonctionnent nativement

---

## 📅 **Installation de Cally (Date Picker)**

### **Option 1: CDN (Recommandé)**
```html
<!-- Dans le head de votre HTML -->
<script type="module" src="https://unpkg.com/cally"></script>
```

### **Option 2: NPM**
```bash
# Installation
npm install cally

# Ou avec pnpm
pnpm add cally

# Ou avec yarn
yarn add cally
```

### **Option 3: Import dans Vue**
```javascript
// Dans votre main.js ou app.js
import 'cally';
```

### **Vérification de l'Installation**
```javascript
// Test dans la console du navigateur
console.log('Cally disponible:', !!window.customElements.get('calendar-date'));
```

---

## 🎨 **Installation de vue-color-kit (Color Picker)**

### **Installation NPM**
```bash
# Installation
npm install vue-color-kit

# Ou avec pnpm
pnpm add vue-color-kit

# Ou avec yarn
yarn add vue-color-kit
```

### **Import dans Vue**
```javascript
// Import global dans main.js
import { ColorPicker } from 'vue-color-kit';
import 'vue-color-kit/dist/style.css';

// Ou import dynamique (recommandé)
// Le composant gère automatiquement l'import dynamique
```

### **Vérification de l'Installation**
```javascript
// Test dans la console du navigateur
import('vue-color-kit').then(module => {
  console.log('vue-color-kit disponible:', !!module.ColorPicker);
});
```

---

## 🔧 **Configuration Projet**

### **package.json**
```json
{
  "dependencies": {
    // Dépendances principales du projet
  },
  "devDependencies": {
    // Dépendances de développement
  },
  "optionalDependencies": {
    "cally": "^1.0.0",
    "vue-color-kit": "^1.0.0"
  }
}
```

### **Vite Configuration**
```javascript
// vite.config.js
export default {
  optimizeDeps: {
    include: [
      // Inclure les dépendances optionnelles
      'cally',
      'vue-color-kit'
    ]
  }
}
```

### **Webpack Configuration**
```javascript
// webpack.config.js
module.exports = {
  externals: {
    // Si vous préférez charger via CDN
    'cally': 'Cally',
    'vue-color-kit': 'VueColorKit'
  }
}
```

---

## 🚀 **Modes d'Utilisation**

### **Mode Complet (Avec Dépendances)**
```vue
<template>
  <!-- Interface avancée avec Cally -->
  <DateField 
    v-model="date"
    label="Date de naissance"
    :min="new Date('1900-01-01')"
    :max="new Date()"
  />
  
  <!-- Interface avancée avec vue-color-kit -->
  <ColorField 
    v-model="color"
    label="Couleur principale"
    format="hex"
    theme="dark"
  />
</template>
```

### **Mode Fallback (Sans Dépendances)**
```vue
<template>
  <!-- Fallback automatique vers input HTML natif -->
  <DateField v-model="date" label="Date" />
  <ColorField v-model="color" label="Couleur" />
</template>
```

---

## 🔍 **Détection Automatique**

### **Système de Fallback**
Les composants détectent automatiquement la disponibilité :

```javascript
// DateCore - Vérification Cally
const isCallyAvailable = ref(false);

if (typeof window !== 'undefined') {
    try {
        const isRegistered = window.customElements?.get('calendar-date');
        const testElement = document.createElement('calendar-date');
        const isCustomElement = testElement.constructor !== HTMLElement;
        
        isCallyAvailable.value = isRegistered || isCustomElement;
    } catch (error) {
        isCallyAvailable.value = false;
    }
}

// ColorCore - Import dynamique vue-color-kit
async function loadColorKit() {
    try {
        const colorKit = await import('vue-color-kit');
        ColorPicker = colorKit.ColorPicker;
        isColorKitReady.value = true;
    } catch (error) {
        isColorKitReady.value = false;
    }
}
```

### **Messages de Console**
```javascript
// Messages informatifs (non bloquants)
console.warn('DateCore: Cally n\'est pas disponible, utilisation du fallback');
console.warn('vue-color-kit non disponible, utilisation du fallback HTML natif');
```

---

## 🧪 **Tests et Validation**

### **Test de Fonctionnement**
```vue
<template>
  <div class="space-y-4">
    <!-- Test Date -->
    <div>
      <h3>Test Date Picker</h3>
      <DateField v-model="testDate" label="Test Date" />
      <p>Valeur: {{ testDate }}</p>
    </div>
    
    <!-- Test Color -->
    <div>
      <h3>Test Color Picker</h3>
      <ColorField v-model="testColor" label="Test Color" />
      <p>Valeur: {{ testColor }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const testDate = ref('');
const testColor = ref('#000000');
</script>
```

### **Vérification des Modes**
```javascript
// Dans la console du navigateur
// Vérifier le mode utilisé
console.log('Mode Date:', document.querySelector('input[type="date"]') ? 'Fallback' : 'Cally');
console.log('Mode Color:', document.querySelector('input[type="color"]') ? 'Fallback' : 'vue-color-kit');
```

---

## 🚨 **Dépannage**

### **Problèmes Courants**

#### **Cally ne se charge pas**
```bash
# Vérifier l'installation
npm list cally

# Réinstaller
npm uninstall cally
npm install cally

# Vérifier le CDN
curl https://unpkg.com/cally
```

#### **vue-color-kit ne se charge pas**
```bash
# Vérifier l'installation
npm list vue-color-kit

# Réinstaller
npm uninstall vue-color-kit
npm install vue-color-kit

# Vérifier les imports
node -e "import('vue-color-kit').then(console.log)"
```

#### **Erreurs de Build**
```javascript
// Ajouter aux externals si nécessaire
// vite.config.js
export default {
  build: {
    rollupOptions: {
      external: ['cally', 'vue-color-kit']
    }
  }
}
```

### **Logs de Debug**
```javascript
// Activer les logs détaillés
localStorage.setItem('debug-input-system', 'true');

// Vérifier les logs dans la console
console.log('Debug mode activé');
```

---

## 📚 **Ressources Supplémentaires**

### **Documentation Officielle**
- **[Cally Documentation](https://github.com/WickyNilliams/cally)** - Guide complet
- **[vue-color-kit Documentation](https://www.vuescript.com/color-picker-kit/)** - Guide d'utilisation
- **[DaisyUI Calendar](https://daisyui.com/components/calendar/)** - Styles et exemples

### **Exemples Avancés**
```vue
<!-- Date avec configuration avancée -->
<DateField 
  v-model="date"
  :min="new Date('2020-01-01')"
  :max="new Date('2030-12-31')"
  format="DD/MM/YYYY"
  locale="fr"
/>

<!-- Color avec palette personnalisée -->
<ColorField 
  v-model="color"
  :colorsDefault="['#FF0000', '#00FF00', '#0000FF']"
  format="hex"
  theme="light"
  :showRandom="true"
  :showClear="true"
/>
```

---

## 🎯 **Recommandations**

### **Pour le Développement**
- **Installer les dépendances** pour une expérience complète
- **Tester le fallback** en mode hors ligne
- **Documenter les versions** utilisées

### **Pour la Production**
- **Optimiser le bundle** avec les dépendances
- **Tester sur différents navigateurs**
- **Prévoir les fallbacks** pour la compatibilité

### **Pour la Maintenance**
- **Surveiller les mises à jour** des librairies
- **Tester les nouvelles versions**
- **Maintenir la compatibilité** avec les fallbacks

---

*Documentation générée le : {{ date('Y-m-d H:i:s') }}*
*Guide d'Installation KrosmozJDR v2.0*
