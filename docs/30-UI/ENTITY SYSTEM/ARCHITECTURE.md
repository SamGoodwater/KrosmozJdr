# Architecture du système d'entités — Détails complets

**Version** : 2.0  
**Date** : 2026-01-XX

---

## 🎯 Vue d'ensemble

Le système d'entités frontend suit une **architecture en 7 couches** qui transforme les données brutes du backend en interfaces utilisateur complètes.

### Principe directeur

> **Chaque couche connaît uniquement la couche juste en dessous, jamais plus.**

Cette séparation stricte garantit :
- **Maintenabilité** : Chaque couche a une responsabilité claire
- **Réutilisabilité** : Les composants peuvent être réutilisés entre entités
- **Testabilité** : Chaque couche peut être testée indépendamment

---

## 📦 Couche 1 : Adapter & Mapper

### Rôle
Transforme la réponse backend `{ meta, entities }` en structure compatible avec TanStackTable `{ meta, rows }`.

### Fichiers clés
- `Utils/Entity/createEntityAdapter.js` — Factory générique
- `Utils/Entity/MapperRegistry.js` — Registre centralisé des mappers
- `Mappers/Entity/ResourceMapper.js` — Mapper spécifique (exemple)

### Processus

**1. Adapter (`createEntityAdapter`) :**
```javascript
// Si un Mapper existe → utilise Mapper.fromApiArray(entities)
// Sinon → instancie directement : new ModelClass(entityData)
const adapter = createEntityAdapter(Resource, ResourceMapper);
const result = adapter({ meta: {...}, entities: [...] });
// → { meta, rows: [{ id, cells: {}, rowParams: { entity } }] }
```

**2. Mapper (optionnel) :**
```javascript
// Transforme les données brutes en structure frontend
ResourceMapper.fromApiArray(entities) → [Resource instances]
ResourceMapper.fromBulkForm(bulkFormData) → { ... } // Pour quickedit
```

### Quand utiliser un Mapper ?

- **Nécessaire** : Transformations complexes (ex: agrégation de données, calculs)
- **Optionnel** : Si les données backend correspondent déjà au modèle frontend

---

## 🧠 Couche 2 : Models

### Rôle
Encapsule la logique métier et le formatage des données.

### Fichiers clés
- `Models/BaseModel.js` — Classe de base
- `Models/Entity/Resource.js` — Modèle spécifique (exemple)
- `Models/Entity/*.js` — Autres modèles

### Fonctionnalités principales

**1. `toCell(fieldKey, options)` :**
```javascript
const cell = entity.toCell('rarity', { size: 'md' });
// → { type: 'badge', value: 'Rare', params: { color: 'success', icon: 'fa-circle' } }
```

**Processus interne :**
1. Vérifie le cache (`_cellCache`)
2. Appelle `getFormatter(fieldKey)` → trouve le formatter approprié
3. Appelle `Formatter.toCell(value, options)`
4. Met en cache le résultat
5. Retourne l'objet `Cell`

**2. Propriétés communes :**
- `id`, `created_at`, `updated_at`
- `can.*` (permissions depuis le backend)

**3. Extraction normalisée :**
- Gère les Proxies Vue/Inertia
- Extrait les données depuis différentes structures (`data`, racine, etc.)

---

## 🎨 Couche 3 : Formatters

### Rôle
Centralise le formatage des valeurs en labels, badges et cellules.

### Fichiers clés
- `Utils/Formatters/BaseFormatter.js` — Classe abstraite
- `Utils/Formatters/FormatterRegistry.js` — Registre centralisé
- `Utils/Formatters/*.js` — Formatters spécifiques
- `Utils/Entity/SharedConstants.js` — Constantes partagées

### Structure d'un formatter

```javascript
class RarityFormatter extends BaseFormatter {
  static name = 'RarityFormatter';
  static fieldKeys = ['rarity'];
  
  static format(value) {
    // → "Rare"
  }
  
  static toCell(value, options) {
    // → { type: 'badge', value: 'Rare', params: { color: 'success', icon: 'fa-circle' } }
  }
}
```

### Enregistrement

Tous les formatters sont enregistrés dans `FormatterRegistry` au démarrage (`app.js`) :
```javascript
import "@/Utils/Formatters"; // Enregistre tous les formatters
```

### Constantes partagées

`SharedConstants.js` centralise :
- `FIELD_LABELS` : Labels traduits (level → "Niveau")
- `FIELD_ICONS` : Icônes FontAwesome
- `LEVEL_COLORS` : Dégradé de couleurs pour niveaux 1-30
- `RARITY_GRADIENT` : Dégradé de couleurs pour rareté 0-5
- `USER_ROLES` : Rôles avec traductions et couleurs

---

## 📜 Couche 4 : Descriptors

### Rôle
**Source de vérité déclarative** pour la configuration UX de chaque entité.

### Fichiers clés
- `Entities/{entity}/{entity}-descriptors.js` — Descriptors spécifiques
- `Entities/entity-registry.js` — Registre centralisé

### Structure d'un descriptor

```javascript
{
  fieldKey: {
    // Métadonnées générales
    general: {
      label: "Niveau",
      icon: "fa-solid fa-level-up-alt",
      tooltip: "Niveau de la ressource"
    },
    
    // Permissions
    permissions: {
      visibleIf: (ctx) => boolean,  // Visibilité en mode read
      editableIf: (ctx) => boolean  // Éditabilité
    },
    
    // Configuration tableau
    table: {
      defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
      visibleIf: (ctx) => boolean,  // Visibilité de la colonne
      cell: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          // ...
        }
      }
    },
    
    // Configuration affichage (vues Large, Compact, etc.)
    display: {
      tooltip: "...",
      style: { compact: "...", large: "..." },
      color: { compact: "...", large: "..." },
      format: "rarity" // Clé du formatter
    },
    
    // Configuration édition
    edition: {
      form: {
        type: "select",
        required: true,
        validation: { min: 0, max: 5 },
        options: [...] // ou fonction(ctx)
      },
      bulk: {
        enabled: true,
        nullable: true
      }
    }
  },
  
  // Configuration globale tableau
  _tableConfig: {
    id: "resources.index",
    entityType: "resource",
    quickEdit: { enabled: true, permission: "updateAny" },
    actions: { enabled: true, permission: "view" },
    features: { search: {...}, filters: {...}, pagination: {...} }
  },
  
  // Configuration globale quickedit
  _quickeditConfig: {
    fields: ["resource_type_id", "rarity", "level", ...]
  }
}
```

### Utilisation

- **TableConfig** : Génère les colonnes depuis `table.*`
- **BulkConfig** : Génère les champs quickedit depuis `edition.bulk.*`
- **FormConfig** : Génère les formulaires depuis `edition.form.*`
- **Vues** : Utilisent `general.*`, `display.*`, `permissions.*`

### Règles strictes

✅ **Autorisé** :
- Déclarations pures (pas de logique)
- Constantes et options
- Fonctions conditionnelles (`visibleIf`, `editableIf`)

❌ **Interdit** :
- Logique métier
- Calculs
- Appels à des modèles
- État ou effets de bord

---

## ⚙️ Couche 5 : Configs & Helpers

### Rôle
Génère les configurations utilisables par les composants Vue.

### Fichiers clés
- `Utils/Entity/Configs/TableConfig.js` — Configuration tableaux
- `Utils/Entity/Configs/TableColumnConfig.js` — Configuration colonne
- `Utils/Entity/Configs/BulkConfig.js` — Configuration bulk
- `Utils/Entity/Configs/FormConfig.js` — Configuration formulaires
- `Utils/entity/descriptor-form.js` — Helpers génération configs
- `Utils/entity/form-helpers.js` — Helpers formulaires

### TableConfig

**Génération depuis descriptors :**
```javascript
const descriptors = getResourceFieldDescriptors(ctx);
const tableConfig = TableConfig.fromDescriptors(descriptors, ctx);
```

**Processus :**
1. `createColumnFromDescriptor()` : Crée une `TableColumnConfig` depuis un descriptor
2. Extrait : `general.label`, `general.icon`, `table.defaultVisible`, `table.visibleIf`, `table.cell.sizes`
3. Génère la config complète avec headers, visibilité, formatage

### BulkConfig

**Génération depuis descriptors :**
```javascript
const bulkConfig = BulkConfig.fromDescriptors(descriptors, ctx);
```

**Processus :**
1. Itère sur les champs avec `edition.bulk.enabled: true`
2. `createBulkFieldFromDescriptor()` : Crée la config d'un champ bulk
3. Génère la liste des champs quickedit

### FormConfig

**Génération depuis descriptors :**
```javascript
const fieldsConfig = createFieldsConfigFromDescriptors(descriptors, ctx);
```

**Processus :**
1. Itère sur les champs avec `edition.form.*`
2. Extrait : `type`, `required`, `validation`, `options`, `placeholder`, etc.
3. Génère la config complète pour chaque champ

---

## 🖼️ Couche 6 : Renderers (Composants Vue génériques)

### Rôle
Composants génériques qui utilisent les configs pour rendre les interfaces.

### Fichiers clés
- `Pages/Organismes/entity/EntityTanStackTable.vue` — Tableau principal
- `Pages/Organismes/entity/EntityModal.vue` — Modal d'affichage
- `Pages/Organismes/entity/EntityQuickEditPanel.vue` — Panneau quickedit
- `Pages/Organismes/entity/EntityActions.vue` — Menu d'actions

### EntityTanStackTable

**Fonctionnement :**
1. Reçoit `tableConfig` (depuis `TableConfig.build()`)
2. Pour chaque cellule : appelle `entity.toCell(fieldKey)` pour générer le formatage
3. Utilise `CellRenderer` pour afficher les cellules (badge, text, route, image, etc.)

**Props :**
- `entity-type` : Type d'entité (ex: "resources")
- `table-config` : Configuration du tableau
- `response-adapter` : Adapter pour transformer les réponses backend

### EntityModal

**Fonctionnement :**
1. Charge dynamiquement les vues via `resolveEntityViewComponent(entityType, view)`
2. Passe l'entité au composant de vue
3. Gère la navigation entre les vues (Large, Compact, Minimal, Text)

**Props :**
- `entity-type` : Type d'entité
- `entity` : Données de l'entité
- `view` : Vue à afficher (large, compact, minimal, text)

### EntityQuickEditPanel

**Fonctionnement :**
1. Charge `EntityQuickEdit.vue` (générique) ou `ResourceQuickEdit.vue` (spécifique)
2. Utilise `useBulkEditPanel` pour gérer l'agrégation et le dirty state
3. Gère la soumission via `buildPayload()`

**Props :**
- `entity-type` : Type d'entité
- `selected-entities` : Entités sélectionnées
- `is-admin` : Permissions admin

---

## 🎨 Couche 7 : Vues (Composants Vue spécifiques)

### Rôle
Composants Vue **manuels** qui définissent le layout et utilisent les méthodes du modèle.

### Fichiers clés
- `Pages/Molecules/entity/{entity}/{Entity}ViewLarge.vue`
- `Pages/Molecules/entity/{entity}/{Entity}ViewCompact.vue`
- `Pages/Molecules/entity/{entity}/{Entity}ViewMinimal.vue`
- `Pages/Molecules/entity/{entity}/{Entity}ViewText.vue`
- `Pages/Molecules/entity/{entity}/{Entity}EditLarge.vue`
- `Pages/Molecules/entity/{entity}/{Entity}EditCompact.vue`
- `Pages/Molecules/entity/{entity}/{Entity}QuickEdit.vue` (optionnel)
- `Pages/Molecules/entity/EntityQuickEdit.vue` (générique, fallback)
- `Utils/entity/resolveEntityViewComponent.js` — Résolution dynamique

### Vues d'affichage (Large, Compact, Minimal, Text)

**Structure :**
```vue
<script setup>
import { Resource } from '@/Models/Entity/Resource';
import { getResourceFieldDescriptors } from '@/Entities/resource/resource-descriptors';

const props = defineProps({
  resource: { type: Object, required: true }
});

const entity = computed(() => new Resource(props.resource));
const descriptors = computed(() => getResourceFieldDescriptors(ctx));
</script>

<template>
  <!-- Layout manuel -->
  <div class="flex gap-2">
    <Badge v-bind="entity.toCell('rarity').params" />
    <Badge v-bind="entity.toCell('level').params" />
  </div>
</template>
```

**Utilisation :**
- `entity.toCell(fieldKey)` : Obtient la cellule formatée
- `descriptors[fieldKey].general.label` : Obtient le label
- `descriptors[fieldKey].general.icon` : Obtient l'icône
- `descriptors[fieldKey].permissions.visibleIf(ctx)` : Vérifie la visibilité

### Vues d'édition (EditLarge, EditCompact, QuickEdit)

**Structure :**
```vue
<script setup>
import { createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';
import { EntityFormField } from '@/Pages/Molecules/entity/EntityFormField';
import { useBulkEditPanel } from '@/Composables/entity/useBulkEditPanel';

const descriptors = getResourceFieldDescriptors(ctx);
const fieldsConfig = createFieldsConfigFromDescriptors(descriptors, ctx);
const { form, dirty, aggregate, buildPayload } = useBulkEditPanel(selectedEntities, fieldMeta);
</script>

<template>
  <EntityFormField
    v-for="field in fieldsConfig"
    :key="field.key"
    :field-config="field"
    :model-value="form[field.key]"
    @update:model-value="form[field.key] = $event"
  />
</template>
```

**Utilisation :**
- `createFieldsConfigFromDescriptors()` : Génère la config des champs
- `EntityFormField` : Rend chaque champ selon sa config
- `useBulkEditPanel` : Gère l'agrégation et le dirty state (QuickEdit uniquement)
- `useForm` : Gère la soumission (EditLarge/Compact)

### Résolution dynamique

**`resolveEntityViewComponent(entityType, view)` :**
- Charge le composant approprié selon le type d'entité et la vue
- Utilise `import.meta.glob` pour que Vite puisse résoudre les imports dynamiques
- Fallback vers `EntityQuickEdit.vue` si le composant spécifique n'existe pas

---

## 🔄 Interactions entre couches

### Génération d'une cellule de tableau

```
EntityTanStackTable
  → entity.toCell('rarity')
    → BaseModel.toCell()
      → getFormatter('rarity')
        → RarityFormatter.toCell()
          → SharedConstants.RARITY_GRADIENT
            → { type: 'badge', value: 'Rare', params: {...} }
```

### Génération d'un header de colonne

```
ResourceTableConfig
  → getResourceFieldDescriptors(ctx)
    → TableConfig.fromDescriptors(descriptors, ctx)
      → createColumnFromDescriptor(fieldKey, descriptor, ctx)
        → TableColumnConfig
          → { id, label, icon, defaultVisible, format, ... }
```

### Affichage d'une vue Large

```
EntityModal
  → resolveEntityViewComponent('resource', 'large')
    → ResourceViewLarge.vue
      → new Resource(entity)
      → getResourceFieldDescriptors(ctx)
      → entity.toCell('rarity')
        → (même processus que cellule tableau)
```

### Édition QuickEdit

```
EntityQuickEditPanel
  → resolveEntityViewComponentSync('resource', 'quickedit')
    → EntityQuickEdit.vue
      → getResourceFieldDescriptors(ctx)
      → createFieldsConfigFromDescriptors(descriptors, ctx)
      → useBulkEditPanel(selectedEntities, fieldMeta)
      → EntityFormField (pour chaque champ)
        → buildPayload()
          → getMapperForEntityType('resources')
            → ResourceMapper.fromBulkForm(form)
```

---

## 📁 Structure des fichiers

Voir [STRUCTURE.md](./STRUCTURE.md) pour la structure complète des fichiers.

---

## 🎯 Concepts clés

### Séparation des responsabilités
- **Models** : Logique métier et formatage
- **Formatters** : Formatage centralisé réutilisable
- **Descriptors** : Configuration déclarative (pas de logique)
- **Configs** : Génération de configurations depuis descriptors
- **Vues** : Layout manuel (pas de génération automatique)

### Source de vérité unique
- **Descriptors** : Source de vérité pour la configuration UX
- **SharedConstants** : Source de vérité pour les constantes partagées
- **FormatterRegistry** : Source de vérité pour le formatage

### Génération vs Manuel
- **Généré automatiquement** : Tableaux (headers, cellules), QuickEdit (champs), Formulaires (champs)
- **Manuel** : Vues (Large, Compact, Minimal, Text), Layout des vues d'édition

### Permissions
- **Backend** : Source de vérité pour la sécurité
- **Frontend** : `permissions.visibleIf`, `permissions.editableIf` dans descriptors pour l'UX
- **Table** : `table.defaultVisible`, `table.visibleIf` pour la visibilité des colonnes

### Formatage
- **Formatters** : Formatage centralisé par type de champ (rarity, level, etc.)
- **Models.toCell()** : Point d'entrée unique pour générer les cellules
- **Cache** : Les cellules sont mises en cache dans `_cellCache`

---

## 🔗 Liens utiles

- [MODELS.md](./MODELS.md) — Guide détaillé des modèles
- [FORMATTERS.md](./FORMATTERS.md) — Guide détaillé des formatters
- [DESCRIPTORS.md](./DESCRIPTORS.md) — Guide détaillé des descriptors
- [CONFIGS.md](./CONFIGS.md) — Guide détaillé des configurations
- [RENDERERS.md](./RENDERERS.md) — Guide détaillé des renderers
- [VIEWS.md](./VIEWS.md) — Guide détaillé des vues
- [FLUX_COMPLETS.md](./FLUX_COMPLETS.md) — Flux détaillés pour chaque format
