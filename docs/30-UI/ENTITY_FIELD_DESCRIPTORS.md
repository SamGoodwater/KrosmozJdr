# Entity Field Descriptors — Architecture (Option B)

## Objectif

Mettre en place une **source de vérité frontend** par champ (“field descriptor”) afin de générer :

- les **cellules table** (`Cell{type,value,params}`) côté frontend,
- les **vues** (`large/compact/minimal/text`),
- les **configs de formulaire** (`EntityEditForm`) et de **bulk edit** (`useBulkEditPanel`),

…tout en gardant :

- **Backend = vérité** pour les **permissions** (Policies/Gates) + validation,
- un payload serveur stable : `meta` (capabilities + filterOptions + query).

## Pourquoi Option B ?

Option B = le backend renvoie des **entités brutes** et le frontend **génère** les `cells`.

- **Avantage** : cohérence totale (table + modal + form = mêmes règles).
- **Coût** : plus de logique frontend (adapter + descriptors + tests).

## Contrat backend (Table v2 — mode entities)

Endpoint (exemple Ressources) :

- `GET /api/tables/resources?format=entities&limit=5000`

Payload attendu :

- `meta` : identique à Table v2 (capabilities + filterOptions + query)
- `entities[]` : entités brutes nécessaires au rendu **et** au tri/filtre côté client.

> Règle sécurité : le backend **filtre** aussi les champs renvoyés (read), le front ne fait que de l’UX.

## Contrat frontend (adapter)

Le frontend reçoit `{ meta, entities }` et transforme en `{ meta, rows }` :

- `rows[]` : `TableRow[]`
  - `id`
  - `cells` (générées)
  - `rowParams.entity` (entité brute, pour modal/bulk/quick edit)

## Affichage : tailles + contexts (convention)

Chaque champ peut définir :

- **Tailles** : `small | normal | large`
  - **small** : **icône + valeur** (sans label)
  - **normal** : **label + valeur** (sans icône)
  - **large** : **icône + label + valeur**
- **Contexts** : mapping “où” le champ est rendu :
  - `table | text | compact | minimal | extended` → taille par défaut

Conventions recommandées (v1) :

- `table -> small`
- `text -> normal`
- `compact -> small`
- `minimal -> small`
- `extended -> large`

## Structure recommandée (par entité)

Exemple Ressource :

- `resources/js/Entities/resource/resource-descriptors.js`
  - source de vérité (label, format, permissions UX, etc.)
  - `display.contexts` + `display.sizes` (small/normal/large)
- `resources/js/Entities/resource/resource-adapter.js`
  - génération de `cells`
  - `adapt*Response(payload) -> { meta, rows }`

## Migration (checklist)

- [ ] Ajouter `format=entities` côté backend pour l’entité (sans casser le mode `cells`)
- [ ] Créer `*descriptors` + `*adapter`
- [ ] Brancher `responseAdapter` dans la page Index (sur `EntityTanStackTable`)
- [ ] Remplacer progressivement :
  - [ ] `field-schema` -> `fieldsConfig` généré depuis descriptors
  - [ ] bulk meta -> depuis descriptors
  - [ ] `EntityView*` -> rendu basé sur des descriptors (au lieu d’itérer les props)

## Formulaires & Bulk Edit depuis les descriptors (v1)

On introduit un bloc optionnel `edit.form` dans chaque descriptor (par champ) :

- `type`: `text | number | textarea | select | checkbox | file`
- `required`, `showInCompact`
- `options` (array ou fonction `ctx => options`)
- `defaultValue`
- (optionnel) `help`, `tooltip`, `placeholder` (UX)
- `bulk` (optionnel) : `enabled`, `nullable`, `build(raw, ctx)`

Générateurs (frontend) :

- `createFieldsConfigFromDescriptors(descriptors, ctx)` → `fieldsConfig` pour `EntityEditForm`
- `createDefaultEntityFromDescriptors(descriptors)` → `defaultEntity` pour `CreateEntityModal`
- `createBulkFieldMetaFromDescriptors(descriptors, ctx)` → `fieldMeta` pour `useBulkEditPanel`

> Remarque : le backend reste la vérité sécurité. `edit.form` sert à l’UX, l’API est validée côté Laravel.

## Quick Edit (sélection multiple) — `viewFields.quickEdit`

Le **quick edit** est un panneau d’édition en masse “côté table” (sélection multiple) basé sur :

- `useBulkEditPanel` (agrégation, “valeurs différentes”, payload)
- les descriptors (`edit.form.bulk`) pour savoir quels champs sont bulk-editables et comment construire le payload

### Convention

- Chaque entité peut définir `viewFields.quickEdit` (liste ordonnée de clés de champs) dans `*descriptors.js`.
- Le composant générique `EntityQuickEditPanel` utilise :
  - **priorité** : `viewFields.quickEdit` si présent
  - **fallback** : tous les champs où `edit.form.bulk.enabled === true`

> Important : la liste `quickEdit` doit rester cohérente avec le **bulk endpoint backend** (sinon champs ignorés / 422).

### Sections (groupes)

Pour améliorer la lisibilité, un champ peut définir `edit.form.group` (string) :

- Exemple : `"Statut" | "Métier" | "Métadonnées" | "Contenu" | "Image"`
- Le `EntityQuickEditPanel` regroupe alors les champs par section, en conservant l’ordre (groupes ordonnés par première apparition).

### Exemple

- `Resource` : `resource_type_id`, `rarity`, `level`, `usable`, `auto_update`, `is_visible`, `price`, …
- `ResourceType` : `decision`, `usable`, `is_visible`
- `Item` : `rarity`, `level`, `usable`, `auto_update`, `is_visible`, `price`, …

## Pattern "minimal → hover details"

Recommandation UX :

- **Minimal (base)** : affiche uniquement les champs "importants" en **small** (icône + valeur) avec tooltips.
- **Hover** : déplie un panneau qui affiche les champs manquants en **extended** (plus détaillé).

---

## 📚 Exemples concrets

### Exemple 1 : Champ texte simple (Resource.description)

```javascript
description: {
  key: "description",
  label: "Description",
  icon: "fa-solid fa-align-left",
  format: "text",
  display: {
    views: DEFAULT_RESOURCE_FIELD_VIEWS,
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
},
```

### Exemple 2 : Champ select avec relation (Resource.resource_type_id)

```javascript
resource_type_id: {
  key: "resource_type_id",
  label: "Type de ressource",
  icon: "fa-solid fa-tag",
  format: "text",
  display: {
    views: DEFAULT_RESOURCE_FIELD_VIEWS,
    sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
  },
  edit: {
    form: {
      type: "select",
      group: "Relations",
      required: false,
      showInCompact: true,
      options: ctx?.meta?.filterOptions?.resource_type_id || [],
      bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
    },
  },
},
```

### Exemple 3 : Champ booléen avec badge (Resource.usable)

```javascript
usable: {
  key: "usable",
  label: "Utilisable",
  icon: "fa-solid fa-check-circle",
  format: "bool",
  display: {
    views: DEFAULT_RESOURCE_FIELD_VIEWS,
    sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } },
  },
  edit: {
    form: {
      type: "checkbox",
      group: "Statut",
      required: false,
      showInCompact: true,
      bulk: { enabled: true, build: (v) => Boolean(v) },
    },
  },
},
```

### Exemple 4 : Champ enum avec badge (Resource.is_visible)

```javascript
is_visible: {
  key: "is_visible",
  label: "Visibilité",
  icon: "fa-solid fa-eye",
  format: "enum",
  display: {
    views: DEFAULT_RESOURCE_FIELD_VIEWS,
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
        { value: "game_master", label: "Maître de jeu" },
        { value: "admin", label: "Administrateur" },
      ],
      bulk: { enabled: true, build: (v) => String(v) },
    },
  },
},
```

### Exemple 5 : Adapter avec cellule route (Resource.name)

```javascript
if (colId === "name") {
  const name = entity?.name || "-";
  const href = route("entities.resources.show", entity?.id);
  return {
    type: "route",
    value: name,
    params: {
      href,
      tooltip: name === "-" ? "" : name,
      searchValue: name === "-" ? "" : name,
      sortValue: name,
    },
  };
}
```

### Exemple 6 : Adapter avec relation (Npc.classe_id)

```javascript
if (colId === "classe" || colId === "classe_id") {
  const classe = entity?.classe || null;
  const classeName = classe?.name || "-";
  return {
    type: "text",
    value: classeName,
    params: {
      tooltip: classeName === "-" ? "" : classeName,
      searchValue: classeName === "-" ? "" : classeName,
      sortValue: classeName,
    },
  };
}
```

---

## 🔄 Patterns récurrents

### Pattern 1 : Gestion des relations (belongs to)

**Dans le descriptor** :
- Utiliser `_id` comme clé (ex: `classe_id`)
- Le backend renvoie la relation complète (ex: `classe: { id, name }`)

**Dans l'adapter** :
- Gérer les deux cas : `colId === "classe"` et `colId === "classe_id"`
- Extraire le nom depuis la relation : `entity?.classe?.name`

### Pattern 2 : Champs nullable en bulk

**Dans le descriptor** :
```javascript
bulk: { 
  enabled: true, 
  nullable: true, 
  build: (v) => (v === "" ? null : String(v)) 
}
```

**Dans le BulkController** :
```php
'champ' => ['sometimes', 'nullable', 'string', 'max:255'],
```

### Pattern 3 : Permissions conditionnelles

**Dans le descriptor** :
```javascript
visibleIf: () => canCreateAny, // Afficher seulement si l'utilisateur peut créer
editableIf: () => canUpdateAny, // Éditer seulement si l'utilisateur peut mettre à jour
```

**Note** : Le backend doit aussi vérifier les permissions (Policies Laravel).

### Pattern 4 : Groupes dans le quick edit

**Dans le descriptor** :
```javascript
edit: {
  form: {
    group: "Statut", // Regroupe les champs dans le quick edit panel
    // ...
  },
}
```

Les groupes sont automatiquement organisés par ordre d'apparition dans `quickEdit`.

---

## 🛠️ Troubleshooting

### Problème : Le champ n'apparaît pas dans le quick edit

**Solutions** :
1. Vérifier que le champ est dans `{ENTITY}_VIEW_FIELDS.quickEdit`
2. Vérifier que `edit.form.bulk.enabled === true`
3. Vérifier que le champ est dans le BulkController backend

### Problème : Le champ n'est pas sauvegardé en bulk

**Solutions** :
1. Vérifier que le champ est dans la validation du BulkController
2. Vérifier que le champ est dans le `foreach` des champs à patcher
3. Vérifier les permissions (`updateAny` dans la Policy)

### Problème : La cellule ne s'affiche pas correctement

**Solutions** :
1. Vérifier que le cas est géré dans `build{Entity}Cell`
2. Vérifier que le format correspond (text, badge, route, etc.)
3. Vérifier que les valeurs nulles sont gérées (`entity?.champ || "-"`)

### Problème : Les options du select ne s'affichent pas

**Solutions** :
1. Vérifier que les options sont fournies par le backend (`meta.filterOptions.{champ}`)
2. Vérifier que le contexte est passé correctement : `get{Entity}FieldDescriptors({ meta })`
3. Vérifier que les options sont au bon format : `[{ value, label }, ...]`

---

## ✅ État d'implémentation

**Date de finalisation** : 2026-01-06

### Migration complète

- ✅ **16 entités migrées** vers le système de descriptors
- ✅ **15 contrôleurs bulk** créés et testés
- ✅ **16 contrôleurs table** supportent `?format=entities`
- ✅ **16 adapters frontend** créés
- ✅ **159 tests passent** (941 assertions) — Voir [TESTS_ENTITY_DESCRIPTORS_IMPLEMENTATION.md](../100-%20Done/TESTS_ENTITY_DESCRIPTORS_IMPLEMENTATION.md)

### Entités migrées

1. `resource` ✅
2. `resource_type` ✅
3. `item` ✅
4. `spell` ✅
5. `monster` ✅
6. `creature` ✅
7. `npc` ✅
8. `classe` ✅
9. `consumable` ✅
10. `campaign` ✅
11. `scenario` ✅
12. `attribute` ✅
13. `panoply` ✅
14. `capability` ✅
15. `specialization` ✅
16. `shop` ✅

### Tests

- ✅ **14 tests BulkControllers** (PHPUnit)
- ✅ **14 tests TableControllers** (PHPUnit)
- ✅ **12 tests Adapters** (Vitest)
- ✅ **4 tests Utils/Composables** (Vitest)

**Voir** :
- [ENTITY_DESCRIPTORS_MIGRATION_COMPLETE.md](../100-%20Done/ENTITY_DESCRIPTORS_MIGRATION_COMPLETE.md) — Détails de la migration
- [ENTITY_DESCRIPTORS_MAINTENANCE_GUIDE.md](./ENTITY_DESCRIPTORS_MAINTENANCE_GUIDE.md) — Guide de maintenance complet


