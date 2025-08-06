# Intégration Date/Calendar avec Cally

## Vue d'ensemble

Les composants de date utilisent le **web component Cally** pour fournir une expérience de sélection de date moderne et accessible, tout en s'intégrant parfaitement avec DaisyUI et Vue 3.

## Installation de Cally

### Option 1 : CDN (Recommandé pour le développement)

Ajoutez le script dans votre `index.html` :

```html
<script type="module" src="https://unpkg.com/cally"></script>
```

### Option 2 : Installation via npm

```bash
npm install cally
```

Puis importez dans votre fichier principal :

```javascript
import "cally";
```

## Composants disponibles

### DateCore (Atom)

Composant de base pour les sélecteurs de date, utilisant le web component Cally.

#### Props principales

- `modelValue` : Date ou String - La date sélectionnée
- `min` : Date ou String - Date minimum autorisée
- `max` : Date ou String - Date maximum autorisée
- `format` : String - Format d'affichage (défaut: 'YYYY-MM-DD')
- `locale` : String - Locale pour l'affichage (défaut: 'fr')

#### Exemples d'utilisation

```vue
<!-- Simple -->
<DateCore v-model="date" />

<!-- Avec min/max -->
<DateCore 
  v-model="date" 
  :min="'2024-01-01'" 
  :max="'2024-12-31'" 
/>

<!-- Avec style personnalisé -->
<DateCore 
  v-model="date"
  :style="{ variant: 'glass', color: 'primary', size: 'lg' }"
/>

<!-- Avec icônes personnalisées -->
<DateCore v-model="date">
  <template #previous>
    <i class="fa-solid fa-chevron-left"></i>
  </template>
  <template #next>
    <i class="fa-solid fa-chevron-right"></i>
  </template>
</DateCore>
```

### DateField (Molecule)

Composant complet orchestrant DateCore avec labels, validation, helpers et actions contextuelles.

#### Props principales

- `label` : String ou Object - Labels avec positions multiples
- `min` / `max` : Date ou String - Plage de dates autorisée
- `format` : String - Format d'affichage
- `locale` : String - Locale pour l'affichage
- `showValue` : Boolean - Afficher la date sélectionnée (défaut: true)
- `showToday` : Boolean - Afficher le bouton "Aujourd'hui" (défaut: true)
- `showClear` : Boolean - Afficher le bouton "Effacer" (défaut: true)

#### Exemples d'utilisation

```vue
<!-- Simple -->
<DateField label="Date de naissance" v-model="birthDate" />

<!-- Avec plage de dates -->
<DateField 
  label="Date de naissance" 
  v-model="birthDate"
  :min="'1900-01-01'"
  :max="'2024-12-31'"
/>

<!-- Avec format personnalisé -->
<DateField 
  label="Date" 
  v-model="date"
  format="DD/MM/YYYY"
  locale="fr"
/>

<!-- Avec positions de labels -->
<DateField 
  :label="{ start: 'Date de début', end: 'Format: DD/MM/YYYY' }" 
  v-model="startDate" 
/>

<!-- Avec actions personnalisées -->
<DateField label="Date" v-model="date">
  <template #overStart>
    <Btn variant="ghost" size="xs">
      <i class="fa-solid fa-calendar-day"></i>
    </Btn>
  </template>
  <template #overEnd>
    <Btn variant="ghost" size="xs" @click="setToday">
      <i class="fa-solid fa-calendar-check"></i>
    </Btn>
  </template>
</DateField>

<!-- Avec validation -->
<DateField 
  label="Date" 
  v-model="date"
  :validation="{ state: 'error', message: 'Date invalide' }"
/>

<!-- Avec style objet -->
<DateField 
  label="Date" 
  v-model="date"
  :style="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }"
/>
```

## Fonctionnalités avancées

### Navigation clavier

Le calendrier supporte la navigation clavier complète :

- **Flèches** : Navigation entre les jours
- **Home/End** : Premier/dernier jour du mois
- **PageUp/PageDown** : Mois précédent/suivant
- **Enter/Espace** : Sélection de la date

### Affichage intelligent

Le composant affiche automatiquement :

- **Date formatée** selon la locale
- **Badge de statut** (Passé/Aujourd'hui/Cette semaine/Futur)
- **Icône dynamique** selon la période
- **Couleur contextuelle** selon la date

### Actions contextuelles

Actions automatiques disponibles :

- **Aujourd'hui** : Définit la date à aujourd'hui
- **Effacer** : Efface la date sélectionnée
- **Reset** : Remet la valeur initiale (si useFieldComposable)

### Validation

Support complet de la validation :

```vue
<DateField 
  label="Date de naissance" 
  v-model="birthDate"
  :validation="{ 
    state: 'error', 
    message: 'Vous devez avoir au moins 18 ans',
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

## Intégration avec le système de notifications

Le composant s'intègre automatiquement avec le système de notifications :

```vue
<DateField 
  label="Date" 
  v-model="date"
  :validation="{ 
    state: 'success', 
    message: 'Date valide !',
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

## Personnalisation avancée

### Slots disponibles

```vue
<DateField v-model="date">
  <!-- Icônes de navigation -->
  <template #previous>
    <i class="fa-solid fa-chevron-left"></i>
  </template>
  <template #next>
    <i class="fa-solid fa-chevron-right"></i>
  </template>
  
  <!-- Affichage personnalisé de la valeur -->
  <template #valueDisplay>
    <span class="text-lg font-bold">
      {{ formatCustomDate(date) }}
    </span>
  </template>
  
  <!-- Actions personnalisées -->
  <template #overStart>
    <Btn @click="setYesterday">Hier</Btn>
  </template>
  <template #overEnd>
    <Btn @click="setTomorrow">Demain</Btn>
  </template>
</DateField>
```

### Styles personnalisés

```scss
// Personnalisation des couleurs du calendrier
.cally {
  --cally-color: var(--color-primary, #3b82f6);
  
  // Jours sélectionnés
  [selected] {
    background-color: var(--cally-color);
    color: white;
  }
  
  // Jours hover
  [hover] {
    background-color: rgba(var(--cally-color), 0.1);
  }
}
```

## Migration depuis d'autres composants

### Depuis un input date natif

```vue
<!-- Avant -->
<input type="date" v-model="date" />

<!-- Après -->
<DateField label="Date" v-model="date" />
```

### Depuis un composant de date personnalisé

```vue
<!-- Avant -->
<CustomDatePicker v-model="date" />

<!-- Après -->
<DateField 
  label="Date" 
  v-model="date"
  :style="{ variant: 'glass', color: 'primary' }"
/>
```

## Dépannage

### Cally non trouvé

Si vous voyez l'erreur "Cally web component not found", assurez-vous d'avoir importé Cally :

```html
<!-- Dans index.html -->
<script type="module" src="https://unpkg.com/cally"></script>
```

Ou dans votre fichier principal :

```javascript
import "cally";
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

Assurez-vous que le format est compatible avec Cally :

- `YYYY-MM-DD` (recommandé)
- `DD/MM/YYYY`
- `MM/DD/YYYY`

## Ressources

- [Documentation Cally](https://cally.js.org/)
- [Documentation DaisyUI Calendar](https://daisyui.com/components/calendar/)
- [Guide d'accessibilité WCAG](https://www.w3.org/WAI/WCAG21/quickref/) 