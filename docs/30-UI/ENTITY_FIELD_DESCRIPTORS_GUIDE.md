# Guide complet — Entity Field Descriptors

**Date de création** : 2026-01-06  
**Dernière mise à jour** : 2026-01-06  
**Statut** : ✅ Système en production

---

## 📋 Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture](#architecture)
3. [Composants principaux](#composants-principaux)
4. [Flux de données](#flux-de-données)
5. [Utilisation pratique](#utilisation-pratique)
6. [Exemples concrets](#exemples-concrets)
7. [Bonnes pratiques](#bonnes-pratiques)
8. [Troubleshooting](#troubleshooting)

---

## Vue d'ensemble

Le système **Entity Field Descriptors** est une architecture frontend qui centralise la définition de l'affichage, de l'édition et de la validation des champs d'entités. Il permet de :

- **Générer automatiquement** les cellules de tableaux
- **Créer des formulaires** d'édition dynamiques
- **Gérer l'édition en masse** (bulk edit) avec agrégation intelligente
- **Unifier l'UX** entre tableaux, formulaires et vues détaillées

### Principe fondamental

**Le backend reste la vérité pour la sécurité et la validation**, mais le frontend gère toute l'UX (affichage, formulaires, bulk edit) via les descriptors.

---

## Architecture

### Structure des fichiers

Pour chaque entité (ex: `spell`, `item`, `monster`), on trouve :

```
resources/js/Entities/{entity}/
├── {entity}-descriptors.js    # Définition des champs (source de vérité UX)
└── {entity}-adapter.js        # Transformation entities → TableResponse
```

### Registry central

Le fichier `entity-registry.js` centralise l'accès aux descriptors et adapters :

```javascript
import { getEntityConfig } from "@/Entities/entity-registry";

const config = getEntityConfig("spells");
const descriptors = config.getDescriptors(ctx);
const adapter = config.responseAdapter;
```

---

## Composants principaux

### 1. Descriptors (`*-descriptors.js`)

Les descriptors définissent pour chaque champ :

- **Affichage** : label, icône, format, tailles (small/normal/large)
- **Édition** : type de champ, validation, options, groupes
- **Bulk edit** : activation, nullable, fonction de transformation
- **Permissions UX** : `visibleIf`, `editableIf` (le backend reste la vérité)

**Exemple** :

```javascript
level: {
  key: "level",
  label: "Niveau",
  icon: "fa-solid fa-level-up-alt",
  format: "number",
  display: {
    views: DEFAULT_SPELL_FIELD_VIEWS,
    sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
  },
  edit: {
    form: {
      type: "text",
      group: "Métier",
      placeholder: "Ex: 50",
      required: false,
      showInCompact: true,
      bulk: { 
        enabled: true, 
        nullable: true, 
        build: (v) => (v === "" ? null : String(v)) 
      },
    },
  },
}
```

### 2. Adapters (`*-adapter.js`)

Les adapters transforment les entités brutes en `TableResponse` pour TanStack Table :

- **`build{Entity}Cell(colId, entity, ctx, opts)`** : Génère une cellule pour un champ
- **`adapt{Entity}EntitiesTableResponse({ meta, entities })`** : Transforme la réponse backend

**Exemple** :

```javascript
export function buildSpellCell(colId, entity, ctx = {}, opts = {}) {
  // Logique de génération de cellule selon le type de champ
  if (colId === "name") {
    return {
      type: "route",
      value: entity?.name || "-",
      params: { href: spellShowHref(entity?.id), ... }
    };
  }
  // ...
}

export function adaptSpellEntitiesTableResponse({ meta, entities }) {
  const rows = entities.map(entity => ({
    id: entity?.id,
    cells: { /* générées via buildSpellCell */ },
    rowParams: { entity }
  }));
  return { meta, rows };
}
```

### 3. Utilitaires

#### `descriptor-form.js`

Génère les configurations pour les formulaires :

- `createFieldsConfigFromDescriptors()` → `fieldsConfig` pour `EntityEditForm`
- `createBulkFieldMetaFromDescriptors()` → `fieldMeta` pour `useBulkEditPanel`
- `createDefaultEntityFromDescriptors()` → `defaultEntity` pour création

#### `descriptor-cache.js`

Système de cache pour les descriptors (TTL 5 minutes) :

```javascript
import { getCachedDescriptors } from "@/Utils/entity/descriptor-cache";

const descriptors = getCachedDescriptors(
  entityType, 
  getDescriptorsFn, 
  ctx
);
```

#### `adapter-helpers.js`

Fonctions utilitaires communes pour les adapters :

- `toNumber()`, `formatDateFr()`
- `buildTextCell()`, `buildBadgeCell()`, `buildRouteCell()`
- `VISIBILITY_LABELS`, `RARITY_LABELS`, etc.

### 4. Composables

#### `useBulkEditPanel`

Gère l'édition en masse :

- Agrégation des valeurs (communes vs différentes)
- Tracking des champs modifiés (`dirty`)
- Construction du payload pour l'API
- Support multi-sélection et filtres

**Exemple** :

```javascript
const {
  ids,
  aggregate,
  form,
  dirty,
  canApply,
  onChange,
  buildPayload,
} = useBulkEditPanel({
  selectedEntities,
  isAdmin,
  fieldMeta,
  mode: "client",
});
```

### 5. Composants Vue

#### `EntityQuickEditPanel`

Panneau d'édition rapide (sélection multiple) :

- Affiche les champs bulk-enabled
- Gère les groupes de champs
- Indicateurs visuels pour les champs modifiés
- Bouton "Tout réinitialiser"

#### `EntityEditForm`

Formulaire d'édition générique :

- Mode single-edit et multi-edit
- Support des champs "valeurs différentes"
- Raccourcis clavier (Ctrl+S, Esc, Ctrl+Z)
- Génération automatique depuis les descriptors

---

## Flux de données

### 1. Affichage d'un tableau

```
Backend (TableController)
  ↓ format=entities
{ meta, entities[] }
  ↓
Adapter (adapt{Entity}EntitiesTableResponse)
  ↓
{ meta, rows[] } avec cells générées
  ↓
TanStackTable
  ↓
CellRenderer (affiche chaque cellule)
```

### 2. Édition rapide (bulk)

```
Sélection multiple
  ↓
EntityQuickEditPanel
  ↓
useBulkEditPanel (agrégation + dirty tracking)
  ↓
buildPayload() → { ids, champs_modifiés }
  ↓
API PATCH /api/entities/{type}/bulk
  ↓
BulkController (validation + update)
```

### 3. Édition unitaire

```
Clic sur une ligne
  ↓
EntityEditForm
  ↓
createFieldsConfigFromDescriptors()
  ↓
Formulaire généré automatiquement
  ↓
Soumission → API standard (store/update)
```

---

## Utilisation pratique

### Dans une page Index.vue

```vue
<script setup>
import { getEntityConfig } from "@/Entities/entity-registry";
import { adaptSpellEntitiesTableResponse } from "@/Entities/spell/spell-adapter";
import EntityQuickEditPanel from "@/Pages/Organismes/entity/EntityQuickEditPanel.vue";

const cfg = computed(() => getEntityConfig("spells"));
const descriptors = computed(() => 
  cfg.value?.getDescriptors({ capabilities: { updateAny: isAdmin } }) || {}
);

const handleBulkUpdate = async (payload) => {
  await useBulkRequest("spells", payload);
};
</script>

<template>
  <EntityTanStackTable
    :response-adapter="adaptSpellEntitiesTableResponse"
    :server-url="`/api/tables/spells?format=entities`"
  />
  
  <EntityQuickEditPanel
    entity-type="spells"
    :selected-entities="selectedEntities"
    :is-admin="isAdmin"
    @applied="handleBulkUpdate"
  />
</template>
```

### Ajouter un nouveau champ

1. **Modifier le descriptor** :

```javascript
// spell-descriptors.js
nouveau_champ: {
  key: "nouveau_champ",
  label: "Nouveau Champ",
  format: "text",
  display: { /* ... */ },
  edit: {
    form: {
      type: "text",
      bulk: { enabled: true, nullable: true, build: (v) => v },
    },
  },
}
```

2. **Ajouter dans l'adapter** (si besoin de logique spéciale) :

```javascript
// spell-adapter.js
if (colId === "nouveau_champ") {
  return buildTextCell(entity?.nouveau_champ);
}
```

3. **Ajouter dans le BulkController** (si bulk-enabled) :

```php
// SpellBulkController.php
'nouveau_champ' => ['sometimes', 'nullable', 'string', 'max:255'],
```

---

## Exemples concrets

### Exemple 1 : Champ texte simple

```javascript
description: {
  key: "description",
  label: "Description",
  icon: "fa-solid fa-align-left",
  format: "text",
  display: {
    views: DEFAULT_SPELL_FIELD_VIEWS,
    sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
  },
  edit: {
    form: {
      type: "textarea",
      group: "Contenu",
      required: false,
      showInCompact: false,
      bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
    },
  },
}
```

### Exemple 2 : Champ select avec options

```javascript
is_visible: {
  key: "is_visible",
  label: "Visibilité",
  icon: "fa-solid fa-eye",
  format: "enum",
  display: {
    views: DEFAULT_SPELL_FIELD_VIEWS,
    sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } },
  },
  edit: {
    form: {
      type: "select",
      group: "Statut",
      required: false,
      showInCompact: true,
      options: [
        { value: "guest", label: "Invité" },
        { value: "user", label: "Utilisateur" },
        { value: "admin", label: "Administrateur" },
      ],
      defaultValue: "guest",
      bulk: { enabled: true, nullable: false, build: (v) => v },
    },
  },
}
```

### Exemple 3 : Champ booléen avec tri-state

```javascript
usable: {
  key: "usable",
  label: "Utilisable",
  icon: "fa-solid fa-check-circle",
  format: "bool",
  display: {
    views: DEFAULT_SPELL_FIELD_VIEWS,
    sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } },
  },
  edit: {
    form: {
      type: "checkbox",
      group: "Statut",
      required: false,
      showInCompact: true,
      defaultValue: false,
      bulk: { enabled: true, nullable: false, build: (v) => v === "1" || v === true },
    },
  },
}
```

### Exemple 4 : Champ avec permission conditionnelle

```javascript
id: {
  key: "id",
  label: "ID",
  icon: "fa-solid fa-hashtag",
  format: "number",
  visibleIf: () => canCreateAny, // Afficher seulement si peut créer
  display: {
    views: DEFAULT_SPELL_FIELD_VIEWS,
    sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
  },
}
```

---

## Bonnes pratiques

### 1. Structure des descriptors

- **Toujours définir** `key`, `label`, `format`, `display`
- **Utiliser les constantes** `DEFAULT_{ENTITY}_FIELD_VIEWS` pour la cohérence
- **Grouper les champs** avec `edit.form.group` pour le quick edit

### 2. Adapters

- **Réutiliser les helpers** de `adapter-helpers.js` quand possible
- **Gérer les valeurs nulles** : toujours retourner `"-"` pour les valeurs null
- **Optimiser les boucles** : générer uniquement les cellules nécessaires

### 3. Bulk edit

- **Toujours définir `build`** pour les champs bulk-enabled
- **Gérer les valeurs vides** : `build: (v) => (v === "" ? null : String(v))`
- **Respecter `nullable`** : si `nullable: false`, ne pas permettre de vider le champ

### 4. Performance

- **Utiliser le cache** : `getCachedDescriptors()` pour éviter les recalculs
- **Optimiser les re-renders** : `v-memo` dans les composants de table
- **Limiter les champs** : ne générer que les cellules visibles

---

## Troubleshooting

### Le champ n'apparaît pas dans le quick edit

**Solutions** :
1. Vérifier que le champ est dans `{ENTITY}_VIEW_FIELDS.quickEdit`
2. Vérifier que `edit.form.bulk.enabled === true`
3. Vérifier que le champ est dans le BulkController backend

### Le champ n'est pas sauvegardé en bulk

**Solutions** :
1. Vérifier que le champ est dans la validation du BulkController
2. Vérifier que le champ est dans le `foreach` des champs à patcher
3. Vérifier les permissions (`updateAny` dans la Policy)

### La cellule ne s'affiche pas correctement

**Solutions** :
1. Vérifier que le cas est géré dans `build{Entity}Cell`
2. Vérifier que le format correspond (text, badge, route, etc.)
3. Vérifier que les valeurs nulles sont gérées (`entity?.champ || "-"`)

### Les options du select ne s'affichent pas

**Solutions** :
1. Vérifier que les options sont fournies par le backend (`meta.filterOptions.{champ}`)
2. Vérifier que le contexte est passé correctement : `get{Entity}FieldDescriptors({ meta })`
3. Vérifier que les options sont au bon format : `[{ value, label }, ...]`

---

## Ressources

- **Guide de maintenance** : [`ENTITY_DESCRIPTORS_MAINTENANCE_GUIDE.md`](./ENTITY_DESCRIPTORS_MAINTENANCE_GUIDE.md)
- **Tests** : [`TESTS_ENTITY_DESCRIPTORS_IMPLEMENTATION.md`](../100-%20Done/TESTS_ENTITY_DESCRIPTORS_IMPLEMENTATION.md)
- **Plan de migration** : [`PLAN_MIGRATION_DESCRIPTORS.md`](./PLAN_MIGRATION_DESCRIPTORS.md)

---

## État d'implémentation

**Date de finalisation** : 2026-01-06

- ✅ **16/16 entités** migrées
- ✅ **15 BulkControllers** créés et testés
- ✅ **16 TableControllers** supportent `?format=entities`
- ✅ **16 adapters** frontend créés
- ✅ **165 tests** passent (966 assertions)
- ✅ **Cache des descriptors** implémenté
- ✅ **Optimisations UX** (indicateurs, raccourcis, animations)
- ✅ **Optimisations performance** (`v-memo`, cache)

