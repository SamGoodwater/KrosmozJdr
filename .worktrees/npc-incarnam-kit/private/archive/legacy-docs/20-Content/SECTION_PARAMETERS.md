# Paramètres des Sections

## 📋 Paramètres communs à toutes les sections

Toutes les sections partagent les paramètres suivants, stockés directement dans la table `sections` :

### Paramètres de base

| Paramètre | Type | Nullable | Description | Valeur par défaut |
|-----------|------|----------|-------------|-------------------|
| `title` | `string` | ✅ Oui | Titre de la section | `null` |
| `slug` | `string` | ✅ Oui | Slug unique pour l'ancre de la section | `null` |
| `order` | `integer` | ❌ Non | Ordre d'affichage dans la page | `0` |
| `template` | `SectionType` (enum) | ❌ Non | Type de template de la section | - |

### Paramètres de visibilité et permissions

| Paramètre | Type | Valeurs possibles | Description | Valeur par défaut |
|-----------|------|-------------------|-------------|-------------------|
| `read_level` | `integer` | `0..5` (guest→super_admin) | Niveau minimal requis pour voir la section | `0` |
| `write_level` | `integer` | `0..5` (guest→super_admin) | Niveau minimal requis pour modifier la section | `4` |
| `state` | `string` | `raw`, `draft`, `playable`, `archived` | État de cycle de vie de la section | `draft` |

**Contrainte** : `write_level >= read_level`.

### Paramètres de style (dans `settings` JSON)

| Paramètre | Type | Description | Valeur par défaut |
|-----------|------|-------------|-------------------|
| `classes` | `string` | Classes CSS personnalisées à ajouter au conteneur | `null` |
| `customCss` | `string` | CSS personnalisé pour la section (sera injecté dans un `<style>` tag) | `null` |

**Note** : Les paramètres `classes` et `customCss` sont disponibles pour **toutes les sections** et seront toujours présents dans le modal de paramètres.

---

## 🎨 Paramètres spécifiques aux templates (dans `settings` JSON)

Chaque template peut définir ses propres paramètres additionnels qui seront stockés dans le champ `settings` (JSON) de la section.

### Structure proposée pour les paramètres dans les configs

Pour faciliter la création automatique des champs dans le modal de paramètres, chaque template peut définir ses paramètres dans son fichier `config.js` avec la structure suivante :

```javascript
export default {
  name: 'Image',
  description: '...',
  icon: 'fa-solid fa-image',
  value: 'image',
  supportsAutoSave: true,
  
  // Valeurs par défaut pour les settings
  defaultSettings: {
    align: 'center',
    size: 'md',
    zoom: 100,
  },
  
  // Définition des paramètres pour le modal
  // Chaque paramètre définit comment créer le champ dans le formulaire
  parameters: [
    {
      // Clé du paramètre (sera stocké dans settings[key])
      key: 'align',
      
      // Type de champ
      type: 'select', // 'text' | 'number' | 'select' | 'toggle' | 'textarea' | 'color'
      
      // Label affiché dans le formulaire
      label: 'Alignement',
      
      // Description/helper text
      description: 'Position de l\'image dans la section',
      
      // Valeur par défaut
      default: 'center',
      
      // Options pour les selects
      options: [
        { value: 'left', label: 'Gauche' },
        { value: 'center', label: 'Centre' },
        { value: 'right', label: 'Droite' },
      ],
      
      // Validation (optionnel)
      validation: {
        required: false,
        min: null, // Pour number
        max: null, // Pour number
        pattern: null, // Pour text (regex)
      },
    },
    {
      key: 'size',
      type: 'select',
      label: 'Taille',
      description: 'Taille d\'affichage de l\'image',
      default: 'md',
      options: [
        { value: 'sm', label: 'Petit' },
        { value: 'md', label: 'Moyen' },
        { value: 'lg', label: 'Grand' },
        { value: 'xl', label: 'Très grand' },
        { value: 'full', label: 'Pleine largeur' },
      ],
    },
    {
      key: 'zoom',
      type: 'number',
      label: 'Zoom',
      description: 'Niveau de zoom de l\'image en pourcentage',
      default: 100,
      validation: {
        required: false,
        min: 10,
        max: 500,
      },
      // Suffixe pour l'affichage (ex: "%", "px", etc.)
      suffix: '%',
    },
    {
      key: 'lazyLoad',
      type: 'toggle',
      label: 'Chargement différé',
      description: 'Charger l\'image uniquement quand elle est visible',
      default: false,
    },
  ],
  
  // Valeurs par défaut pour les data (contenu)
  defaultData: {
    src: null,
    alt: null,
    caption: null,
  },
};
```

### Types de champs supportés

| Type | Composant Vue | Description | Propriétés spécifiques |
|------|---------------|-------------|----------------------|
| `text` | `InputField` | Champ texte simple | `placeholder`, `maxLength` |
| `number` | `InputField` (type="number") | Champ numérique | `min`, `max`, `step`, `suffix` |
| `select` | `SelectField` | Liste déroulante | `options` (array de `{value, label}`) |
| `toggle` | `ToggleField` | Interrupteur on/off | - |
| `textarea` | `TextareaField` | Zone de texte multiligne | `rows`, `maxLength` |
| `color` | `InputField` (type="color") | Sélecteur de couleur | - |

### Exemple complet : Template Image

```javascript
export default {
  name: 'Image',
  description: 'Affiche une image unique avec légende optionnelle.',
  icon: 'fa-solid fa-image',
  value: 'image',
  supportsAutoSave: true,
  
  defaultSettings: {
    align: 'center',
    size: 'md',
    zoom: 100,
    lazyLoad: false,
  },
  
  parameters: [
    {
      key: 'align',
      type: 'select',
      label: 'Alignement',
      description: 'Position de l\'image dans la section',
      default: 'center',
      options: [
        { value: 'left', label: 'Gauche' },
        { value: 'center', label: 'Centre' },
        { value: 'right', label: 'Droite' },
      ],
    },
    {
      key: 'size',
      type: 'select',
      label: 'Taille',
      description: 'Taille d\'affichage de l\'image',
      default: 'md',
      options: [
        { value: 'sm', label: 'Petit' },
        { value: 'md', label: 'Moyen' },
        { value: 'lg', label: 'Grand' },
        { value: 'xl', label: 'Très grand' },
        { value: 'full', label: 'Pleine largeur' },
      ],
    },
    {
      key: 'zoom',
      type: 'number',
      label: 'Zoom',
      description: 'Niveau de zoom de l\'image en pourcentage (10% à 500%)',
      default: 100,
      validation: {
        min: 10,
        max: 500,
      },
      suffix: '%',
    },
    {
      key: 'lazyLoad',
      type: 'toggle',
      label: 'Chargement différé',
      description: 'Charger l\'image uniquement quand elle est visible à l\'écran',
      default: false,
    },
  ],
  
  defaultData: {
    src: null,
    alt: null,
    caption: null,
  },
};
```

---

## 📝 Structure du modal de paramètres

Le modal `SectionParamsModal.vue` sera organisé en sections :

1. **Paramètres communs** (toujours visibles) :
   - Titre (`title`)
   - Slug (`slug`)
  - Accès lecture (`read_level`)
  - Accès écriture (`write_level`)
   - État (`state`)
   - Classes CSS (`settings.classes`)
   - CSS personnalisé (`settings.customCss`)

2. **Paramètres spécifiques au template** (dynamiques) :
   - Générés automatiquement depuis `config.parameters`
   - Chaque paramètre crée son champ selon son type

---

## 🔄 Migration des configs existants

Les configs actuels utilisent seulement `defaultSettings` et `defaultData`. Pour migrer vers le nouveau système :

1. Ajouter la propriété `parameters` dans chaque `config.js`
2. Définir chaque paramètre avec sa structure complète
3. Le modal utilisera `parameters` pour générer les champs
4. `defaultSettings` reste pour les valeurs par défaut si `parameters` n'est pas défini (rétrocompatibilité)

---

## ✅ Avantages de cette approche

1. **Déclaration simple** : Les paramètres sont définis dans un seul endroit (le config)
2. **Génération automatique** : Le modal crée les champs automatiquement
3. **Type-safe** : Chaque type de champ a ses propriétés spécifiques
4. **Extensible** : Facile d'ajouter de nouveaux types de champs
5. **Documentation intégrée** : Labels et descriptions directement dans le config
6. **Validation** : Les règles de validation sont définies avec le paramètre

