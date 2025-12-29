# Système d'Avatar

## Vue d'ensemble

Le système d'avatar de KrosmozJDR utilise le composant `Avatar.vue` pour afficher des avatars utilisateur avec un système de fallback intelligent. Il intègre le nouvel utilitaire de couleurs basé sur `colord` pour une gestion robuste et flexible des couleurs.

## Architecture

### Composant Avatar

**Fichier :** `resources/js/Pages/Atoms/data-display/Avatar.vue`

Le composant Avatar gère l'affichage des avatars avec un système de fallback en cascade :

1. **Image principale** (`src`) - Si disponible et valide
2. **Image par défaut** (`defaultAvatar`) - Si l'image principale n'est pas disponible
3. **Initiales avec couleur** - Générées à partir du `label` avec une couleur pastel
4. **Image de fallback** (`/storage/images/no_found.svg`) - Image générique en dernier recours

### Utilitaire de Couleurs

**Fichier :** `resources/js/Utils/color/Color.js`

Nouvel utilitaire complet basé sur `colord` offrant :

- **Génération de couleurs** depuis des chaînes de caractères
- **Ajustements** (luminosité, saturation, teinte)
- **Normalisation** selon des contraintes
- **Conversion** entre tous les formats de couleur
- **Contraste WCAG** et ajustements automatiques
- **Support Tailwind** avec conversion automatique

#### Mapping “label → Tailwind token”

Le projet expose aussi un helper pour associer une **couleur Tailwind** (token `color-shade`, ex: `blue-500`) à :
- une lettre / un mot / une phrase,
- ou un nombre (support jusqu’à **20** pour des progressions “niveau 1..20”).

Fonction :

- `getTailwindTokenFromLabel(input, options)`

Exemples :

```js
import { getTailwindTokenFromLabel } from "@/Utils/color/Color";

getTailwindTokenFromLabel("Alice") // ex: "emerald-500" (stable)
getTailwindTokenFromLabel("Niveau 12", { mode: "numericProgression", baseColor: "violet" }) // ex: "violet-700"
getTailwindTokenFromLabel("Bob", { mode: "alphabetical", tone: "light" }) // ex: "amber-200"
```

## API du Composant Avatar

### Props

```javascript
{
  src: String,              // URL de l'image principale
  alt: String,              // Texte alternatif (obligatoire)
  label: String,            // Texte pour générer les initiales
  defaultAvatar: String,    // URL de l'image par défaut
  size: String,             // Taille (xs, sm, md, lg, xl, 2xl, 3xl, 4xl)
  rounded: String,          // Arrondi (sm, md, lg, xl, full)
  ring: String,             // Anneau (xs, sm, md, lg, xl, 2xl, 3xl, 4xl)
  ringColor: String,        // Couleur de l'anneau
  ringOffset: String,       // Offset de l'anneau
  ringOffsetColor: String   // Couleur de l'offset
}
```

### Exemples d'utilisation

```vue
<!-- Avatar avec image -->
<Avatar 
  src="/img/avatar.jpg" 
  alt="Avatar utilisateur" 
  size="lg" 
/>

<!-- Avatar avec initiales -->
<Avatar 
  label="John Doe" 
  alt="John Doe" 
  size="md" 
/>

<!-- Avatar avec image par défaut -->
<Avatar 
  defaultAvatar="/img/default-avatar.png" 
  label="Utilisateur" 
  alt="Utilisateur" 
  size="lg" 
/>

<!-- Avatar avec fallback -->
<Avatar 
  label="" 
  alt="Sans label" 
  size="md" 
/>
```

## API de l'Utilitaire de Couleurs

### Fonctions principales

#### `generateColorFromString(input, options)`

Génère une couleur à partir d'une chaîne de caractères.

```javascript
import { generateColorFromString } from '@/Utils/color/Color';

// Génération basique
const color = generateColorFromString("John Doe"); // Retourne en hex

// Avec ajustements et normalisation
const color = generateColorFromString("John Doe", {
  adjustments: { lightness: 0.2, saturation: 0.3 },
  normalize: {
    minLightness: 0.3,
    maxLightness: 0.7,
    minSaturation: 0.4,
    maxSaturation: 0.8
  },
  format: 'rgb',
  fallback: '#3b82f6'
});
```

#### `adjustColor(color, adjustments, options)`

Ajuste une couleur selon les paramètres fournis.

```javascript
import { adjustColor } from '@/Utils/color/Color';

// Éclaircir
const lighter = adjustColor('#ff6b6b', { lightness: 0.2 });

// Assombrir
const darker = adjustColor('#ff6b6b', { lightness: -0.2 });

// Modifier la saturation
const moreSaturated = adjustColor('#ff6b6b', { saturation: 0.3 });

// Rotation de teinte
const rotated = adjustColor('#ff6b6b', { hue: 45 });
```

#### `normalizeColor(color, constraints, options)`

Normalise une couleur selon des contraintes.

```javascript
import { normalizeColor } from '@/Utils/color/Color';

const normalized = normalizeColor('#ffcccc', {
  minLightness: 0.3,
  maxLightness: 0.7,
  minSaturation: 0.4,
  maxSaturation: 0.8
});
```

#### `convertColor(color, targetFormat, fallback)`

Convertit une couleur vers un format spécifique.

```javascript
import { convertColor } from '@/Utils/color/Color';

// Conversion explicite
const rgb = convertColor('#3b82f6', 'rgb'); // "rgb(59, 130, 246)"
const hsl = convertColor('#3b82f6', 'hsl'); // "hsl(217, 91%, 60%)"

// Garder le format d'entrée
const auto = convertColor('rgb(59, 130, 246)', 'auto'); // "rgb(59, 130, 246)"
```

#### `getContrastRatio(color1, color2)`

Calcule le ratio de contraste entre deux couleurs.

```javascript
import { getContrastRatio, isContrastValid } from '@/Utils/color/Color';

const ratio = getContrastRatio('#ffffff', '#000000'); // 21
const isValid = isContrastValid('#ffffff', '#000000', 'AA', 'normal'); // true
```

#### `getNearestTailwindColor(color, options)`

Trouve la couleur Tailwind la plus proche.

```javascript
import { getNearestTailwindColor } from '@/Utils/color/Color';

const tailwindColor = getNearestTailwindColor('#ff6b6b'); // "red-500"
```

## Système de Fallback

### Ordre de priorité

1. **Image principale** (`src`)
   - Si l'image existe et se charge correctement
   - Gestion des erreurs de chargement

2. **Image par défaut** (`defaultAvatar`)
   - Si l'image principale n'est pas disponible
   - Gestion des erreurs de chargement

3. **Initiales avec couleur**
   - Générées à partir du `label` ou `alt`
   - Couleur générée par `generateColorFromString`
   - Normalisée pour être ni trop claire ni trop foncée

4. **Image de fallback** (`/storage/images/no_found.svg`)
   - Image générique du projet
   - Utilisée en dernier recours

### Génération des initiales

```javascript
// Un mot : première lettre
"John" → "J"

// Plusieurs mots : première lettre des deux premiers mots
"John Doe" → "JD"
"Marie Claire Dupont" → "MC"
```

## Intégration avec le Design System

### Tailles disponibles

- `xs` : 0.875rem (14px)
- `sm` : 1rem (16px)
- `md` : 1.5rem (24px) - **Défaut**
- `lg` : 2rem (32px)
- `xl` : 2.5rem (40px)
- `2xl` : 4rem (64px)
- `3xl` : 6rem (96px)
- `4xl` : 8rem (128px)

### Styles DaisyUI

Le composant utilise les classes DaisyUI pour :
- Tailles (`avatar-xs`, `avatar-sm`, etc.)
- Anneaux (`ring`, `ring-offset`)
- Arrondis (`rounded-full`, etc.)

## Utilisation dans le Projet

### Header utilisateur

```vue
<Avatar
  :src="user.avatar"
  :label="user.name"
  :alt="user.name"
  size="md"
/>
```

### Pages de profil

```vue
<Avatar
  :src="user.avatar"
  :label="user.name"
  :alt="user.name"
  size="xl"
  ring="md"
  ringColor="primary"
/>
```

### Listes d'utilisateurs

```vue
<Avatar
  :label="user.name"
  :alt="user.name"
  size="sm"
/>
```

## Avantages du Nouvel Utilitaire

### 1. Flexibilité
- Support de tous les formats de couleur (HEX, RGB, HSL, HSV, LAB, LCH, OKLCH)
- Ajustements granulaires (luminosité, saturation, teinte)
- Normalisation automatique selon des contraintes

### 2. Robustesse
- Validation des couleurs
- Gestion des erreurs avec fallbacks
- Respect des standards WCAG

### 3. Performance
- Utilisation de `colord` (2.9kB gzipped)
- Calculs optimisés
- Pas de dépendances lourdes

### 4. Maintenabilité
- API cohérente et intuitive
- Documentation complète
- Tests automatisés

## Migration depuis l'Ancien Système

### Anciennes fonctions (dépréciées)

```javascript
// ❌ Ancien système
import { getAvatarColor, getColorFromString } from '@/Utils/color/Color';

const color = getAvatarColor("John Doe");
const color2 = getColorFromString("John Doe", 500);
```

### Nouvelles fonctions (recommandées)

```javascript
// ✅ Nouveau système
import { generateColorFromString } from '@/Utils/color/Color';

const color = generateColorFromString("John Doe", {
  normalize: {
    minLightness: 0.3,
    maxLightness: 0.7,
    minSaturation: 0.4,
    maxSaturation: 0.8
  },
  format: 'hex',
  fallback: '#3b82f6'
});
```

## Tests

### Tests automatisés

```bash
# Test du code du composant Avatar
node playwright/tasks/test-avatar-code.js

# Test de l'utilitaire de couleurs
node playwright/tasks/test-new-color-utility.js
```

### Tests manuels

1. **Page Home** : Section "Système d'Avatar"
2. **Header** : Avatar utilisateur connecté
3. **Pages de profil** : Avatar en grand format

## Dépendances

- **colord** : Bibliothèque de gestion des couleurs
- **Vue 3** : Framework frontend
- **DaisyUI** : Classes CSS utilitaires
- **Tailwind CSS** : Framework CSS

## Support

Pour toute question ou problème avec le système d'avatar :

1. Consulter la documentation de `colord` : https://github.com/omgovich/colord
2. Vérifier les tests automatisés
3. Consulter les exemples dans `resources/js/Pages/Home.vue`
