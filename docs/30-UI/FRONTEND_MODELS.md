# Modèles Frontend JS — KrosmozJDR

## 📋 Vue d'ensemble

Les modèles frontend JS sont des classes qui normalisent et encapsulent les données des entités côté client. Ils fournissent une interface cohérente pour accéder aux propriétés, relations et méthodes utilitaires, indépendamment de la structure des données brutes (Proxies Vue, objets Inertia, etc.).

---

## 🎯 **Avantages**

1. **Normalisation** : Extraction automatique des données depuis différentes structures (Proxy, `.data`, objet brut)
2. **Valeurs par défaut** : Gestion automatique des valeurs nulles/undefined
3. **Relations** : Accès simplifié aux relations via des getters
4. **Méthodes utilitaires** : `toFormData()`, `fromArray()`, etc.
5. **Type safety** : Interface claire et prévisible

---

## 📁 **Structure**

### **BaseModel** (`resources/js/Models/BaseModel.js`)

Classe abstraite de base pour tous les modèles :

```javascript
import { BaseModel } from '@/Models/BaseModel';

export class Item extends BaseModel {
    // Propriétés spécifiques
    get name() {
        return this._data.name || '';
    }
    
    // Relations
    get resources() {
        return this._data.resources || [];
    }
    
    // Méthodes utilitaires
    toFormData() {
        return {
            name: this.name,
            // ...
        };
    }
}
```

### **Modèles disponibles**

- `Item` - Objets/équipements
- `Creature` - Créatures (base pour NPCs et Monstres)
- `Npc` - Personnages non-joueurs
- `Monster` - Monstres
- `Campaign` - Campagnes
- `Scenario` - Scénarios
- `Spell` - Sorts
- `Panoply` - Panoplies
- `Resource` - Ressources
- `Shop` - hotels de vente
- Et autres...

---

## 🔧 **Utilisation dans les Vues**

### **1. Vues Index (Liste)**

```vue
<script setup>
import { Item } from "@/Models/Entity/Item";

const props = defineProps({
    items: {
        type: Object,
        required: true
    }
});

// Transformation des entités en instances de modèles
const items = computed(() => {
    return Item.fromArray(props.items.data || []);
});
</script>

<template>
    <EntityTanStackTable
        entity-type="items"
        :config="tableConfig"
        :server-url="serverUrl"
    />
</template>
```

**Points importants :**
- Utiliser `Model.fromArray()` pour transformer un tableau de données (quand on consomme un dataset Inertia)
- `EntityTanStackTable` est le wrapper Table v2 recommandé (permissions + fetch optionnel)
- La pagination “Laravel paginator” n'est plus le contrat principal des tables v2 (dataset chargé côté table API)

## 🧩 Schémas de champs (génération de formulaires)

En complément des modèles, le projet supporte un format **meta-driven** de champs (schema) pour éviter la duplication
entre :
- formulaires create/edit (`EntityEditForm` via `fieldsConfig`)
- bulk panels (`useBulkEditPanel` via `fieldMeta`)

Le schéma est transformé par des helpers (ex: `createFieldsConfigFromSchema`, `createBulkFieldMetaFromSchema`).

### **2. Vues Edit (Édition)**

```vue
<script setup>
import { Item } from '@/Models/Entity/Item';

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
});

// Créer une instance de modèle
const item = computed(() => {
    return new Item(props.item);
});
</script>

<template>
    <EntityEditForm
        :entity="item"
        entity-type="item"
    />
    
    <EntityRelationsManager
        :relations="item.resources || []"
        :entity-id="item.id"
    />
</template>
```

**Points importants :**
- Utiliser `new Model(data)` pour créer une instance
- Accéder aux propriétés via les getters (pas besoin de `?.`)
- Utiliser les relations directement via les getters

### **3. Handlers (Suppression, Édition)**

```javascript
const handleDelete = (entity) => {
    // entity peut être une instance de modèle ou un objet brut
    const itemModel = entity instanceof Item ? entity : new Item(entity);
    if (confirm(`Êtes-vous sûr de vouloir supprimer "${itemModel.name}" ?`)) {
        router.delete(route(`entities.items.delete`, { item: itemModel.id }));
    }
};
```

---

## 🎨 **Composants Réutilisables**

### **EntityEditForm**

Le composant `EntityEditForm` détecte automatiquement si l'entité est un modèle et utilise `toFormData()` si disponible :

```javascript
// Dans EntityEditForm.vue
const initializeForm = () => {
    // Si l'entité est une instance de modèle avec toFormData(), l'utiliser
    if (props.entity && typeof props.entity.toFormData === 'function') {
        const modelFormData = props.entity.toFormData();
        // ...
    }
    // Sinon, utiliser l'accès direct (compatibilité)
};
```

### **EntityTableRow**

Le composant `EntityTableRow` gère automatiquement les instances de modèles :

```javascript
// Dans EntityTableRow.vue
const getCellValue = (column) => {
    let value;
    
    // Si l'entité est une instance de modèle, utiliser les getters
    if (props.entity && typeof props.entity._data !== 'undefined') {
        const getterName = column.key;
        if (typeof props.entity[getterName] !== 'undefined') {
            value = props.entity[getterName];
        } else {
            value = props.entity._data?.[column.key];
        }
    } else {
        // Objet brut, accès direct
        value = props.entity[column.key];
    }
    // ...
};
```

### **EntityModal**

Le composant `EntityModal` utilise une fonction helper pour récupérer le nom :

```javascript
const getEntityName = () => {
    if (props.entity && typeof props.entity._data !== 'undefined') {
        return props.entity.name || props.entity.title || 'Entité';
    }
    return props.entity?.name || props.entity?.title || 'Entité';
};
```

---

## 📝 **Méthodes Utilitaires**

### **fromArray()** - Créer un tableau d'instances

```javascript
const items = Item.fromArray(props.items.data || []);
```

### **from()** - Créer une instance unique

```javascript
const item = Item.from(props.item);
// Équivalent à : new Item(props.item)
```

### **toFormData()** - Données pour formulaire

```javascript
const formData = item.toFormData();
// Retourne un objet avec les propriétés formatées pour le formulaire
```

### **toRaw()** - Données brutes

```javascript
const rawData = item.toRaw();
// Retourne les données brutes (_data)
```

---

## 🔍 **Propriétés Communes (BaseModel)**

Tous les modèles héritent de `BaseModel` et ont accès à :

- `id` - Identifiant de l'entité
- `createdById` - ID du créateur
- `createdAt` - Date de création
- `updatedAt` - Date de mise à jour
- `deletedAt` - Date de suppression (soft delete)
- `isVisible` - Visibilité
- `can` - Permissions (update, delete, view, etc.)
- `canUpdate`, `canDelete`, `canView` - Getters de permissions

---

## ⚠️ **Bonnes Pratiques**

### ✅ **À faire**

- Utiliser les modèles dans toutes les vues Index et Edit
- Utiliser `fromArray()` pour les listes
- Utiliser `new Model()` pour les instances uniques
- Accéder aux propriétés via les getters (pas besoin de `?.`)
- Utiliser `toFormData()` pour initialiser les formulaires

### ❌ **À éviter**

- Accéder directement à `_data` (sauf cas spéciaux)
- Mélanger objets bruts et modèles sans vérification
- Oublier de transformer les données dans les vues Index

---

## 🔄 **Compatibilité**

Les composants sont **rétrocompatibles** avec les objets bruts :
- Si une instance de modèle est détectée, les getters sont utilisés
- Sinon, l'accès direct aux propriétés fonctionne normalement

Cela permet une migration progressive sans casser le code existant.

---

## 📚 **Exemples Complets**

### **Vue Index complète**

```vue
<script setup>
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, onBeforeUnmount } from "vue";
import { Item } from "@/Models/Entity/Item";
import EntityTable from '@/Pages/Molecules/data-display/EntityTable.vue';

const props = defineProps({
    items: {
        type: Object,
        required: true
    }
});

// Transformation des entités en instances de modèles
const items = computed(() => {
    return Item.fromArray(props.items.data || []);
});

const handleDelete = (entity) => {
    const itemModel = entity instanceof Item ? entity : new Item(entity);
    if (confirm(`Supprimer "${itemModel.name}" ?`)) {
        router.delete(route(`entities.items.delete`, { item: itemModel.id }));
    }
};
</script>

<template>
    <EntityTable
        :entities="items"
        :pagination="props.items"
        entity-type="items"
        @delete="handleDelete"
    />
</template>
```

### **Vue Edit complète**

```vue
<script setup>
import { computed } from 'vue';
import { Item } from '@/Models/Entity/Item';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
});

// Créer une instance de modèle
const item = computed(() => {
    return new Item(props.item);
});
</script>

<template>
    <EntityEditForm
        :entity="item"
        entity-type="item"
        :is-updating="true"
    />
</template>
```

---

## 🔗 **Voir aussi**

- [BaseModel.js](../../resources/js/Models/BaseModel.js) - Classe de base
- [EntityEditForm](../50-Fonctionnalités/EntityEditForm/README.md) - Composant de formulaire
- [EntityTableRow](../../resources/js/Pages/Molecules/data-display/EntityTableRow.vue) - Ligne de tableau

