# EntityEditForm - Formulaire d'édition générique

## 📋 Vue d'ensemble

`EntityEditForm` est un composant générique Vue 3 permettant de créer des formulaires d'édition pour n'importe quelle entité. Il supporte deux modes d'affichage (large et compact) et génère dynamiquement les champs selon une configuration.

## 🎯 Fonctionnalités

- ✅ Génération dynamique de formulaires basée sur `fieldsConfig`
- ✅ Deux modes d'affichage : `large` (complet) et `compact` (essentiel)
- ✅ Support de tous les types de champs (text, textarea, select, file, number, checkbox, etc.)
- ✅ Validation intégrée avec notifications
- ✅ Gestion des images avec prévisualisation
- ✅ Toggle entre modes d'affichage
- ✅ Notifications de succès/erreur

## 📦 Utilisation

### Exemple basique

```vue
<EntityEditForm
    :entity="item"
    entity-type="item"
    view-mode="large"
    :fields-config="itemFieldsConfig"
    :is-updating="true"
/>
```

### Exemple avec configuration complète

```vue
<script setup>
import { computed } from 'vue';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
});

const itemFieldsConfig = computed(() => ({
    name: {
        type: 'text',
        label: 'Nom',
        required: true,
        showInCompact: true
    },
    description: {
        type: 'textarea',
        label: 'Description',
        required: false,
        showInCompact: false
    },
    level: {
        type: 'number',
        label: 'Niveau',
        required: false,
        showInCompact: true,
        min: 1,
        max: 200
    },
    rarity: {
        type: 'select',
        label: 'Rareté',
        required: false,
        showInCompact: true,
        options: [
            { value: 'common', label: 'Commun' },
            { value: 'uncommon', label: 'Peu commun' },
            { value: 'rare', label: 'Rare' },
            { value: 'epic', label: 'Épique' },
            { value: 'legendary', label: 'Légendaire' }
        ]
    },
    image: {
        type: 'file',
        label: 'Image',
        required: false,
        showInCompact: false,
        accept: 'image/*'
    }
}));
</script>

<template>
    <EntityEditForm
        :entity="item"
        entity-type="item"
        view-mode="large"
        :fields-config="itemFieldsConfig"
        :is-updating="true"
    />
</template>
```

## 🔧 Props

| Prop | Type | Requis | Description |
|------|------|---------|-------------|
| `entity` | Object | Oui | Données de l'entité à éditer |
| `entityType` | String | Oui | Type d'entité (item, spell, monster, etc.) |
| `viewMode` | String | Non | Mode d'affichage (`'large'` \| `'compact'`), défaut `'large'` |
| `fieldsConfig` | Object | Non | Configuration des champs à afficher (voir ci-dessous) |
| `isUpdating` | Boolean | Non | Mode édition (true) ou création (false), défaut `true` |

## 📝 Configuration des champs (`fieldsConfig`)

Chaque champ est défini par un objet avec les propriétés suivantes :

### Propriétés communes

| Propriété | Type | Description |
|-----------|------|-------------|
| `type` | String | Type de champ (`'text'`, `'textarea'`, `'select'`, `'file'`, `'number'`, `'checkbox'`, etc.) |
| `label` | String | Label du champ |
| `required` | Boolean | Champ requis ou non |
| `showInCompact` | Boolean | Afficher dans le mode compact |
| `placeholder` | String | Placeholder du champ |
| `help` | String | Texte d'aide |

### Propriétés spécifiques par type

#### Type `select`

```javascript
{
    type: 'select',
    options: [
        { value: 'value1', label: 'Label 1' },
        { value: 'value2', label: 'Label 2' }
    ]
}
```

#### Type `number`

```javascript
{
    type: 'number',
    min: 1,
    max: 200,
    step: 1
}
```

#### Type `file`

```javascript
{
    type: 'file',
    accept: 'image/*',
    maxSize: 5120 // en KB
}
```

#### Type `checkbox`

```javascript
{
    type: 'checkbox',
    checkedValue: true,
    uncheckedValue: false
}
```

## 🔄 Événements

| Événement | Payload | Description |
|-----------|---------|-------------|
| `submit` | `Object` | Émis lors de la soumission du formulaire |
| `cancel` | - | Émis lors de l'annulation |
| `update:viewMode` | `String` | Émis lors du changement de mode d'affichage |

## 🎨 Modes d'affichage

### Mode `large` (par défaut)
- Affiche tous les champs configurés
- Formulaire complet avec tous les détails
- Idéal pour l'édition complète

### Mode `compact`
- Affiche uniquement les champs avec `showInCompact: true`
- Formulaire condensé avec champs essentiels
- Idéal pour une édition rapide

## 🛠️ Routes backend

Le composant construit automatiquement le nom de la route selon le pattern :
```
entities.{entityType}.update
```

Exemples :
- `entities.items.update` pour `item`
- `entities.spells.update` pour `spell`
- `entities.monsters.update` pour `monster`

## 📝 Exemples d'utilisation dans les pages d'édition

### Item

```vue
<script setup>
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';

const props = defineProps({
    item: Object,
    availableResources: Array
});

const itemFieldsConfig = {
    name: { type: 'text', label: 'Nom', required: true, showInCompact: true },
    description: { type: 'textarea', label: 'Description', required: false },
    level: { type: 'number', label: 'Niveau', required: false, showInCompact: true },
    rarity: {
        type: 'select',
        label: 'Rareté',
        options: [
            { value: 'common', label: 'Commun' },
            { value: 'uncommon', label: 'Peu commun' },
            { value: 'rare', label: 'Rare' },
            { value: 'epic', label: 'Épique' },
            { value: 'legendary', label: 'Légendaire' }
        ]
    },
    image: { type: 'file', label: 'Image', accept: 'image/*' }
};
</script>

<template>
    <EntityEditForm
        :entity="item"
        entity-type="item"
        :fields-config="itemFieldsConfig"
    />
</template>
```

### Spell

```vue
<script setup>
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';

const props = defineProps({
    spell: Object
});

const spellFieldsConfig = {
    name: { type: 'text', label: 'Nom', required: true, showInCompact: true },
    description: { type: 'textarea', label: 'Description', required: false },
    level: { type: 'number', label: 'Niveau', required: false, showInCompact: true },
    pa: { type: 'number', label: 'Points d\'action', required: false, showInCompact: true },
    po: { type: 'number', label: 'Portée', required: false },
    area: { type: 'number', label: 'Zone', required: false },
    element: {
        type: 'select',
        label: 'Élément',
        options: [
            { value: 0, label: 'Neutre' },
            { value: 1, label: 'Feu' },
            { value: 2, label: 'Eau' },
            { value: 3, label: 'Terre' },
            { value: 4, label: 'Air' }
        ]
    },
    image: { type: 'file', label: 'Image', accept: 'image/*' }
};
</script>

<template>
    <EntityEditForm
        :entity="spell"
        entity-type="spell"
        :fields-config="spellFieldsConfig"
    />
</template>
```

## 🧪 Tests

Les tests sont couverts par les tests des contrôleurs d'entités :
- `ItemControllerTest` : Tests pour la mise à jour d'item
- `SpellControllerTest` : Tests pour la mise à jour de sort
- `PanoplyControllerTest` : Tests pour la mise à jour de panoply
- Etc.

## 📚 Documentation associée

- [EntityRelationsManager](../EntityRelationsManager/README.md) : Composant de gestion des relations
- [Pages d'édition](../README.md) : Documentation des pages d'édition

## 🔗 Fichiers liés

- **Composant** : `resources/js/Pages/Organismes/entity/EntityEditForm.vue`
- **Tests** : `tests/Feature/Entity/*ControllerTest.php`

---

**Date de création** : 2025-11-30
**Dernière mise à jour** : 2025-11-30

