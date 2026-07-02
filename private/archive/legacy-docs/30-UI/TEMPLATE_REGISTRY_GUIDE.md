# Guide du Template Registry

## Vue d'ensemble

Le **Template Registry** est un système centralisé de gestion des templates de sections pour Krosmoz-JDR. Il fournit :
- ✅ **Auto-discovery** : détection automatique des nouveaux templates
- ✅ **Validation** : vérification de la structure des configurations au démarrage
- ✅ **Cache** : optimisation des performances (pas de rechargement inutile)
- ✅ **Gestion d'erreurs** : logs détaillés et fallbacks robustes

## Architecture

```
resources/js/Pages/Organismes/section/
├── templates/
│   ├── index.js                    # Auto-discovery (import.meta.glob)
│   ├── text/
│   │   ├── config.js               # Configuration du template
│   │   ├── SectionTextRead.vue     # Composant lecture
│   │   └── SectionTextEdit.vue     # Composant édition
│   ├── image/
│   │   ├── config.js
│   │   ├── SectionImageRead.vue
│   │   └── SectionImageEdit.vue
│   └── ...
└── composables/
    ├── useTemplateRegistry.js      # ⭐ Registry principal (cache, validation)
    └── useSectionTemplates.js      # (Ancienne API, toujours compatible)
```

## Utilisation

### 1. Charger un composant

```javascript
import { useTemplateRegistry } from '@/Pages/Organismes/section/composables/useTemplateRegistry';

const registry = useTemplateRegistry();

// Charger un composant (avec cache automatique)
const component = await registry.loadComponent('text', 'read');
```

### 2. Récupérer les options pour un select

```javascript
const registry = useTemplateRegistry();

// Templates valides uniquement
const options = registry.getOptions();
// [{ value: 'text', label: 'Texte', icon: 'fa-align-left', ... }, ...]
```

### 3. Vérifier la validité

```javascript
const registry = useTemplateRegistry();

if (registry.isValidTemplate('text')) {
  const config = registry.getConfig('text');
  console.log(config.name); // "Texte"
}
```

### 4. Récupérer les valeurs par défaut

```javascript
const registry = useTemplateRegistry();

const defaults = registry.getDefaults('text');
// { settings: {}, data: { content: null } }
```

### 5. Optimiser le chargement (préchargement)

```javascript
const registry = useTemplateRegistry();

// Précharger un template spécifique
await registry.preload('text', 'both'); // 'read', 'edit', ou 'both'

// Précharger les templates courants (au démarrage de l'app)
import { preloadCommonTemplates } from '@/Pages/Organismes/section/composables/useTemplateRegistry';
await preloadCommonTemplates(); // Précharge text, image, divider
```

## Créer un nouveau template

### Étape 1 : Structure des fichiers

```
resources/js/Pages/Organismes/section/templates/mon-template/
├── config.js
├── SectionMonTemplateRead.vue
└── SectionMonTemplateEdit.vue
```

### Étape 2 : Créer la configuration (`config.js`)

```javascript
/**
 * Configuration du template Mon Template
 */
export default {
  // Valeur unique (snake_case)
  value: 'mon_template',
  
  // Nom affiché (lisible)
  name: 'Mon Template',
  
  // Description courte
  description: 'Description de mon template',
  
  // Icône FontAwesome
  icon: 'fa-solid fa-star',
  
  // Support de l'auto-save (optionnel)
  supportsAutoSave: true,
  
  // Settings par défaut
  defaultSettings: {
    showTitle: true,
    theme: 'default'
  },
  
  // Data par défaut
  defaultData: {
    content: null,
    metadata: {}
  },
  
  // Paramètres configurables (optionnel)
  parameters: [
    {
      key: 'showTitle',
      label: 'Afficher le titre',
      type: 'boolean',
      default: true,
      helper: 'Affiche ou masque le titre de la section'
    },
    {
      key: 'theme',
      label: 'Thème',
      type: 'select',
      options: [
        { value: 'default', label: 'Par défaut' },
        { value: 'dark', label: 'Sombre' }
      ],
      default: 'default'
    }
  ]
};
```

### Étape 3 : Créer le composant Read

```vue
<!-- SectionMonTemplateRead.vue -->
<script setup>
const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const content = computed(() => props.data?.content || '');
const showTitle = computed(() => props.settings?.showTitle ?? true);
</script>

<template>
  <div class="section-mon-template-read">
    <h3 v-if="showTitle" class="font-bold">{{ section.title }}</h3>
    <div v-html="content"></div>
  </div>
</template>
```

### Étape 4 : Créer le composant Edit

```vue
<!-- SectionMonTemplateEdit.vue -->
<script setup>
import { ref, watch } from 'vue';
import { useSectionSave } from '../../composables/useSectionSave';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const emit = defineEmits(['data-updated']);

const { saveSection } = useSectionSave();

const localData = ref({
  content: props.data?.content || ''
});

// Synchroniser avec les props
watch(() => props.data, (newData) => {
  if (newData) {
    localData.value = {
      content: newData.content || ''
    };
  }
}, { deep: true });

// Auto-save avec debounce
watch(localData, (newVal) => {
  const newData = {
    ...props.data,
    ...newVal
  };
  
  saveSection(props.section.id, { data: newData });
  emit('data-updated', newData);
}, { deep: true });
</script>

<template>
  <div class="section-mon-template-edit">
    <textarea
      v-model="localData.content"
      class="textarea"
      placeholder="Contenu..."
    ></textarea>
  </div>
</template>
```

### Étape 5 : C'est tout ! ✅

Le registry détectera automatiquement votre nouveau template au prochain rechargement.

## Validation automatique

Au démarrage, le registry valide chaque template :

```
🎨 Template Registry - Initialisation
✅ Template "Texte" (text)
✅ Template "Image" (image)
✅ Template "Séparateur" (divider)
❌ Template "Invalide" (invalid):
   - Champ obligatoire manquant: icon
   - Le champ "readComponent" doit être une fonction

📊 Résumé: 3 valides, 1 invalide
```

## Statistiques du registry

```javascript
const registry = useTemplateRegistry();

console.log(registry.stats.value);
// {
//   total: 4,
//   valid: 3,
//   invalid: 1,
//   cached: 6  // Nombre de composants en cache
// }
```

## Gestion du cache

### Vider le cache (dev uniquement)

```javascript
const registry = useTemplateRegistry();
registry.clearCache();
```

### Réinitialiser complètement

```javascript
const registry = useTemplateRegistry();
registry.reset(); // Vide le cache + re-valide tous les templates
```

## Bonnes pratiques

### ✅ À FAIRE

- Nommer les templates en snake_case (`mon_template`)
- Nommer les composants en PascalCase (`SectionMonTemplateRead.vue`)
- Fournir une description claire et concise
- Utiliser des icônes FontAwesome existantes
- Tester la validation en local
- Documenter les paramètres spécifiques

### ❌ À ÉVITER

- Charger des templates directement (toujours passer par le registry)
- Modifier `templates/index.js` manuellement (c'est automatique)
- Créer des templates sans config.js
- Utiliser des noms de templates avec espaces ou caractères spéciaux

## Migration depuis l'ancien système

### Avant (useSectionTemplates)

```javascript
import { useSectionTemplates } from './composables/useSectionTemplates';

const { getTemplateComponent } = useSectionTemplates();
const component = await getTemplateComponent('text', 'read');
```

### Après (useTemplateRegistry)

```javascript
import { useTemplateRegistry } from './composables/useTemplateRegistry';

const registry = useTemplateRegistry();
const component = await registry.loadComponent('text', 'read');
```

**Note** : L'ancien système reste fonctionnel mais le registry est recommandé pour ses optimisations.

## Débogage

### Template non trouvé

```javascript
const registry = useTemplateRegistry();

if (!registry.isValidTemplate('mon_template')) {
  console.log('Template invalide ou non trouvé');
  console.log('Erreur:', registry.lastError.value);
}
```

### Lister tous les templates

```javascript
const registry = useTemplateRegistry();

console.log('Templates disponibles:', registry.templates.value);
```

## Support

Pour toute question ou problème :
- Vérifier la console du navigateur (logs de validation au démarrage)
- Consulter `registry.stats.value` pour les statistiques
- Vérifier que la structure des fichiers est correcte
- S'assurer que `config.js` exporte un objet par défaut

---

**Mis à jour** : Décembre 2024  
**Auteur** : Équipe Krosmoz-JDR

