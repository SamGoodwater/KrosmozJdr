# Architecture du système d'entités frontend — Résumé complet

**Date** : 2026-01-XX  
**Version** : 2.0 (Système refactorisé avec descriptors centralisés)

---

## 🎯 Vue d'ensemble

Le système d'entités frontend de KrosmozJDR suit une architecture en **5 couches** qui transforme les données brutes du backend en interfaces utilisateur complètes (tableaux, vues, formulaires).

### Flux de données global

```
Backend (API)
   ↓
Adapter (createEntityAdapter)
   ↓
Mapper (optionnel, ex: ResourceMapper)
   ↓
Model (BaseModel + entités spécifiques)
   ↓
Formatter (FormatterRegistry)
   ↓
Descriptor (resource-descriptors.js)
   ↓
Config (TableConfig, BulkConfig)
   ↓
Renderer (EntityTanStackTable, EntityModal, EntityQuickEditPanel)
   ↓
Vue (Large, Compact, Minimal, Text, EditLarge, EditCompact, QuickEdit)
```

---

## 📦 Couche 1 : Adapter & Mapper

### Fichiers clés
- `Utils/Entity/createEntityAdapter.js` — Factory générique pour créer des adapters
- `Utils/Entity/MapperRegistry.js` — Registre centralisé des mappers
- `Mappers/Entity/ResourceMapper.js` — Mapper spécifique (exemple)

### Rôle
Transforme la réponse backend `{ meta, entities }` en structure compatible avec TanStackTable `{ meta, rows }`.

**Processus :**
1. **Adapter** (`createEntityAdapter`) :
   - Si un **Mapper** existe → utilise `Mapper.fromApiArray(entities)`
   - Sinon → instancie directement les modèles : `new ModelClass(entityData)`
   - Crée les `rows` avec `{ id, cells: {}, rowParams: { entity } }`

2. **Mapper** (optionnel, ex: `ResourceMapper`) :
   - Transforme les données brutes en structure frontend
   - Gère les transformations complexes (ex: `fromBulkForm()` pour quickedit)

---

## 🧠 Couche 2 : Models

### Fichiers clés
- `Models/BaseModel.js` — Classe de base pour tous les modèles
- `Models/Entity/Resource.js` — Modèle spécifique (exemple)
- `Models/Entity/*.js` — Autres modèles (Item, Spell, Monster, etc.)

### Rôle
Encapsule la logique métier et le formatage des données.

**Fonctionnalités principales :**
- **`toCell(fieldKey, options)`** : Génère un objet `Cell` formaté pour les tableaux
  - Utilise `FormatterRegistry` pour trouver le formatter approprié
  - Cache les cellules générées (`_cellCache`)
  - Retourne `{ type, value, params }` (ex: `{ type: 'badge', value: 'Rare', params: { color: 'success', icon: 'fa-gem' } }`)

- **Propriétés communes** : `id`, `created_at`, `updated_at`, `can.*` (permissions)

- **Extraction normalisée** : Gère les Proxies Vue/Inertia pour accéder aux données

---

## 🎨 Couche 3 : Formatters

### Fichiers clés
- `Utils/Formatters/BaseFormatter.js` — Classe abstraite
- `Utils/Formatters/FormatterRegistry.js` — Registre centralisé
- `Utils/Formatters/*.js` — Formatters spécifiques (RarityFormatter, LevelFormatter, etc.)
- `Utils/Entity/SharedConstants.js` — Constantes partagées (couleurs, labels, icônes)

### Rôle
Centralise le formatage des valeurs en labels, badges et cellules.

**Structure d'un formatter :**
```javascript
class RarityFormatter extends BaseFormatter {
  static name = 'RarityFormatter';
  static fieldKeys = ['rarity'];
  
  static format(value) { /* → "Rare" */ }
  static toCell(value, options) { /* → { type: 'badge', value: 'Rare', params: { color: 'success' } } */ }
}
```

**Enregistrement :**
- Tous les formatters sont enregistrés dans `FormatterRegistry` au démarrage (`app.js`)
- `BaseModel.toCell()` utilise `getFormatter(fieldKey)` pour trouver le formatter approprié

**Constantes partagées :**
- `SharedConstants.js` : `FIELD_LABELS`, `FIELD_ICONS`, `LEVEL_COLORS`, `RARITY_GRADIENT`, `USER_ROLES`

---

## 📜 Couche 4 : Descriptors

### Fichiers clés
- `Entities/{entity}/{entity}-descriptors.js` — Descriptors spécifiques (ex: `resource-descriptors.js`)
- `Entities/entity-registry.js` — Registre centralisé des descriptors

### Rôle
**Source de vérité déclarative** pour la configuration UX de chaque entité.

**Structure d'un descriptor :**
```javascript
{
  fieldKey: {
    general: { label, icon, tooltip },
    permissions: { visibleIf, editableIf },
    table: {
      defaultVisible: { xs, sm, md, lg, xl },
      visibleIf: (ctx) => boolean,
      cell: { sizes: { xs: { mode }, ... } }
    },
    display: { tooltip, style, color, format },
    edition: {
      form: { type, required, validation, options, ... },
      bulk: { enabled, nullable }
    }
  },
  _tableConfig: { id, entityType, quickEdit, actions, features },
  _quickeditConfig: { fields }
}
```

**Utilisation :**
- **TableConfig** : Génère les colonnes du tableau depuis `table.*`
- **BulkConfig** : Génère les champs quickedit depuis `edition.bulk.*`
- **FormConfig** : Génère les formulaires depuis `edition.form.*`
- **Vues** : Utilisent `general.*`, `display.*`, `permissions.*` pour l'affichage

---

## ⚙️ Couche 5 : Configs & Helpers

### Fichiers clés
- `Utils/Entity/Configs/TableConfig.js` — Configuration des tableaux
- `Utils/Entity/Configs/TableColumnConfig.js` — Configuration d'une colonne
- `Utils/Entity/Configs/BulkConfig.js` — Configuration de l'édition en masse
- `Utils/Entity/Configs/FormConfig.js` — Configuration des formulaires
- `Utils/entity/descriptor-form.js` — Helpers pour générer les configs depuis descriptors
- `Utils/entity/form-helpers.js` — Helpers pour l'initialisation des formulaires

### Rôle
Génère les configurations utilisables par les composants Vue.

**TableConfig :**
- `TableConfig.fromDescriptors(descriptors, ctx)` : Génère la config complète du tableau
- `createColumnFromDescriptor()` : Crée une `TableColumnConfig` depuis un descriptor
- Gère : headers (label, icon), visibilité (defaultVisible, visibleIf), formatage (cell.sizes)

**BulkConfig :**
- `BulkConfig.fromDescriptors(descriptors, ctx)` : Génère la config quickedit
- `createBulkFieldFromDescriptor()` : Crée la config d'un champ bulk

**FormConfig :**
- Génère les configurations de formulaires depuis `edition.form.*`

---

## 🖼️ Couche 6 : Renderers (Composants Vue génériques)

### Fichiers clés
- `Pages/Organismes/entity/EntityTanStackTable.vue` — Tableau principal
- `Pages/Organismes/entity/EntityModal.vue` — Modal d'affichage
- `Pages/Organismes/entity/EntityQuickEditPanel.vue` — Panneau quickedit
- `Pages/Organismes/entity/EntityActions.vue` — Menu d'actions

### Rôle
Composants génériques qui utilisent les configs pour rendre les interfaces.

**EntityTanStackTable :**
- Reçoit `tableConfig` (depuis `TableConfig.build()`)
- Pour chaque cellule : appelle `entity.toCell(fieldKey)` pour générer le formatage
- Utilise `CellRenderer` pour afficher les cellules (badge, text, route, image, etc.)

**EntityModal :**
- Charge dynamiquement les vues via `resolveEntityViewComponent(entityType, view)`
- Passe l'entité au composant de vue

**EntityQuickEditPanel :**
- Charge `EntityQuickEdit.vue` (générique) ou `ResourceQuickEdit.vue` (spécifique)
- Utilise `useBulkEditPanel` pour gérer l'agrégation et le dirty state

---

## 🎨 Couche 7 : Vues (Composants Vue spécifiques)

### Fichiers clés
- `Pages/Molecules/entity/{entity}/{Entity}ViewLarge.vue` — Vue Large
- `Pages/Molecules/entity/{entity}/{Entity}ViewCompact.vue` — Vue Compact
- `Pages/Molecules/entity/{entity}/{Entity}ViewMinimal.vue` — Vue Minimal
- `Pages/Molecules/entity/{entity}/{Entity}ViewText.vue` — Vue Text
- `Pages/Molecules/entity/{entity}/{Entity}EditLarge.vue` — Édition Large
- `Pages/Molecules/entity/{entity}/{Entity}EditCompact.vue` — Édition Compact
- `Pages/Molecules/entity/{entity}/{Entity}QuickEdit.vue` — QuickEdit (optionnel)
- `Pages/Molecules/entity/EntityQuickEdit.vue` — QuickEdit générique (fallback)
- `Utils/entity/resolveEntityViewComponent.js` — Résolution dynamique des composants

### Rôle
Composants Vue **manuels** qui définissent le layout et utilisent les méthodes du modèle.

**Vues d'affichage (Large, Compact, Minimal, Text) :**
- Reçoivent l'entité en prop
- Utilisent `entity.toCell(fieldKey)` pour obtenir les cellules formatées
- Utilisent `getFieldDescriptors()` pour obtenir les métadonnées (label, icon, tooltip)
- Définissent le layout manuellement (badges, sections, etc.)

**Vues d'édition (EditLarge, EditCompact, QuickEdit) :**
- Utilisent `createFieldsConfigFromDescriptors()` pour générer la config des champs
- Utilisent `EntityFormField.vue` pour rendre chaque champ
- Utilisent `useBulkEditPanel` (pour QuickEdit) ou `useForm` (pour EditLarge/Compact)
- Utilisent `useEntityFormSubmit` pour gérer la soumission

**Résolution dynamique :**
- `resolveEntityViewComponent(entityType, view)` : Charge le composant approprié
- Utilise `import.meta.glob` pour que Vite puisse résoudre les imports dynamiques

---

## 🔄 Flux détaillé : Génération d'une cellule de tableau

```
1. Backend renvoie : { id: 1, name: "Bois", rarity: 2, level: 15 }

2. Adapter transforme :
   createEntityAdapter(Resource, ResourceMapper)
   → { meta, rows: [{ id: 1, cells: {}, rowParams: { entity: Resource instance } }] }

3. Tableau demande une cellule :
   entity.toCell('rarity', { size: 'md' })

4. BaseModel.toCell() :
   - Cherche dans _cellCache (si existe, retourne)
   - Appelle getFormatter('rarity') → RarityFormatter
   - Appelle RarityFormatter.toCell(2, { size: 'md' })

5. RarityFormatter.toCell() :
   - Utilise RARITY_GRADIENT depuis SharedConstants
   - Retourne { type: 'badge', value: 'Rare', params: { color: 'success', icon: 'fa-circle' } }

6. Tableau affiche :
   <Badge color="success" icon="fa-circle">Rare</Badge>
```

---

## 🔄 Flux détaillé : Génération d'un header de colonne

```
1. ResourceTableConfig.js appelle :
   const descriptors = getResourceFieldDescriptors(ctx);
   const tableConfig = TableConfig.fromDescriptors(descriptors, ctx);

2. TableConfig.fromDescriptors() :
   - Itère sur les descriptors (sauf ceux commençant par '_')
   - Pour chaque champ avec table.* :
     - Appelle createColumnFromDescriptor(fieldKey, descriptor, ctx)

3. createColumnFromDescriptor() :
   - Extrait general.label, general.icon depuis le descriptor
   - Extrait table.defaultVisible, table.visibleIf
   - Extrait table.cell.sizes pour le formatage
   - Crée TableColumnConfig avec ces infos

4. TableColumnConfig.build() :
   - Retourne { id, label, icon, defaultVisible, format, ... }

5. EntityTanStackTable utilise cette config pour afficher les headers
```

---

## 🔄 Flux détaillé : Affichage d'une vue Large

```
1. EntityModal appelle :
   const component = await resolveEntityViewComponent('resource', 'large');
   // → ResourceViewLarge.vue

2. ResourceViewLarge.vue :
   - Reçoit resource en prop
   - Crée instance : const entity = new Resource(resource)
   - Récupère descriptors : const descriptors = getResourceFieldDescriptors(ctx)

3. Pour chaque champ à afficher :
   - entity.toCell('rarity') → { type: 'badge', value: 'Rare', params: {...} }
   - descriptors.rarity.general.label → "Rareté"
   - descriptors.rarity.general.icon → "fa-solid fa-gem"
   - descriptors.rarity.permissions.visibleIf(ctx) → true/false

4. Affiche le layout manuel avec badges, sections, etc.
```

---

## 🔄 Flux détaillé : Édition QuickEdit

```
1. EntityQuickEditPanel charge :
   const component = resolveEntityViewComponentSync('resource', 'quickedit');
   // → EntityQuickEdit.vue (générique) ou ResourceQuickEdit.vue (spécifique)

2. EntityQuickEdit.vue :
   - Reçoit selectedEntities, isAdmin, extraCtx
   - Récupère descriptors : getResourceFieldDescriptors(ctx)
   - Génère fieldsConfig : createFieldsConfigFromDescriptors(descriptors, ctx)
   - Génère fieldMeta : createBulkFieldMetaFromDescriptors(descriptors, ctx)

3. useBulkEditPanel(selectedEntities, fieldMeta) :
   - Agrège les valeurs : aggregate = { rarity: { same: true, value: 2 }, ... }
   - Gère le dirty state : dirty = { rarity: false, ... }
   - Gère le form state : form = { rarity: '2', ... }

4. EntityFormField.vue :
   - Rend chaque champ selon fieldsConfig
   - Utilise SelectSearchField pour les selects avec searchable: true
   - Utilise ToggleCore pour les checkboxes
   - Affiche "valeurs différentes" si aggregate[key].same === false

5. Soumission :
   - buildPayload() utilise getMapperForEntityType() si disponible
   - Sinon, utilise directement form
   - Envoie au backend via API
```

---

## 📁 Structure des fichiers

```
resources/js/
├── Models/
│   ├── BaseModel.js
│   └── Entity/
│       ├── Resource.js
│       ├── Item.js
│       └── ...
├── Mappers/
│   └── Entity/
│       └── ResourceMapper.js
├── Utils/
│   ├── Entity/
│   │   ├── SharedConstants.js          # Constantes partagées
│   │   ├── MapperRegistry.js           # Registre des mappers
│   │   ├── createEntityAdapter.js      # Factory adapter
│   │   ├── Configs/
│   │   │   ├── TableConfig.js          # Config tableaux
│   │   │   ├── TableColumnConfig.js    # Config colonne
│   │   │   ├── BulkConfig.js           # Config bulk
│   │   │   └── FormConfig.js           # Config formulaires
│   │   └── Constants.js                # Constantes (CELL_TYPES, etc.)
│   ├── entity/
│   │   ├── descriptor-form.js          # Helpers form depuis descriptors
│   │   ├── form-helpers.js             # Helpers formulaires
│   │   └── resolveEntityViewComponent.js # Résolution vues
│   └── Formatters/
│       ├── BaseFormatter.js
│       ├── FormatterRegistry.js
│       ├── RarityFormatter.js
│       ├── LevelFormatter.js
│       └── ...
├── Entities/
│   ├── entity-registry.js              # Registre central
│   ├── resource/
│   │   ├── resource-descriptors.js     # Descriptors Resource
│   │   └── ResourceTableConfig.js      # Config table (optionnel, peut utiliser fromDescriptors)
│   ├── item/
│   │   └── item-descriptors.js
│   └── ...
├── Composables/
│   └── entity/
│       ├── useBulkEditPanel.js         # Logique bulk edit
│       ├── useEntityFieldHelpers.js    # Helpers champs
│       ├── useEntityFieldFilter.js     # Filtrage champs
│       └── useEntityFormSubmit.js      # Soumission formulaires
└── Pages/
    ├── Organismes/entity/
    │   ├── EntityTanStackTable.vue     # Tableau principal
    │   ├── EntityModal.vue              # Modal affichage
    │   ├── EntityQuickEditPanel.vue     # Panneau quickedit
    │   └── EntityActions.vue            # Menu actions
    ├── Molecules/entity/
    │   ├── EntityQuickEdit.vue          # QuickEdit générique
    │   ├── EntityFormField.vue          # Champ formulaire générique
    │   └── {entity}/
    │       ├── {Entity}ViewLarge.vue
    │       ├── {Entity}ViewCompact.vue
    │       ├── {Entity}ViewMinimal.vue
    │       ├── {Entity}ViewText.vue
    │       ├── {Entity}EditLarge.vue
    │       ├── {Entity}EditCompact.vue
    │       └── {Entity}QuickEdit.vue    # Optionnel
    └── Pages/entity/{entity}/
        └── Index.vue                    # Page liste
```

---

## 🎯 Concepts clés

### 1. **Séparation des responsabilités**
- **Models** : Logique métier et formatage
- **Formatters** : Formatage centralisé réutilisable
- **Descriptors** : Configuration déclarative (pas de logique)
- **Configs** : Génération de configurations depuis descriptors
- **Vues** : Layout manuel (pas de génération automatique)

### 2. **Source de vérité unique**
- **Descriptors** : Source de vérité pour la configuration UX
- **SharedConstants** : Source de vérité pour les constantes partagées
- **FormatterRegistry** : Source de vérité pour le formatage

### 3. **Génération vs Manuel**
- **Généré automatiquement** : Tableaux (headers, cellules), QuickEdit (champs), Formulaires (champs)
- **Manuel** : Vues (Large, Compact, Minimal, Text), Layout des vues d'édition

### 4. **Permissions**
- **Backend** : Source de vérité pour la sécurité
- **Frontend** : `permissions.visibleIf`, `permissions.editableIf` dans descriptors pour l'UX
- **Table** : `table.defaultVisible`, `table.visibleIf` pour la visibilité des colonnes

### 5. **Formatage**
- **Formatters** : Formatage centralisé par type de champ (rarity, level, etc.)
- **Models.toCell()** : Point d'entrée unique pour générer les cellules
- **Cache** : Les cellules sont mises en cache dans `_cellCache`

---

## 🔗 Liens utiles

- [ARCHITECTURE_ENTITY_SYSTEM.md](../110-%20To%20Do/ARCHITECTURE_ENTITY_SYSTEM.md) — Architecture détaillée en 4 couches
- [ENTITY_FIELD_DESCRIPTORS_GUIDE.md](../30-UI/ENTITY_FIELD_DESCRIPTORS_GUIDE.md) — Guide complet des descriptors
- [SharedConstants.js](../../resources/js/Utils/Entity/SharedConstants.js) — Constantes partagées

---

**Note** : Ce document décrit le système actuel après la refactorisation complète. Tous les fichiers mentionnés existent et sont fonctionnels.
