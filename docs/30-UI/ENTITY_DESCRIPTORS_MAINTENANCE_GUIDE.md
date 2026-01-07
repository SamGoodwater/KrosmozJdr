# Guide de maintenance — Entity Field Descriptors

**Date de création** : 2026-01-06  
**Objectif** : Guide pratique pour maintenir et étendre le système de descriptors

---

## 📋 Table des matières

1. [Ajouter un nouveau champ à un descriptor existant](#1-ajouter-un-nouveau-champ-à-un-descriptor-existant)
2. [Créer un descriptor pour une nouvelle entité](#2-créer-un-descriptor-pour-une-nouvelle-entité)
3. [Créer un adapter pour une nouvelle entité](#3-créer-un-adapter-pour-une-nouvelle-entité)
4. [Créer un BulkController pour une nouvelle entité](#4-créer-un-bulkcontroller-pour-une-nouvelle-entité)
5. [Bonnes pratiques](#5-bonnes-pratiques)
6. [Checklist de migration](#6-checklist-de-migration)

---

## 1. Ajouter un nouveau champ à un descriptor existant

### Étapes

1. **Ouvrir le fichier descriptor** : `resources/js/Entities/{entity}/{entity}-descriptors.js`

2. **Ajouter le champ dans la fonction `get{Entity}FieldDescriptors`** :

```javascript
export function getResourceFieldDescriptors(ctx = {}) {
  // ... autres champs ...
  
  nouveau_champ: {
    key: "nouveau_champ",
    label: "Nouveau Champ",
    icon: "fa-solid fa-icon-name", // Optionnel
    format: "text", // text | number | bool | date | image | link | enum
    display: {
      views: DEFAULT_RESOURCE_FIELD_VIEWS,
      sizes: { 
        small: { mode: "text" }, 
        normal: { mode: "text" }, 
        large: { mode: "text" } 
      },
    },
    edit: {
      form: {
        type: "text", // text | number | textarea | select | checkbox | file
        group: "Métier", // Optionnel : groupe pour le quick edit
        placeholder: "Ex: valeur", // Optionnel
        required: false,
        showInCompact: true, // Afficher dans la vue compacte
        bulk: { 
          enabled: true, // Activer pour le bulk edit
          nullable: true, // Permettre de vider le champ
          build: (v) => (v === "" ? null : String(v)) // Transformation avant envoi
        },
        help: "Aide contextuelle", // Optionnel
        tooltip: "Tooltip au survol", // Optionnel
      },
    },
  },
}
```

3. **Ajouter le champ dans `{ENTITY}_VIEW_FIELDS`** si nécessaire :

```javascript
export const RESOURCE_VIEW_FIELDS = Object.freeze({
  quickEdit: [
    // ... autres champs ...
    "nouveau_champ", // Ajouter ici si nécessaire pour le quick edit
  ],
  compact: [
    // ... autres champs ...
    "nouveau_champ", // Ajouter ici si nécessaire pour la vue compacte
  ],
  extended: [
    // ... autres champs ...
    "nouveau_champ", // Ajouter ici si nécessaire pour la vue étendue
  ],
});
```

4. **Mettre à jour l'adapter** si le champ nécessite un rendu spécial :

```javascript
// Dans {entity}-adapter.js
export function buildResourceCell(colId, entity, ctx = {}, opts = {}) {
  // ... autres cas ...
  
  if (colId === "nouveau_champ") {
    const value = entity?.nouveau_champ || null;
    return {
      type: "text", // text | route | badge | image | link | etc.
      value: value || "-",
      params: {
        tooltip: value || "",
        searchValue: value || "",
        sortValue: value || "",
      },
    };
  }
}
```

5. **Mettre à jour le BulkController** si le champ doit être éditable en bulk :

```php
// Dans {Entity}BulkController.php
$validated = $request->validate([
    // ... autres champs ...
    'nouveau_champ' => ['sometimes', 'nullable', 'string', 'max:255'],
]);

// Dans le foreach des champs à patcher
foreach ([
    // ... autres champs ...
    'nouveau_champ',
] as $k) {
    if (array_key_exists($k, $validated)) {
        $patch[$k] = $validated[$k];
    }
}
```

6. **Mettre à jour les tests** :

```php
// Dans {Entity}BulkControllerTest.php
public function test_nouveau_champ_can_be_updated(): void
{
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $entity = Entity::factory()->create();

    $response = $this->actingAs($admin)
        ->patchJson('/api/entities/entities/bulk', [
            'ids' => [$entity->id],
            'nouveau_champ' => 'Nouvelle valeur',
        ]);

    $response->assertOk();
    $entity->refresh();
    $this->assertEquals('Nouvelle valeur', $entity->nouveau_champ);
}
```

---

## 2. Créer un descriptor pour une nouvelle entité

### Structure de base

**Fichier** : `resources/js/Entities/{entity}/{entity}-descriptors.js`

```javascript
/**
 * {Entity} field descriptors (Option B)
 *
 * @description
 * Source de vérité côté frontend pour :
 * - l'affichage (cellules table + vues)
 * - l'édition (forms / bulk)
 *
 * ⚠️ Sécurité : ces descriptors ne sont que de l'UX. Le backend reste la vérité (Policies + filtrage des champs).
 */

/**
 * @typedef {Object} {Entity}FieldDescriptor
 * @property {string} key
 * @property {string} label
 * @property {string} [description]
 * @property {string} [tooltip]
 * @property {string|null} [icon]
 * @property {string|null|"auto"} [color]
 * @property {"text"|"number"|"bool"|"date"|"image"|"link"|"enum"} [format]
 * @property {(ctx: any) => boolean} [visibleIf]
 * @property {(ctx: any) => boolean} [editableIf]
 * @property {Object} [display]
 * @property {Record<"table"|"text"|"compact"|"minimal"|"extended", { size: "small"|"normal"|"large", mode?: string }>} [display.views]
 * @property {Record<"small"|"normal"|"large", any>} [display.sizes]
 */

export const DEFAULT_{ENTITY}_FIELD_VIEWS = Object.freeze({
  table: { size: "small" },
  text: { size: "normal" },
  compact: { size: "small" },
  minimal: { size: "small" },
  extended: { size: "large" },
});

/**
 * Ordre d'affichage "{Entity}" par vue.
 */
export const {ENTITY}_VIEW_FIELDS = Object.freeze({
  quickEdit: [
    // Liste des champs pour le quick edit (sélection multiple)
    // Doit être aligné avec le BulkController backend
  ],
  compact: [
    // Liste des champs pour la vue compacte
  ],
  extended: [
    // Liste des champs pour la vue étendue
  ],
});

/**
 * Descriptors "{Entity}".
 *
 * @param {Object} ctx - Contexte (meta, capabilities, etc.)
 * @returns {Record<string, {Entity}FieldDescriptor>}
 */
export function get{Entity}FieldDescriptors(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || null;
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);

  return {
    id: {
      key: "id",
      label: "ID",
      icon: "fa-solid fa-hashtag",
      format: "number",
      visibleIf: () => canCreateAny,
      display: {
        views: DEFAULT_{ENTITY}_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
    name: {
      key: "name",
      label: "Nom",
      icon: "fa-solid fa-font",
      format: "text",
      display: {
        views: { ...DEFAULT_{ENTITY}_FIELD_VIEWS, table: { size: "small", mode: "route" } },
        sizes: { small: { mode: "route" }, normal: { mode: "route" }, large: { mode: "route" } },
      },
      edit: {
        form: {
          type: "text",
          required: true,
          showInCompact: true,
          bulk: { enabled: false }, // Le nom n'est généralement pas éditable en bulk
        },
      },
    },
    // ... autres champs ...
  };
}
```

### Exemples de champs courants

#### Champ texte simple

```javascript
description: {
  key: "description",
  label: "Description",
  icon: "fa-solid fa-align-left",
  format: "text",
  display: {
    views: DEFAULT_{ENTITY}_FIELD_VIEWS,
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

#### Champ select (relation)

```javascript
type_id: {
  key: "type_id",
  label: "Type",
  icon: "fa-solid fa-tag",
  format: "text",
  display: {
    views: DEFAULT_{ENTITY}_FIELD_VIEWS,
    sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
  },
  edit: {
    form: {
      type: "select",
      group: "Relations",
      required: false,
      showInCompact: true,
      options: ctx?.meta?.filterOptions?.type_id || [], // Options depuis le backend
      bulk: { 
        enabled: true, 
        nullable: true, 
        build: (v) => (v === "" ? null : Number(v)) 
      },
    },
  },
},
```

#### Champ booléen

```javascript
usable: {
  key: "usable",
  label: "Utilisable",
  icon: "fa-solid fa-check-circle",
  format: "bool",
  display: {
    views: DEFAULT_{ENTITY}_FIELD_VIEWS,
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

#### Champ enum (badge)

```javascript
is_visible: {
  key: "is_visible",
  label: "Visibilité",
  icon: "fa-solid fa-eye",
  format: "enum",
  display: {
    views: DEFAULT_{ENTITY}_FIELD_VIEWS,
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

---

## 3. Créer un adapter pour une nouvelle entité

### Structure de base

**Fichier** : `resources/js/Entities/{entity}/{entity}-adapter.js`

```javascript
/**
 * {Entity} adapter (Option B)
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2,
 * en générant les `cells` côté frontend à partir de `{entity}-descriptors`.
 *
 * @example
 * <EntityTanStackTable :response-adapter="adapt{Entity}EntitiesTableResponse" />
 */

import { DEFAULT_{ENTITY}_FIELD_VIEWS, get{Entity}FieldDescriptors } from "@/Entities/{entity}/{entity}-descriptors";
import { getTruncateClass, sizeToTruncateScale } from "@/Utils/entity/text-truncate";
import { route } from "@inertiajs/vue3";

// Helpers de formatage (réutiliser depuis resource-adapter.js si possible)
const formatDateFr = (isoString) => { /* ... */ };
const boolToOuiNon = (v) => (String(v) === "1" || v === true ? "Oui" : "Non");
const getOptionLabel = (options, value, fallback = "-") => { /* ... */ };

/**
 * Génère une cellule pour un champ donné.
 *
 * @param {string} colId - Identifiant de la colonne
 * @param {Object} entity - Entité brute
 * @param {Object} ctx - Contexte (meta, etc.)
 * @param {Object} opts - Options (context, size, etc.)
 * @returns {Object} Cell { type, value, params }
 */
export function build{Entity}Cell(colId, entity, ctx = {}, opts = {}) {
  const descriptors = get{Entity}FieldDescriptors(ctx);
  const d = descriptors[colId] || descriptors?.[colId?.replace(/-/g, "_")] || null;

  const context = opts?.context || "table";
  const viewCfg = resolveViewConfigFor(d, { view: context });
  const size = opts?.size || viewCfg?.size || "normal";
  const sizeCfg = d?.display?.sizes?.[size] || {};

  // Cas par défaut
  if (!d) {
    const raw = entity?.[colId] ?? null;
    return {
      type: "text",
      value: raw != null ? String(raw) : "-",
      params: { searchValue: String(raw ?? ""), sortValue: raw ?? "" },
    };
  }

  // Cas spécifiques par type de champ
  if (colId === "name") {
    const name = entity?.name || "-";
    const href = route(`entities.{entities}.show`, entity?.id);
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

  // ... autres cas spécifiques ...

  // Cas par défaut basé sur le format
  const raw = entity?.[colId] ?? null;
  return {
    type: "text",
    value: raw != null ? String(raw) : "-",
    params: {
      tooltip: raw != null ? String(raw) : "",
      searchValue: String(raw ?? ""),
      sortValue: raw ?? "",
    },
  };
}

/**
 * Adapte une réponse backend "entities" en TableResponse.
 *
 * @param {Object} response - Réponse backend { meta, entities }
 * @returns {Object} TableResponse { meta, rows }
 */
export function adapt{Entity}EntitiesTableResponse(response) {
  const { meta = {}, entities = [] } = response || {};

  const rows = (Array.isArray(entities) ? entities : []).map((entity) => {
    const cells = {};
    const descriptors = get{Entity}FieldDescriptors({ meta });

    // Générer les cellules pour tous les champs définis dans les descriptors
    Object.keys(descriptors).forEach((key) => {
      cells[key] = build{Entity}Cell(key, entity, { meta }, { context: "table" });
    });

    return {
      id: entity?.id ?? null,
      cells,
      rowParams: {
        entity, // Conserver l'entité brute pour le quick edit / modal
      },
    };
  });

  return {
    meta,
    rows,
  };
}
```

### Exemples de cellules

#### Cellule route (lien vers la page de détail)

```javascript
if (colId === "name") {
  const name = entity?.name || "-";
  const href = route("entities.{entities}.show", entity?.id);
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

#### Cellule badge (booléen)

```javascript
if (colId === "usable") {
  const value = entity?.usable ?? 0;
  const label = boolToOuiNon(value);
  return {
    type: "badge",
    value: label,
    params: {
      color: value ? "success" : "neutral",
      searchValue: label,
      sortValue: value ? 1 : 0,
    },
  };
}
```

#### Cellule badge (enum)

```javascript
if (colId === "is_visible") {
  const value = entity?.is_visible || "guest";
  const labels = {
    guest: "Invité",
    user: "Utilisateur",
    game_master: "Maître de jeu",
    admin: "Administrateur",
  };
  const colors = {
    guest: "neutral",
    user: "info",
    game_master: "warning",
    admin: "error",
  };
  return {
    type: "badge",
    value: labels[value] || value,
    params: {
      color: colors[value] || "neutral",
      searchValue: labels[value] || value,
      sortValue: value,
    },
  };
}
```

#### Cellule relation (belongs to)

```javascript
if (colId === "type" || colId === "type_id") {
  const type = entity?.type || null;
  const typeName = type?.name || "-";
  return {
    type: "text",
    value: typeName,
    params: {
      tooltip: typeName === "-" ? "" : typeName,
      searchValue: typeName === "-" ? "" : typeName,
      sortValue: typeName,
    },
  };
}
```

---

## 4. Créer un BulkController pour une nouvelle entité

### Structure de base

**Fichier** : `app/Http/Controllers/Api/{Entity}BulkController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entity\{Entity};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * API Bulk update pour les {entities}.
 *
 * @description
 * Applique un patch sur une liste d'IDs (sélection multiple). Seuls les champs fournis sont modifiés.
 *
 * @example
 * PATCH /api/entities/{entities}/bulk
 * { "ids":[1,2,3], "is_visible":"admin", "usable":true }
 */
class {Entity}BulkController extends Controller
{
    public function bulkUpdate(Request $request): JsonResponse
    {
        $this->authorize('updateAny', {Entity}::class);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'min:1', 'exists:{entities},id'],

            // Champs bulk (les clés absentes ne sont pas modifiées)
            'usable' => ['sometimes', 'boolean'],
            'is_visible' => ['sometimes', 'string', 'in:guest,user,game_master,admin'],
            // ... autres champs ...
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));
        if (count($ids) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Sélection invalide.',
            ], 422);
        }

        $patch = [];
        foreach ([
            'usable',
            'is_visible',
            // ... autres champs ...
        ] as $k) {
            if (array_key_exists($k, $validated)) {
                $patch[$k] = $validated[$k];
            }
        }

        if (empty($patch)) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun champ à mettre à jour.',
            ], 422);
        }

        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            $models = {Entity}::query()->whereIn('id', $ids)->get();

            foreach ($ids as $id) {
                $model = $models->firstWhere('id', $id);
                if (!$model) {
                    $errors[] = ['id' => $id, 'error' => 'Not found'];
                    continue;
                }

                try {
                    $this->authorize('update', $model);
                    foreach ($patch as $k => $v) {
                        $model->{$k} = $v;
                    }
                    $model->save();
                    $updated++;
                } catch (\Throwable $e) {
                    $errors[] = ['id' => $id, 'error' => $e->getMessage()];
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour en masse.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => count($errors) === 0,
            'summary' => [
                'requested' => count($ids),
                'updated' => $updated,
                'errors' => count($errors),
            ],
            'errors' => $errors,
        ]);
    }
}
```

### Ajouter la route

**Fichier** : `routes/api.php`

```php
Route::middleware(['web', 'auth'])->prefix('entities')->group(function () {
    // ... autres routes ...
    Route::patch('/{entities}/bulk', [App\Http\Controllers\Api\{Entity}BulkController::class, 'bulkUpdate'])
        ->name('api.entities.{entities}.bulk');
});
```

### Créer la Policy (si nécessaire)

**Fichier** : `app/Policies/Entity/{Entity}Policy.php`

```php
<?php

namespace App\Policies\Entity;

use App\Models\Entity\{Entity};
use App\Policies\Entity\BaseEntityPolicy;

/**
 * Policy d'autorisation pour l'entité {Entity}.
 */
class {Entity}Policy extends BaseEntityPolicy
{
    // Toutes les méthodes sont héritées de BaseEntityPolicy
    // Si nécessaire, surcharger updateAny() :
    // public function updateAny(User $user): bool
    // {
    //     return $user->isAdmin();
    // }
}
```

---

## 5. Bonnes pratiques

### Structure des descriptors

1. **Ordre des champs** : Organiser logiquement (id, name, champs métier, relations, métadonnées)
2. **Groupes** : Utiliser `edit.form.group` pour regrouper les champs dans le quick edit
3. **Vues** : Définir `quickEdit`, `compact`, `extended` dans `{ENTITY}_VIEW_FIELDS`
4. **Icônes** : Utiliser Font Awesome (format `fa-solid fa-*` ou `fa-regular fa-*`)

### Sécurité

1. **Backend = vérité** : Les descriptors sont uniquement pour l'UX, le backend valide et autorise
2. **Permissions** : Utiliser `visibleIf` et `editableIf` pour l'UX, mais le backend doit aussi vérifier
3. **Validation** : Toujours valider côté backend (Laravel FormRequest ou validation dans le contrôleur)

### Performance

1. **Lazy loading** : Les descriptors sont chargés à la demande
2. **Cache** : Les cellules générées peuvent être mises en cache (à implémenter si nécessaire)
3. **Optimisation** : Éviter les calculs lourds dans les adapters

### Cohérence

1. **Nommage** : Suivre les conventions (kebab-case pour les fichiers, camelCase pour les fonctions)
2. **Patterns** : Réutiliser les patterns existants (voir `resource-descriptors.js` comme référence)
3. **Tests** : Toujours créer des tests pour les nouveaux contrôleurs et adapters

---

## 6. Checklist de migration

### Pour une nouvelle entité

- [ ] Créer `{entity}-descriptors.js` avec tous les champs
- [ ] Créer `{entity}-adapter.js` avec la logique de génération de cellules
- [ ] Mettre à jour `TableController` pour supporter `?format=entities`
- [ ] Mettre à jour `Index.vue` pour utiliser le nouveau système
- [ ] Mettre à jour `entity-registry.js` pour enregistrer la nouvelle entité
- [ ] Créer `{Entity}BulkController.php` (si nécessaire)
- [ ] Ajouter la route bulk dans `routes/api.php`
- [ ] Créer/mettre à jour la Policy (si nécessaire)
- [ ] Créer les tests (BulkControllerTest, TableControllerTest, adapter.test.js)

### Pour ajouter un champ à une entité existante

- [ ] Ajouter le champ dans le descriptor
- [ ] Ajouter le champ dans `{ENTITY}_VIEW_FIELDS` si nécessaire
- [ ] Mettre à jour l'adapter si le champ nécessite un rendu spécial
- [ ] Mettre à jour le BulkController si le champ doit être éditable en bulk
- [ ] Mettre à jour les tests

---

## 📚 Ressources

- [ENTITY_FIELD_DESCRIPTORS.md](./ENTITY_FIELD_DESCRIPTORS.md) — Architecture complète
- [ENTITY_DESCRIPTORS_MIGRATION_COMPLETE.md](../100-%20Done/ENTITY_DESCRIPTORS_MIGRATION_COMPLETE.md) — Détails de la migration
- [TESTS_ENTITY_DESCRIPTORS_IMPLEMENTATION.md](../100-%20Done/TESTS_ENTITY_DESCRIPTORS_IMPLEMENTATION.md) — Guide des tests

---

## 💡 Exemples de référence

- **Descriptor simple** : `resource-descriptors.js`
- **Descriptor avec relations** : `npc-descriptors.js` (classe_id, specialization_id)
- **Adapter simple** : `resource-adapter.js`
- **Adapter avec relations** : `monster-adapter.js` (creature)
- **BulkController simple** : `AttributeBulkController.php`
- **BulkController avec relations** : `ResourceBulkController.php` (resource_type_id)

