# Intégration Color Picker avec vue-color-kit

## Vue d'ensemble

Les composants de couleur utilisent **vue-color-kit** pour fournir une expérience de sélection de couleur moderne et accessible, tout en s'intégrant parfaitement avec DaisyUI et Vue 3.

## Installation de vue-color-kit

### Installation via npm

```bash
npm install vue-color-kit
```

Ou avec pnpm (recommandé pour votre projet) :

```bash
pnpm add vue-color-kit
```

## Composants disponibles

### ColorCore (Atom)

Composant de base pour les sélecteurs de couleur, utilisant le composant vue-color-kit.

#### Props principales

- `modelValue` : String - La couleur sélectionnée (format hex, rgb, rgba, hsl, hsla)
- `format` : String - Format de couleur (défaut: 'hex')
- `theme` : String - Thème du color picker (défaut: 'dark')
- `colorsDefault` : Array - Palette de couleurs par défaut
- `colorsHistoryKey` : String - Clé pour l'historique des couleurs
- `suckerHide` : Boolean - Masquer le pipette (défaut: true)

#### Exemples d'utilisation

```vue
<!-- Simple -->
<ColorCore v-model="color" />

<!-- Avec format personnalisé -->
<ColorCore 
  v-model="color" 
  format="rgb"
  theme="light"
/>

<!-- Avec palette personnalisée -->
<ColorCore 
  v-model="color"
  :colorsDefault="['#FF0000', '#00FF00', '#0000FF']"
/>

<!-- Avec style personnalisé -->
<ColorCore 
  v-model="color"
  :style="{ variant: 'glass', color: 'primary', size: 'lg' }"
/>
```

### ColorField (Molecule)

Composant complet orchestrant ColorCore avec labels, validation, helpers et actions contextuelles.

#### Props principales

- `label` : String ou Object - Labels avec positions multiples
- `format` : String - Format de couleur (hex, rgb, rgba, hsl, hsla)
- `theme` : String - Thème du color picker (light, dark)
- `colorsDefault` : Array - Palette de couleurs par défaut
- `showValue` : Boolean - Afficher la couleur actuelle (défaut: true)
- `showPreview` : Boolean - Afficher l'aperçu de la couleur (défaut: true)
- `showFormat` : Boolean - Afficher le format de la couleur (défaut: true)
- `showRandom` : Boolean - Afficher le bouton couleur aléatoire (défaut: true)
- `showClear` : Boolean - Afficher le bouton "Effacer" (défaut: true)

#### Exemples d'utilisation

```vue
<!-- Simple -->
<ColorField label="Couleur principale" v-model="primaryColor" />

<!-- Avec format personnalisé -->
<ColorField 
  label="Couleur" 
  v-model="color"
  format="rgb"
  theme="light"
/>

<!-- Avec palette personnalisée -->
<ColorField 
  label="Couleur" 
  v-model="color"
  :colorsDefault="['#FF0000', '#00FF00', '#0000FF']"
/>

<!-- Avec positions de labels -->
<ColorField 
  :label="{ start: 'Couleur', end: 'Format: HEX' }" 
  v-model="color" 
/>

<!-- Avec actions personnalisées -->
<ColorField label="Couleur" v-model="color">
  <template #overStart>
    <Btn variant="ghost" size="xs">
      <i class="fa-solid fa-palette"></i>
    </Btn>
  </template>
  <template #overEnd>
    <Btn variant="ghost" size="xs" @click="setRandomColor">
      <i class="fa-solid fa-dice"></i>
    </Btn>
  </template>
</ColorField>

<!-- Avec validation -->
<ColorField 
  label="Couleur" 
  v-model="color"
  :validation="{ state: 'error', message: 'Couleur invalide' }"
/>

<!-- Avec style objet -->
<ColorField 
  label="Couleur" 
  v-model="color"
  :style="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }"
/>

<!-- Avec label complexe -->
<ColorField :label="{ start: 'Couleur' }" v-model="color">
  <template #labelStart>
    <span class="flex items-center gap-2">
      <i class="fa-solid fa-palette"></i>
      Couleur du thème
    </span>
  </template>
</ColorField>

<!-- Sans affichage de valeur -->
<ColorField 
  v-model="color" 
  :showValue="false"
  :showPreview="false"
/>
```

## Fonctionnalités avancées

### Formats de couleur supportés

- **hex** : `#FF0000` (format par défaut)
- **rgb** : `rgb(255, 0, 0)`
- **rgba** : `rgba(255, 0, 0, 1)`
- **hsl** : `hsl(0, 100%, 50%)`
- **hsla** : `hsla(0, 100%, 50%, 1)`

### Palette de couleurs par défaut

```javascript
const defaultColors = [
    '#000000', '#FFFFFF', '#FF1900', '#F47365', '#FFB243', '#FFE623',
    '#6EFF2A', '#1BC7B1', '#00BEFF', '#2E81FF', '#5D61FF', '#FF89CF',
    '#FC3CAD', '#BF3DCE', '#8E00A7', 'rgba(0,0,0,0)'
];
```

### Actions automatiques

Actions disponibles dans ColorField :

- **Aléatoire** : Génère une couleur aléatoire
- **Transparent** : Définit la couleur à transparent
- **Noir** : Définit la couleur à noir
- **Blanc** : Définit la couleur à blanc
- **Effacer** : Efface la couleur sélectionnée
- **Reset** : Remet la valeur initiale (si useFieldComposable)

### Affichage intelligent

Le composant affiche automatiquement :

- **Aperçu de couleur** : Carré coloré avec la couleur sélectionnée
- **Valeur formatée** : Code couleur selon le format choisi
- **Icône de format** : Icône selon le type de format
- **Badge de format** : Badge avec le format actuel
- **Couleur de contraste** : Texte adapté pour la lisibilité

### Validation

Support complet de la validation :

```vue
<ColorField 
  label="Couleur" 
  v-model="color"
  :validation="{ 
    state: 'error', 
    message: 'Couleur invalide',
    showNotification: true 
  }"
/>
```

## Styles et thèmes

### Variants disponibles

- `glass` : Effet de verre avec backdrop-filter
- `dash` : Bordure pointillée
- `outline` : Bordure simple
- `ghost` : Fond invisible
- `soft` : Style doux avec bordure inférieure

### Couleurs

Toutes les couleurs DaisyUI sont supportées :

- `primary`, `secondary`, `accent`
- `info`, `success`, `warning`, `error`
- `neutral`

### Tailles

- `xs`, `sm`, `md`, `lg`, `xl`

### Thèmes du color picker

- `light` : Thème clair
- `dark` : Thème sombre (défaut)

## Intégration avec le système de notifications

Le composant s'intègre automatiquement avec le système de notifications :

```vue
<ColorField 
  label="Couleur" 
  v-model="color"
  :validation="{ 
    state: 'success', 
    message: 'Couleur valide !',
    showNotification: true 
  }"
/>
```

## Accessibilité

Le composant respecte les standards d'accessibilité :

- **ARIA labels** pour tous les éléments interactifs
- **Navigation clavier** complète
- **Screen reader** support
- **Contraste** respectant les standards WCAG
- **Couleur de contraste** automatique pour le texte

## Personnalisation avancée

### Slots disponibles

```vue
<ColorField v-model="color">
  <!-- Affichage personnalisé de la valeur -->
  <template #valueDisplay>
    <span class="text-lg font-bold">
      {{ formatCustomColor(color) }}
    </span>
  </template>
  
  <!-- Actions personnalisées -->
  <template #overStart>
    <Btn @click="setCustomColor">Custom</Btn>
  </template>
  <template #overEnd>
    <Btn @click="setBrandColor">Brand</Btn>
  </template>
</ColorField>
```

### Styles personnalisés

```scss
// Personnalisation des couleurs du color picker
.color-picker-container {
  --color-picker-primary: var(--color-primary, #3b82f6);
  
  // Palette de couleurs
  .color-palette {
    .color-item {
      &:hover {
        border-color: var(--color-picker-primary);
      }
      
      &.active {
        border-color: var(--color-picker-primary);
        box-shadow: 0 0 0 2px rgba(var(--color-picker-primary), 0.3);
      }
    }
  }
  
  // Sliders
  .color-slider {
    input[type="range"] {
      background: linear-gradient(to right, transparent, var(--color-picker-primary));
    }
  }
}
```

## Migration depuis d'autres composants

### Depuis un input color natif

```vue
<!-- Avant -->
<input type="color" v-model="color" />

<!-- Après -->
<ColorField label="Couleur" v-model="color" />
```

### Depuis un composant de couleur personnalisé

```vue
<!-- Avant -->
<CustomColorPicker v-model="color" />

<!-- Après -->
<ColorField 
  label="Couleur" 
  v-model="color"
  :style="{ variant: 'glass', color: 'primary' }"
/>
```

## Dépannage

### vue-color-kit non trouvé

Si vous voyez l'erreur "vue-color-kit not found", assurez-vous d'avoir installé le package :

```bash
pnpm add vue-color-kit
```

### Styles non appliqués

Vérifiez que DaisyUI est correctement configuré dans votre `tailwind.config.js` :

```javascript
module.exports = {
  plugins: [require("daisyui")],
  daisyui: {
    themes: ["light", "dark"],
  },
}
```

### Problèmes de format

Assurez-vous que le format est compatible avec vue-color-kit :

- `hex` : Format hexadécimal (#RRGGBB)
- `rgb` : Format RGB (rgb(r, g, b))
- `rgba` : Format RGBA (rgba(r, g, b, a))
- `hsl` : Format HSL (hsl(h, s%, l%))
- `hsla` : Format HSLA (hsla(h, s%, l%, a))

## Fonctionnalités uniques

### Historique des couleurs

Le composant garde automatiquement un historique des couleurs utilisées :

```vue
<ColorField 
  label="Couleur" 
  v-model="color"
  colorsHistoryKey="my-app-colors"
/>
```

### Pipette (Color Picker)

Le composant supporte la pipette pour sélectionner des couleurs depuis l'écran :

```vue
<ColorField 
  label="Couleur" 
  v-model="color"
  :suckerHide="false"
/>
```

### Palette personnalisée

Vous pouvez définir votre propre palette de couleurs :

```vue
<ColorField 
  label="Couleur" 
  v-model="color"
  :colorsDefault="[
    '#FF0000', '#00FF00', '#0000FF',
    '#FFFF00', '#FF00FF', '#00FFFF'
  ]"
/>
```

## Ressources

- [Documentation vue-color-kit](https://www.vuescript.com/color-picker-kit/)
- [Guide d'accessibilité WCAG](https://www.w3.org/WAI/WCAG21/quickref/)
- [Spécifications CSS Color](https://www.w3.org/TR/css-color-3/) 