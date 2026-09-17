# 🎯 Composants Spécialisés

## 📋 Vue d'ensemble

Les composants **Date** et **Color** sont des composants spécialisés qui utilisent des librairies externes avec des **systèmes de fallback intelligents**. Ils basculent automatiquement vers les inputs HTML natifs si les librairies ne sont pas disponibles.

---

## 📅 **Composant Date (DateCore/DateField)**

### **Librairie Externe : Cally**
- **Source** : [DaisyUI Calendar](https://daisyui.com/components/calendar/)
- **Web Component** : `calendar-date` avec styles DaisyUI
- **Fallback** : `input type="date"` HTML natif

### **Système de Fallback**
```javascript
// Vérification automatique de la disponibilité
const isCallyAvailable = ref(false);

// Vérification immédiate
if (typeof window !== 'undefined') {
    try {
        const isRegistered = window.customElements?.get('calendar-date');
        const testElement = document.createElement('calendar-date');
        const isCustomElement = testElement.constructor !== HTMLElement;
        
        isCallyAvailable.value = isRegistered || isCustomElement;
    } catch (error) {
        console.warn('DateCore: Cally n\'est pas disponible, utilisation du fallback');
        isCallyAvailable.value = false;
    }
}
```

### **Installation de Cally**
```bash
# Option 1: CDN (recommandé)
<script type="module" src="https://unpkg.com/cally"></script>

# Option 2: NPM
npm install cally
```

### **Utilisation**
```vue
<!-- Avec Cally (si disponible) -->
<DateField 
  v-model="date"
  label="Date de naissance"
  :min="new Date('1900-01-01')"
  :max="new Date()"
/>

<!-- Fallback automatique vers input HTML natif -->
<input type="date" v-model="date" />
```

### **Props Spécifiques**
```javascript
const dateProps = {
  min: 'Date minimum sélectionnable',
  max: 'Date maximum sélectionnable',
  format: 'Format d\'affichage (YYYY-MM-DD par défaut)',
  locale: 'Locale pour l\'affichage (fr par défaut)'
}
```

### **Événements**
```javascript
const dateEvents = {
  'update:modelValue': 'Valeur mise à jour',
  'change': 'Date changée',
  'select': 'Date sélectionnée',
  'clear': 'Date effacée',
  'open': 'Calendrier ouvert',
  'close': 'Calendrier fermé'
}
```

---

## 🎨 **Composant Color (ColorCore/ColorField)**

### **Librairie Externe : vue-color-kit**
- **Source** : [Vue Color Picker Kit](https://www.vuescript.com/color-picker-kit/)
- **Composant Vue** : `ColorPicker` avec interface avancée
- **Fallback** : `input type="color"` HTML natif

### **Système de Fallback**
```javascript
// Import dynamique de vue-color-kit
let ColorPicker = null;
const isColorKitReady = ref(false);

async function loadColorKit() {
    try {
        const colorKit = await import('vue-color-kit');
        ColorPicker = colorKit.ColorPicker;
        isColorKitReady.value = true;
    } catch (error) {
        console.warn('vue-color-kit non disponible, utilisation du fallback HTML natif');
        isColorKitReady.value = false;
    }
}
```

### **Installation de vue-color-kit**
```bash
# Installation via NPM
npm install vue-color-kit
```

### **Utilisation**
```vue
<!-- Avec vue-color-kit (si disponible) -->
<ColorField 
  v-model="color"
  label="Couleur principale"
  format="hex"
  theme="dark"
/>

<!-- Fallback automatique vers input HTML natif -->
<input type="color" v-model="color" />
```

### **Props Spécifiques**
```javascript
const colorProps = {
  format: 'Format de couleur (hex, rgb, rgba, hsl, hsla)',
  theme: 'Thème du color picker (light, dark)',
  colorsDefault: 'Palette de couleurs par défaut',
  colorsHistoryKey: 'Clé pour l\'historique des couleurs',
  suckerHide: 'Masquer le pipette',
  showValue: 'Afficher la valeur',
  showPreview: 'Afficher l\'aperçu',
  showFormat: 'Afficher le sélecteur de format',
  showRandom: 'Afficher le bouton aléatoire',
  showClear: 'Afficher le bouton effacer'
}
```

### **Configuration Avancée**
```vue
<ColorField 
  v-model="brandColor"
  :colorsDefault="[
    '#FF1900', '#F47365', '#FFB243', '#FFE623',
    '#6EFF2A', '#1BC7B1', '#00BEFF', '#2E81FF'
  ]"
  format="hex"
  theme="light"
  :showRandom="true"
  :showClear="true"
/>
```

---

## 🔧 **Gestion des Dépendances**

### **Vérification Automatique**
Les composants vérifient automatiquement la disponibilité des librairies :

1. **Au chargement** : Vérification immédiate
2. **Au montage** : Vérification après le montage du composant
3. **Retry** : Nouvelles tentatives après 100ms et 500ms

### **Messages de Console**
```javascript
// DateCore
console.warn('DateCore: Cally n\'est pas disponible, utilisation du fallback input date HTML natif');

// ColorCore  
console.warn('vue-color-kit non disponible, utilisation du fallback HTML natif');
```

### **Fallback Transparent**
L'utilisateur ne voit aucune différence dans l'API :
```vue
<!-- Même API, comportement différent selon la disponibilité -->
<DateField v-model="date" label="Date" />
<ColorField v-model="color" label="Couleur" />
```

---

## 🎯 **Cas d'Usage**

### **Formulaire de Configuration**
```vue
<template>
  <form class="space-y-6">
    <!-- Date avec validation -->
    <DateField 
      v-model="config.releaseDate"
      label="Date de sortie"
      :min="new Date()"
      :validation="{ 
        state: 'success', 
        message: 'Date valide' 
      }"
    />
    
    <!-- Couleur avec palette personnalisée -->
    <ColorField 
      v-model="config.brandColor"
      label="Couleur de marque"
      :colorsDefault="brandColors"
      format="hex"
      theme="light"
    />
    
    <!-- Date avec format personnalisé -->
    <DateField 
      v-model="config.expiryDate"
      label="Date d'expiration"
      format="DD/MM/YYYY"
      locale="fr"
    />
  </form>
</template>

<script setup>
const brandColors = [
  '#FF1900', '#F47365', '#FFB243', '#FFE623',
  '#6EFF2A', '#1BC7B1', '#00BEFF', '#2E81FF'
];
</script>
```

### **Interface d'Administration**
```vue
<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Sélecteur de date avec historique -->
    <DateField 
      v-model="admin.lastLogin"
      label="Dernière connexion"
      readonly
    />
    
    <!-- Sélecteur de couleur avec thème sombre -->
    <ColorField 
      v-model="admin.themeColor"
      label="Couleur du thème"
      theme="dark"
      :showRandom="true"
    />
  </div>
</template>
```

---

## 🚀 **Bonnes Pratiques**

### ✅ **À faire**
- **Toujours tester** les deux modes (avec et sans librairie)
- **Utiliser les props spécifiques** pour personnaliser l'expérience
- **Gérer les erreurs** de format de date/couleur
- **Documenter les dépendances** dans le projet
- **Tester le fallback** en mode hors ligne

### ❌ **À éviter**
- **Ne pas dépendre** de fonctionnalités spécifiques à une librairie
- **Ne pas ignorer** les messages de console de fallback
- **Ne pas oublier** de gérer les formats de date/couleur
- **Ne pas surcharger** avec trop d'options

### 🔧 **Configuration Projet**
```javascript
// package.json - Dépendances optionnelles
{
  "dependencies": {
    // Dépendances principales
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

---

## 🔗 **Liens utiles**

### **Documentation Officielle**
- **[DaisyUI Calendar](https://daisyui.com/components/calendar/)** - Styles DaisyUI pour calendriers
- **[Cally Web Component](https://github.com/WickyNilliams/cally)** - Web component de calendrier
- **[Vue Color Picker Kit](https://www.vuescript.com/color-picker-kit/)** - Composant Vue pour sélecteur de couleur

### **Documentation Projet**
- **[COMPONENTS.md](./COMPONENTS.md)** - Guide des composants
- **[API_REFERENCE.md](./API_REFERENCE.md)** - Référence complète
- **[USAGE_EXAMPLES.md](./USAGE_EXAMPLES.md)** - Exemples d'utilisation

---

## 📝 **Notes Techniques**

### **Compatibilité Navigateur**
- **Cally** : Support moderne (Web Components)
- **vue-color-kit** : Vue 3 + navigateurs modernes
- **Fallback HTML** : Support universel

### **Performance**
- **Chargement asynchrone** : Les librairies se chargent en arrière-plan
- **Fallback immédiat** : Pas de blocage si librairie indisponible
- **Bundle size** : Les librairies ne sont pas incluses par défaut

### **Accessibilité**
- **Labels appropriés** : Support complet des labels
- **Navigation clavier** : Fonctionne dans les deux modes
- **ARIA** : Attributs d'accessibilité préservés

---

*Documentation générée le : {{ date('Y-m-d H:i:s') }}*
*Composants Spécialisés KrosmozJDR v2.0*
